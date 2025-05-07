<?php
require_once '../config/database.php';
require_once '../config/jwt_config.php';
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;

header("Content-Type: application/json");

// Hanya izinkan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Metode tidak diizinkan"]);
    exit;
}

// Ambil input JSON
$data = json_decode(file_get_contents("php://input"));
$email = $data->email ?? '';
$password = $data->password ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["message" => "Email dan password wajib diisi"]);
    exit;
}

// Koneksi DB
$database = new Database();
$conn = $database->getConnection();

// Cari user berdasarkan email
$query = "SELECT * FROM users WHERE email = :email LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':email', $email);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Siapkan payload token
    $payload = [
        "iat" => time(),
        "exp" => time() + 3600, // Token berlaku 1 jam
        "id" => $user['id'],
        "email" => $user['email'],
        "role" => $user['role']
    ];

    $jwt = generate_jwt($user['id'], $user['email'], $user['role']);
    echo json_encode(["token" => $jwt]);

} else {
    http_response_code(401);
    echo json_encode(["message" => "Email atau password salah"]);
}
