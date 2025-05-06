<?php
require_once '../config/jwt_config.php';

function authenticate_request() {
    // Mengambil header Authorization
    $headers = getallheaders();
    
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["message" => "Token tidak ditemukan."]);
        exit;
    }

    $jwt = str_replace('Bearer ', '', $headers['Authorization']);
    $decoded = validate_jwt($jwt);

    if ($decoded === null) {
        http_response_code(401);
        echo json_encode(["message" => "Token tidak valid"]);
        exit;
    }

    return $decoded;
}
?>
