<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Verifikasi token via middleware
require_once "../middleware/auth.php";
$decoded_token = authenticate_request(); 

// Cek apakah user adalah admin
if ($decoded_token->data->role !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Akses ditolak. Hanya admin yang dapat melihat daftar favorit pengguna lain."]);
    exit;
}

// Cek jika ada parameter id di URL (untuk melihat favorit berdasarkan user_id tertentu)
$user_id = isset($_GET['id']) ? $_GET['id'] : null;

// Koneksi ke database
require_once '../config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Jika ada parameter id, tampilkan favorit dari pengguna tersebut
if ($user_id) {
    $query = "SELECT favorite.users_id, favorite.movies_id, movies.title, movies.genre, movies.release_year, movies.director, movies.rating
              FROM favorite
              INNER JOIN movies ON favorite.movies_id = movies.id
              WHERE favorite.users_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $favorites_arr = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $favorite_item = [
                'user_id' => $users_id,
                'movie_id' => $movies_id,
                'title' => $title,
                'genre' => $genre,
                'release_year' => $release_year,
                'director' => $director,
                'rating' => $rating,
            ];
            array_push($favorites_arr, $favorite_item);
        }
        echo json_encode($favorites_arr);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Tidak ada film favorit ditemukan untuk pengguna dengan ID $user_id."]);
    }
} else {
    // Jika tidak ada parameter id, tampilkan favorit dari pengguna yang sedang login (admin hanya)
    $query = "SELECT favorite.users_id, favorite.movies_id, movies.title, movies.genre, movies.release_year, movies.director, movies.rating
                FROM favorite
                INNER JOIN movies ON favorite.movies_id = movies.id";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $favorites_arr = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $favorite_item = [
                'user_id' => $users_id,
                'movie_id' => $movies_id,
                'title' => $title,
                'genre' => $genre,
                'release_year' => $release_year,
                'director' => $director,
                'rating' => $rating,
            ];
            array_push($favorites_arr, $favorite_item);
        }
        echo json_encode($favorites_arr);
    } else {
        echo json_encode(["message" => "Tidak ada film favorit ditemukan."]);
    }
}
?>
