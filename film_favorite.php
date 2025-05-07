<?php
class Favorite {
    // Database connection
    private $db;
    // Table name
    private $db_table = "favorite";

    // Properties sesuai kolom tabel
    public $users_id;
    public $movies_id;

    public $result;

    // Konstruktor: koneksi database
    public function __construct($db){
        $this->db = $db;
    }

    // GET ALL FAVORITES BY USER
    public function getAllFavoritesByUser(){
        $sqlQuery = "SELECT movies.id, movies.title, movies.genre, movies.release_year, movies.director, movies.rating 
                    FROM " . $this->db_table . " 
                    JOIN movies ON favorite.movies_id = movies.id
                    WHERE favorite.users_id = " . $this->users_id;
        $this->result = $this->db->query($sqlQuery);
        return $this->result;
    }

    // ADD MOVIE TO FAVORITE
    public function addFavorite(){
        $sqlQuery = "INSERT INTO " . $this->db_table . " 
                    SET users_id = '{$this->users_id}', 
                    movies_id = '{$this->movies_id}'";

        $this->db->query($sqlQuery);
        return $this->db->affected_rows > 0;
    }

    // REMOVE MOVIE FROM FAVORITE
    public function removeFavorite(){
        $sqlQuery = "DELETE FROM " . $this->db_table . " 
                    WHERE users_id = {$this->users_id} 
                    AND movies_id = {$this->movies_id}";
        $this->db->query($sqlQuery);
        return $this->db->affected_rows > 0;
    }
}

?>