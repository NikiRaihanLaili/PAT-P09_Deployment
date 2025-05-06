<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "../middleware/auth.php";
require_once '../config/database.php';
require_once '../models/Film.php';

// Verifikasi token JWT via middleware
$decoded_token = authenticate_request();

// Koneksi database
$database = new Database();
$conn = $database->getConnection();
$film = new Film($conn);

// Ambil data JSON dari body request
$data = json_decode(file_get_contents("php://input"));

// Validasi keberadaan ID
if (!empty($data->id_film)) {
    $film->id_film = htmlspecialchars(strip_tags($data->id_film));

    if ($film->delete($film->id_film)) {
        http_response_code(200);
        echo json_encode(["message" => "Film berhasil dihapus."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Gagal menghapus film."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "ID film tidak ditemukan."]);
}
?>
