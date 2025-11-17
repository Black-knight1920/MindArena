<?php

class Publication {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function count() {
        return $this->pdo->query("SELECT COUNT(*) AS c FROM publications")
                         ->fetch()['c'];
    }

    public function getAll() {
        return $this->pdo->query("
            SELECT p.*, f.title AS forum_title
            FROM publications p
            LEFT JOIN forums f ON f.id = p.forum_id
            ORDER BY p.id DESC
        ")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM publications WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /* ⭐ REQUIRED BY FRONT PublicationController::listFront() */
    public function getByForum($forum_id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, f.title AS forum_title
            FROM publications p
            LEFT JOIN forums f ON f.id = p.forum_id
            WHERE p.forum_id = ?
            ORDER BY p.id DESC
        ");
        $stmt->execute([$forum_id]);
        return $stmt->fetchAll();
    }

    public function create($forum_id, $author, $content) {
        $stmt = $this->pdo->prepare("
            INSERT INTO publications (forum_id, author, content)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$forum_id, $author, $content]);
    }

    public function update($id, $forum_id, $author, $content) {
        $stmt = $this->pdo->prepare("
            UPDATE publications
            SET forum_id=?, author=?, content=?
            WHERE id=?
        ");
        return $stmt->execute([$forum_id, $author, $content, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM publications WHERE id=?");
        return $stmt->execute([$id]);
    }
}
