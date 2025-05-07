<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Verifikasi token via middleware
require_once "../middleware/auth.php";
$decoded_token = authenticate_request(); // Validasi token dan ambil data pengguna

// Ambil user_id dari token
$user_id_from_token = $decoded_token->data->user_id;

// Ambil data input
$data = json_decode(file_get_contents("php://input"));

if (empty($data->movie_id)) {
    http_response_code(400);
    echo json_encode(["message" => "ID film harus diisi."]);
    exit;
}

$movie_id = $data->movie_id;

// Inisialisasi DB dan model
require_once '../config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Periksa apakah film ada dalam daftar favorit user
$query = "SELECT * FROM favorite WHERE users_id = :user_id AND movies_id = :movie_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":user_id", $user_id_from_token);
$stmt->bindParam(":movie_id", $movie_id);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    http_response_code(400);
    echo json_encode(["message" => "Film tidak ada dalam daftar favorit."]);
    exit;
}

// Menghapus film dari daftar favorit user
$query = "DELETE FROM favorite WHERE users_id = :user_id AND movies_id = :movie_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":user_id", $user_id_from_token);
$stmt->bindParam(":movie_id", $movie_id);
if ($stmt->execute()) {
    echo json_encode(["message" => "Film berhasil dihapus dari favorit."]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Gagal menghapus film dari favorit."]);
}
?>
