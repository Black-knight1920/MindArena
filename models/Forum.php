<?php

class Forum {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function count() {
        return $this->pdo->query("SELECT COUNT(*) AS c FROM forums")
                         ->fetch()['c'];
    }

    public function getAll() {
        return $this->pdo->query("
            SELECT * FROM forums 
            ORDER BY id DESC
        ")->fetchAll();
    }

    /* ⭐ REQUIRED BY FRONT-END (ForumController::listFront) */
    public function getAllFront() {
        return $this->pdo->query("
            SELECT * FROM forums 
            ORDER BY id DESC
        ")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM forums WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($title, $description) {
        $stmt = $this->pdo->prepare("
            INSERT INTO forums (title, description)
            VALUES (?, ?)
        ");
        return $stmt->execute([$title, $description]);
    }

    public function update($id, $title, $description) {
        $stmt = $this->pdo->prepare("
            UPDATE forums 
            SET title=?, description=? 
            WHERE id=?
        ");
        return $stmt->execute([$title, $description, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM forums WHERE id=?");
        return $stmt->execute([$id]);
    }
}
