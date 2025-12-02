<?php
// Controller/jeuxfront.php
require_once __DIR__ . '/../config/Database.php';

class JeuxFrontController {
    private $pdo;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->pdo = $this->db->getConnection();
    }

    // Récupérer tous les jeux pour le FrontOffice
    public function getAllJeux() {
        try {
            $sql = "SELECT j.*, c.nom as categorie_nom 
                    FROM jeux j 
                    LEFT JOIN categories c ON j.categorie_id = c.id 
                    ORDER BY j.id DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllJeux Front: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer un jeu spécifique
    public function getJeu($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT j.*, c.nom as categorie_nom 
                                       FROM jeux j 
                                       LEFT JOIN categories c ON j.categorie_id = c.id 
                                       WHERE j.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getJeu Front: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer les jeux par catégorie
    public function getJeuxByCategorie($categorie_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT j.*, c.nom as categorie_nom 
                                       FROM jeux j 
                                       LEFT JOIN categories c ON j.categorie_id = c.id 
                                       WHERE j.categorie_id = ? 
                                       ORDER BY j.id DESC");
            $stmt->execute([$categorie_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getJeuxByCategorie: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer les jeux en promotion
    public function getJeuxEnPromotion() {
        try {
            $sql = "SELECT j.*, c.nom as categorie_nom 
                    FROM jeux j 
                    LEFT JOIN categories c ON j.categorie_id = c.id 
                    WHERE j.prix_promotion IS NOT NULL 
                    AND j.prix_promotion > 0 
                    ORDER BY j.id DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getJeuxEnPromotion: " . $e->getMessage());
            return [];
        }
    }
}
?>