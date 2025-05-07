<?php
require_once '../config/jwt_config.php';

function authenticate_request() {
    // Mengambil semua header, menggunakan apache_request_headers jika getallheaders tidak tersedia
    $headers = function_exists('getallheaders') ? getallheaders() : apache_request_headers();

    // Memeriksa apakah header Authorization ada
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["message" => "Token tidak ditemukan."]);
        exit;
    }

    // Mengambil token dari header Authorization (menghapus "Bearer " di depan token)
    $jwt = str_replace('Bearer ', '', $headers['Authorization']);

    // Memvalidasi token dengan fungsi validate_jwt
    $decoded = validate_jwt($jwt);

    // Jika token tidak valid
    if ($decoded === null) {
        http_response_code(401);
        echo json_encode(["message" => "Token sudah kedaluwarsa."]);
        exit;
    }

    // Mengembalikan hasil decoding JWT yang berisi data pengguna
    return $decoded;
}
?>
