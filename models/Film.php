<?php
class Film {
    private $conn;
    private $table_name = "film_favorite";

    public $id_film;
    public $judul_film;
    public $year_film;
    public $genre_film;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($id_film = null, $judul_film = null, $year_film = null) {
        $query = "SELECT id_film, judul_film, year_film, genre_film FROM " . $this->table_name;
        $conditions = [];
        if ($id_film) {
            $conditions[] = "id_film = :id_film";
        }
        if ($judul_film) {
            $conditions[] = "judul_film LIKE :judul_film";
        }
        if ($year_film) {
            $conditions[] = "year_film = :year_film";
        }
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Menambahkan pengurutan
        $query .= " ORDER BY year_film DESC";

        $stmt = $this->conn->prepare($query);

        // Bind parameter jika diperlukan
        if ($id_film) {
            $stmt->bindParam(":id_film", $id_film);
        }
        if ($judul_film) {
            $judul_film = "%" . $judul_film . "%"; // Untuk LIKE query
            $stmt->bindParam(":judul_film", $judul_film);
        }
        if ($year_film) {
            $stmt->bindParam(":year_film", $year_film);
        }

        // Eksekusi query
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (judul_film, year_film, genre_film) VALUES (:judul_film, :year_film, :genre_film)";
        $stmt = $this->conn->prepare($query);
    
        // Bind parameter
        $stmt->bindParam(':judul_film', $this->judul_film);
        $stmt->bindParam(':year_film', $this->year_film);
        $stmt->bindParam(':genre_film', $this->genre_film);
    
        // Eksekusi
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_film = :id_film";
        $stmt = $this->conn->prepare($query);
    
        // Binding parameter
        $stmt->bindParam(':id_film', $id);
    
        return $stmt->execute();
    }
    
    public function update($id, $judul, $tahun, $genre) {
        $query = "UPDATE " . $this->table_name . " 
                    SET judul_film = :judul_film, year_film = :year_film, genre_film = :genre_film 
                    WHERE id_film = :id_film";
        $stmt = $this->conn->prepare($query);
    
        // Bind parameter
        $stmt->bindParam(':judul_film', $judul);
        $stmt->bindParam(':year_film', $tahun);
        $stmt->bindParam(':genre_film', $genre);
        $stmt->bindParam(':id_film', $id);
    
        return $stmt->execute();
    }
    
    
}
?>
