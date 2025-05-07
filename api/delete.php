<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../middleware/auth.php";
require_once '../config/database.php';

// Verifikasi token dan ambil data pengguna
$decoded_token = authenticate_request(); 

// Cek apakah token valid dan data pengguna tersedia
if (!isset($decoded_token->data)) {
    http_response_code(401);
    echo json_encode(["message" => "Token tidak valid atau rusak."]);
    exit;
}

// Ambil ID film dari URL
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$movie_id) {
    http_response_code(400);
    echo json_encode(["message" => "ID film tidak ditemukan di URL."]);
    exit;
}

// Ambil role pengguna dari token
$user_role = $decoded_token->data->role ?? null;

// Koneksi ke database
$database = new Database();
$conn = $database->getConnection();

// Jika role admin, admin bisa menghapus film dari daftar film (movies)
if ($user_role === 'admin') {
    // Hapus film dari daftar film
    $query = "DELETE FROM movies WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id", $movie_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Film berhasil dihapus dari daftar film."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Gagal menghapus film dari daftar film."]);
    }
} 
// Jika role user, user hanya bisa menghapus film dari daftar favorit mereka
elseif ($user_role === 'user') {
    // Periksa apakah film ada dalam daftar favorit user
    $query = "DELETE FROM favorite WHERE users_id = :user_id AND movies_id = :movie_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":user_id", $decoded_token->data->user_id);
    $stmt->bindParam(":movie_id", $movie_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Film berhasil dihapus dari daftar favorit."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Gagal menghapus film dari daftar favorit."]);
    }
} else {
    http_response_code(403);
    echo json_encode(["message" => "Akses ditolak. Hanya admin yang bisa menghapus film dari daftar film, user hanya bisa menghapus film dari daftar favorit."]);
}
?>
