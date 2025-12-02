<?php
// Controller/categoriefront.php
require_once __DIR__ . '/../config/Database.php';

class CategorieFrontController {
    private $pdo;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->pdo = $this->db->getConnection();
    }

    // Récupérer toutes les catégories pour le FrontOffice
    public function getAllCategories() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY nom");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllCategories Front: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer une catégorie spécifique
    public function getCategorie($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getCategorie Front: " . $e->getMessage());
            return false;
        }
    }
}
?>