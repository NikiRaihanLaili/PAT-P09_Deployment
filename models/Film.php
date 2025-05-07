<?php
class Film {
    private $conn;
    private $table_name = "movies";
    public $id;
    public $title;
    public $genre;
    public $release_year;
    public $director;
    public $rating;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY release_year DESC";
        return $this->conn->query($query);
    }

    public function readSingle($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (title, genre, release_year, director, rating)
                    VALUES (:title, :genre, :release_year, :director, :rating)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':genre', $this->genre);
        $stmt->bindParam(':release_year', $this->release_year, PDO::PARAM_INT);
        $stmt->bindParam(':director', $this->director);
        $stmt->bindParam(':rating', $this->rating);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE movies SET title=:title, genre=:genre, release_year=:release_year, director=:director, rating=:rating WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":release_year", $this->release_year);
        $stmt->bindParam(":director", $this->director);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":id", $this->id);
    
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
