<?php
class Database {
    private $host = "localhost";
    private $db_name = "niki-vercel-api";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Koneksi ke database gagal.",
                "error" => $e->getMessage()
            ]);
            exit;
        }

        return $this->conn;
    }
}
