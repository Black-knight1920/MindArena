<?php
require_once __DIR__ . "/../config/db.php";

class Reponse {
    private $db;

    public function __construct() {
        $this->db = DB::connect();
    }

    public function add($recId, $text) {
        $stmt = $this->db->prepare("INSERT INTO reponse(reclamation_id, response) VALUES (?,?)");
        $stmt->execute([$recId, $text]);
    }

    public function all() {
        return $this->db->query("SELECT * FROM reponse ORDER BY id DESC")->fetchAll();
    }
}
