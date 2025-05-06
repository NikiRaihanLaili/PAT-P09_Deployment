<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Favorit</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Daftar Film Favorit</h1>

    <!-- Tampilan setelah login -->
    <div id="authSection" style="display: none;">
        <button id="logoutButton">Logout</button>
        <h2>Daftar Film</h2>
        <table id="filmTable">
            <thead>
                <tr>
                    <th>Judul Film</th>
                    <th>Tahun</th>
                    <th>Genre</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <h2>Tambah Film</h2>
        <form id="addFilmForm">
            <input type="text" id="judul_film" placeholder="Judul Film" required>
            <input type="number" id="year_film" placeholder="Tahun" required>
            <input type="text" id="genre_film" placeholder="Genre" required>
            <button type="submit">Tambah Film</button>
        </form>
    </div>

    <!-- Tampilan sebelum login -->
    <div id="loginSection" style="display: none;">
        <a href="login.php">Login</a> | 
        <a href="register.php">Register</a>
    </div>

    <script src="config.js"></script>
    <script src="app.js"></script>
</body>
</html>
