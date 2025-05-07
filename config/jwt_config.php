<?php
require_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$_ENV['SECRET_KEY'] = 'SecretKeySangatRahasia123456'; // Kunci JWT
$_ENV['ALGORITHM'] = 'HS256';

// Fungsi untuk membuat token baru
function generate_jwt($user_id, $email, $role) {
    $issued_at = time();
    $expiration_time = $issued_at + 3600;

    $payload = array(
        "iat" => $issued_at,
        "exp" => $expiration_time,
        "data" => array(
            "user_id" => $user_id,
            "email" => $email,
            "role" => $role
        )
    );

    return JWT::encode($payload, $_ENV['SECRET_KEY'], $_ENV['ALGORITHM']);
}


// Fungsi untuk validasi token
function validate_jwt($jwt) {
    $secret_key = $_ENV['SECRET_KEY'];
    try {
        return JWT::decode($jwt, new Key($secret_key, 'HS256'));
    } catch (Exception $e) {
        return null;
    }
}

// Fungsi untuk refresh token (jika kamu pakai)
function refresh_jwt($expired_jwt) {
    $decoded = validate_jwt($expired_jwt);
    if ($decoded === null) return null;

    return generate_jwt($decoded->data->user_id, $decoded->data->email, $decoded->data->role);
}
