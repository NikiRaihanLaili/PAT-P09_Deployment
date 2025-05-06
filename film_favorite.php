<?php
class Film {
    // Database connection
    private $db;
    // Table name
    private $db_table = "film_favorite";

    // Properties sesuai kolom tabel
    public $id_film;
    public $judul_film;
    public $year_film;
    public $genre_film;

    public $result;

    // Konstruktor: koneksi database
    public function __construct($db){
        $this->db = $db;
    }

    // GET ALL
    public function getAllFilms(){
        $sqlQuery = "SELECT id_film, judul_film, year_film, genre_film FROM " . $this->db_table;
        $this->result = $this->db->query($sqlQuery);
        return $this->result;
    }

    // GET SINGLE
    public function getSingleFilm(){
        $sqlQuery = "SELECT id_film, judul_film, year_film, genre_film FROM " . $this->db_table . " WHERE id_film = " . $this->id_film;
        $record = $this->db->query($sqlQuery);
        $dataRow = $record->fetch_assoc();

        $this->judul_film = $dataRow['judul_film'];
        $this->year_film = $dataRow['year_film'];
        $this->genre_film = $dataRow['genre_film'];
    }

    // CREATE
    public function createFilm(){
        // Sanitasi input
        $this->judul_film = htmlspecialchars(strip_tags($this->judul_film));
        $this->year_film = htmlspecialchars(strip_tags($this->year_film));
        $this->genre_film = htmlspecialchars(strip_tags($this->genre_film));

        $sqlQuery = "INSERT INTO " . $this->db_table . " 
                    SET judul_film = '{$this->judul_film}', 
                    year_film = '{$this->year_film}', 
                    genre_film = '{$this->genre_film}'";

        $this->db->query($sqlQuery);
        return $this->db->affected_rows > 0;
    }

    // UPDATE
    public function updateFilm(){
        $this->judul_film = htmlspecialchars(strip_tags($this->judul_film));
        $this->year_film = htmlspecialchars(strip_tags($this->year_film));
        $this->genre_film = htmlspecialchars(strip_tags($this->genre_film));
        $this->id_film = htmlspecialchars(strip_tags($this->id_film));

        $sqlQuery = "UPDATE " . $this->db_table . " 
                    SET judul_film = '{$this->judul_film}', 
                    year_film = '{$this->year_film}', 
                    genre_film = '{$this->genre_film}'
                    WHERE id_film = {$this->id_film}";

        $this->db->query($sqlQuery);
        return $this->db->affected_rows > 0;
    }

    // DELETE
    public function deleteFilm(){
        $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE id_film = {$this->id_film}";
        $this->db->query($sqlQuery);
        return $this->db->affected_rows > 0;
    }
}
?>