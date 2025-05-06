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

// Ambil parameter query dari URL
$id_film = isset($_GET['id_film']) ? $_GET['id_film'] : null;
$judul_film = isset($_GET['judul_film']) ? $_GET['judul_film'] : null;
$year_film = isset($_GET['year_film']) ? $_GET['year_film'] : null;

// Panggil metode read dengan parameter filter
$result = $film->read($id_film, $judul_film, $year_film); 
$itemCount = $result->rowCount();  // Menggunakan rowCount() untuk PDOStatement

// Respon JSON
if ($itemCount > 0) {
    $filmArr = array();
    $filmArr["body"] = array();
    $filmArr["itemCount"] = $itemCount;

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        array_push($filmArr["body"], $row);
    }

    echo json_encode($filmArr);
} else {
    http_response_code(404);
    echo json_encode([
        "message" => "Data film tidak ditemukan."
    ]);
}
?>
