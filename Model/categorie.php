<?php
class Categorie {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO("mysql:host=localhost;dbname=crud_jeux;charset=utf8", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Récupère toutes les catégories
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY id DESC"); // <-- categories
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vérifie si une catégorie existe déjà
    public function existe($nom) {
        $query = $this->pdo->prepare("SELECT COUNT(*) FROM categories WHERE nom = ?");
        $query->execute([$nom]);
        return $query->fetchColumn() > 0;
    }

    // Ajoute une catégorie
    public function ajouter($nom, $description) {
        $query = $this->pdo->prepare("INSERT INTO categories (nom, description) VALUES (?, ?)");
        return $query->execute([$nom, $description]);
    }

    // Supprime une catégorie
    public function delete($id) {
        $query = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $query->execute([$id]);
    }

    // Met à jour une catégorie
    public function update($id, $nom, $description) {
        $query = $this->pdo->prepare("UPDATE categories SET nom = ?, description = ? WHERE id = ?");
        return $query->execute([$nom, $description, $id]);
    }
}
