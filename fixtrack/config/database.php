
<?php

class Database {

    private $host = "localhost";
    private $dbname = "fixtrack_db";
    private $username = "root";
    private $password = "";

    public function connect() {

        try {

            $pdo = new PDO(
                "mysql:host=" . $this->host .
                ";dbname=" . $this->dbname .
                ";charset=utf8mb4",
                $this->username,
                $this->password
            );

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $pdo->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            return $pdo;

        } catch (PDOException $e) {

            die("Koneksi database gagal: " . $e->getMessage());

        }
    }
}
