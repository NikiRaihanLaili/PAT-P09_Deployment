<?php
require_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$secret_key = "SecretKeySangatRahasia123456"; 

function generate_jwt($user_id) {
    global $secret_key;

    $issued_at = time();
    $expiration_time = $issued_at + 3600;  // Token expired in 1 hour
    $payload = array(
        "iat" => $issued_at,
        "exp" => $expiration_time,
        "data" => array(
            "user_id" => $user_id
        )
    );

    return JWT::encode($payload, $secret_key, 'HS256');
}

function validate_jwt($jwt) {
    global $secret_key;
    try {
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        return null;
    }
}
?>
