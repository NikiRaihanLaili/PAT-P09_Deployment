<?php
require_once '../config/database.php';
require_once '../config/jwt_config.php';
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email === "admin@example.com" && $password === "123456") {
        $payload = [
            "iat" => time(),
            "exp" => time() + 3600,
            "data" => ["id" => 1, "email" => $email]
        ];

        $jwt = JWT::encode($payload, $_ENV['SECRET_KEY'], $_ENV['ALGORITHM']);
        echo json_encode(["token" => $jwt]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Login gagal"]);
    }
}
?>
