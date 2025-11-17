<?php
class Database {
    private $host = "localhost";
    private $port = "3306"; 
    private $db = "mindarena_forum";
    private $user = "root";
    private $pass = "";
    private $pdo;

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset=utf8mb4";

            $this->pdo = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

        } catch (PDOException $e) {
            die("<strong>Database connection error:</strong> " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
