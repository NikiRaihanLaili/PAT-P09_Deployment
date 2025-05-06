<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../models/Film.php';
require_once '../middleware/auth.php';

// Verifikasi token
$decoded_token = authenticate_request(); 

// Koneksi DB
$database = new Database();
$conn = $database->getConnection();
$film = new Film($conn);

// Ambil data dari body JSON
$data = json_decode(file_get_contents("php://input"));

// Validasi data
if (!empty($data->judul_film) && !empty($data->year_film) && !empty($data->genre_film)) {
    $film->judul_film = htmlspecialchars(strip_tags($data->judul_film));
    $film->year_film = htmlspecialchars(strip_tags($data->year_film));
    $film->genre_film = htmlspecialchars(strip_tags($data->genre_film));

    if ($film->create()) {
        http_response_code(201);
        echo json_encode(["message" => "Film berhasil ditambahkan."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Gagal menambahkan film."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap."]);
}
?>
