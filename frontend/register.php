<?php
require 'vendor/autoload.php';

$pdo = new PDO("mysql:host=localhost;dbname=testing", "root", "password");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$name || !$email || !$password) {
        $error = 'Semua field wajib diisi.';
    } else {
        // Cek apakah email sudah digunakan
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar.';
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Simpan user baru
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword, 'user']);

            $success = 'Registrasi berhasil. Silakan <a href="login.php" class="text-blue-600 underline">Login</a>.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Register</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-semibold text-center text-blue-600 mb-6">Belum Punya Akun Daftar Sekarang</h1>
        
        <form id="registerForm" class="space-y-4">
        <div>
            <label for="name" class="block mb-1 text-sm font-medium text-gray-700">Nama</label>
            <input type="text" id="name" placeholder="Nama"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required />
        </div>
        <div>
            <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email</label>
            <input type="text" id="email" placeholder="Email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required />
        </div>
        <div>
            <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" placeholder="Password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required />
        </div>
        <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Register
        </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">
        Sudah punya akun?
        <a href="login.php" class="text-blue-600 hover:underline">Login</a>
        </p>
    </div>

    <script src="config.js"></script>
    <script src="app.js"></script>
</body>
</html>