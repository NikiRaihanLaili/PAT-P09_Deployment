<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../models/Film.php';
require_once '../middleware/auth.php';

/// Verifikasi token
$decoded_token = authenticate_request(); 

// Hanya admin yang bisa tambah movie
if ($decoded_token->data->role !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Akses ditolak. Hanya admin yang dapat menambahkan film."]);
    exit;
}

// Koneksi DB
$database = new Database();
$conn = $database->getConnection();
$film = new Film($conn);

// Ambil data dari body JSON
$data = json_decode(file_get_contents("php://input"));

// Validasi input
if (
    !empty($data->title) &&
    !empty($data->genre) &&
    !empty($data->release_year) &&
    !empty($data->director) &&
    isset($data->rating)
) {
    $film->title = htmlspecialchars(strip_tags($data->title));
    $film->genre = htmlspecialchars(strip_tags($data->genre));
    $film->release_year = htmlspecialchars(strip_tags($data->release_year));
    $film->director = htmlspecialchars(strip_tags($data->director));
    $film->rating = htmlspecialchars(strip_tags($data->rating));

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
