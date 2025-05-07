<?php 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../middleware/auth.php";
require_once '../config/database.php';

// Verifikasi token dan ambil role
$decoded_token = authenticate_request(); 

// Hanya admin yang bisa tambah movie
if ($decoded_token->data->role !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Akses ditolak. Hanya admin yang dapat menambahkan film."]);
    exit;
}

// Ambil ID dari URL (misal: ?id=3)
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$movie_id) {
    http_response_code(400);
    echo json_encode(["message" => "ID film tidak ditemukan di URL."]);
    exit;
}

// Ambil data JSON dari body
$data = json_decode(file_get_contents("php://input"));

// Validasi input
if (
    !empty($data->title) &&
    !empty($data->genre) &&
    !empty($data->release_year) &&
    !empty($data->director) &&
    isset($data->rating)
) {
    // Sanitasi data
    $title = htmlspecialchars(strip_tags($data->title));
    $genre = htmlspecialchars(strip_tags($data->genre));
    $release_year = (int) $data->release_year;
    $director = htmlspecialchars(strip_tags($data->director));
    $rating = number_format((float)$data->rating, 1, '.', '');

    // Koneksi DB
    $database = new Database();
    $conn = $database->getConnection();

    // Query update
    $query = "UPDATE movies SET title = :title, genre = :genre, release_year = :release_year, director = :director, rating = :rating WHERE id = :id";
    $stmt = $conn->prepare($query);

    // Bind parameter
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':release_year', $release_year, PDO::PARAM_INT);
    $stmt->bindParam(':director', $director);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':id', $movie_id, PDO::PARAM_INT);

    // Eksekusi
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "Data film berhasil diperbarui."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Gagal memperbarui data film."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap."]);
}
?>
