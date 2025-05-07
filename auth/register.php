<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';

// Ambil input JSON
$data = json_decode(file_get_contents("php://input"));

// Validasi input
if (empty($data->name) || empty($data->email) || empty($data->password) || empty($data->role)) {
    http_response_code(400);
    echo json_encode(["message" => "Semua field (name, email, password, role) wajib diisi."]);
    exit;
}

// Ambil data
$name = htmlspecialchars(strip_tags($data->name));
$email = htmlspecialchars(strip_tags($data->email));
$password = $data->password;
$role = strtolower($data->role);

// Validasi role
if (!in_array($role, ['admin', 'user'])) {
    http_response_code(400);
    echo json_encode(["message" => "Role harus bernilai 'admin' atau 'user'."]);
    exit;
}

// Koneksi DB
$database = new Database();
$db = $database->getConnection();

// Cek apakah email sudah digunakan
$checkQuery = "SELECT id FROM users WHERE email = :email";
$stmt = $db->prepare($checkQuery);
$stmt->bindParam(":email", $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(["message" => "Email sudah terdaftar."]);
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Simpan user baru
$insertQuery = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
$stmt = $db->prepare($insertQuery);
$stmt->bindParam(":name", $name);
$stmt->bindParam(":email", $email);
$stmt->bindParam(":password", $hashedPassword);
$stmt->bindParam(":role", $role);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode(["message" => "Registrasi berhasil."]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Gagal menyimpan data user."]);
}
?>
