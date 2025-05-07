<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Verifikasi token via middleware
require_once "../middleware/auth.php";
$decoded_token = authenticate_request(); // Ini sudah cukup untuk validasi token

// Inisialisasi DB dan model
require_once '../config/database.php';
require_once '../models/Film.php';

$database = new Database();
$conn = $database->getConnection();
$film = new Film($conn);

// Ambil parameter dari query jika ada
$id = isset($_GET['moviesid']) ? intval($_GET['moviesid']) : null;

if ($id) {
    $result = $film->readSingle($id);  // Fungsi ambil 1 film
    if ($result) {
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Film tidak ditemukan."]);
    }
} else {
    $stmt = $film->readAll();  // Fungsi ambil semua film
    $itemCount = $stmt->rowCount();

    if ($itemCount > 0) {
        $films = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $films[] = $row;
        }
        echo json_encode($films);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Tidak ada data film."]);
    }
}
?>
