<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require "../config/core.php";  // Pastikan ini ada
require "../config/jwt_config.php";  // Menambahkan pemanggilan config/jwt_config.php
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"));

// Validasi user dummy (bisa diganti autentikasi DB)
if ($data->username === "admin" && $data->password === "123456") {
    // Menggunakan fungsi generate_jwt dari config/jwt_config.php
    $jwt = generate_jwt($data->username);

    http_response_code(200);
    echo json_encode([
        "message" => "Login berhasil",
        "jwt" => $jwt,
        "expireAt" => time() + 3600
    ]);
} else {
    http_response_code(401);
    echo json_encode(["message" => "Login gagal"]);
}
?>
