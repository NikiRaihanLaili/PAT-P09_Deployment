<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';
require_once '../config/jwt_config.php';
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;

// Baca data dari input JSON
$data = json_decode(file_get_contents("php://input"));

if (empty($data->email) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(["message" => "Email dan password harus diisi."]);
    exit;
}

$email = $data->email;
$password = $data->password;

// Koneksi ke database
$database = new Database();
$db = $database->getConnection();

// Ambil user berdasarkan email
$query = "SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":email", $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Deteksi apakah password di-hash atau plaintext
    $isValid = false;

    if (strlen($user['password']) >= 60 && password_verify($password, $user['password'])) {
        $isValid = true;
    } elseif ($password === $user['password']) {
        $isValid = true;
    }

    if ($isValid) {
        // Siapkan payload token
        $issued_at = time();
        $expiration_time = $issued_at + 3600;

        $payload = [
            "iat" => $issued_at,
            "exp" => $expiration_time,
            "data" => [
                "user_id" => $user['id'],
                "email" => $user['email'],
                "role" => $user['role']
            ]
        ];

        // Generate token JWT
        $jwt = JWT::encode($payload, $secret_key, 'HS256');

        echo json_encode([
            "message" => "Login berhasil",
            "token" => $jwt,
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email'],
                "role" => $user['role']
            ],
            "expires_in" => 3600
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Password salah."]);
    }
} else {
    http_response_code(404);
    echo json_encode(["message" => "User tidak ditemukan."]);
}
?>
