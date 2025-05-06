<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "../middleware/auth.php";
require_once '../config/database.php';
require_once '../models/Film.php';

// Verifikasi token JWT
$decoded_token = authenticate_request(); // aman dan langsung validasi token

// Inisialisasi koneksi database dan model
$database = new Database();
$conn = $database->getConnection();
$film = new Film($conn);

// Ambil data JSON dari request body
$data = json_decode(file_get_contents("php://input"));

// Validasi input data
if (
    isset($data->id_film) &&
    !empty($data->judul_film) &&
    !empty($data->year_film) &&
    !empty($data->genre_film)
) {
    // Sanitasi & set nilai
    $film->id_film = htmlspecialchars(strip_tags($data->id_film));
    $film->judul_film = htmlspecialchars(strip_tags($data->judul_film));
    $film->year_film = htmlspecialchars(strip_tags($data->year_film));
    $film->genre_film = htmlspecialchars(strip_tags($data->genre_film));

    // Proses update
    if ($film->update($film->id_film, $film->judul_film, $film->year_film, $film->genre_film)) {
        http_response_code(200);
        echo json_encode(["message" => "Data film berhasil diperbarui."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Gagal memperbarui data film."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap atau ID tidak ditemukan."]);
}
?>
