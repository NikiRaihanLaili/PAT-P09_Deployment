<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$pdo = new PDO("mysql:host=localhost;dbname=testing", "root", "password");
$secretKey = 'your-secret-key';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $payload = [
                'iss' => 'http://localhost',
                'iat' => time(),
                'exp' => time() + 3600,
                'data' => [
                    'user_id' => $user['id'],
                    'user_name' => $user['name'],
                    'role' => $user['role']
                ]
            ];

            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            setcookie("token", $jwt, time() + 3600, "/", "", false, true);

            header('Location: login.php');
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>


<?php if ($error): ?>
    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-semibold text-center text-blue-600 mb-6">Login Dashboard</h1>
        
        <form method="POST" action="" class="space-y-4">
    <div>
        <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email</label>
        <input type="text" id="email" name="email" placeholder="Email"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required />
    </div>
    <div>
        <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Password</label>
        <input type="password" id="password" name="password" placeholder="Password"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required />
    </div>
    <button type="submit" 
            class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        Login
    </button>
</form>


        <p class="text-center text-sm text-gray-500 mt-4">
        Belum punya akun?
        <a href="register.php" class="text-blue-600 hover:underline">Daftar</a>
        </p>
    </div>

    <script src="config.js"></script>
    <script src="app.js"></script>
</body>
</html>

