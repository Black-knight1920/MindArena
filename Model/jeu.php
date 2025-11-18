<?php
class Jeu {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO("mysql:host=localhost;dbname=crud_jeux;charset=utf8", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

public function getAll() {
    $stmt = $this->pdo->query(
        "SELECT j.*, c.nom AS categorie_nom 
         FROM jeux j 
         LEFT JOIN categories c ON j.categorie_id = c.id
         ORDER BY j.id DESC"
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function delete($id) {
        $query = $this->pdo->prepare("DELETE FROM jeux WHERE id = ?");
        return $query->execute([$id]);
    }

    public function ajouter($titre, $description, $prix, $categorie_id, $image = null) {
        $query = $this->pdo->prepare(
            "INSERT INTO jeux (titre, description, prix, categorie_id, image) 
             VALUES (?, ?, ?, ?, ?)"
        );
        return $query->execute([$titre, $description, $prix, $categorie_id, $image]);
    }
}
