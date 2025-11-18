<?php

class Reclamation
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM reclamation ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($full_name, $email, $subject, $message) {
        $stmt = $this->pdo->prepare("
            INSERT INTO reclamation(full_name, email, subject, message)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$full_name, $email, $subject, $message]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reclamation WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM reclamation WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
