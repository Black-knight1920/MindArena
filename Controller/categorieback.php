<?php
require_once __DIR__ . "/../Model/categorie.php";  // Chemin corrigé

class CategorieBackController {
    private $categorieModel;

    public function __construct() {
        $this->categorieModel = new Categorie();
    }

    // Méthode pour ajouter une catégorie
    public function addCategorie($nom, $description) {
        // Validation des données
        if (empty($nom)) {
            return false;
        }
        
        // Vérifier si la catégorie existe déjà
        if ($this->categorieModel->existe($nom)) {
            return false; // Catégorie existe déjà
        }
        
        // Ajouter la catégorie
        return $this->categorieModel->ajouter($nom, $description);
    }

    // Méthode pour récupérer toutes les catégories
    public function getAllCategories() {
        return $this->categorieModel->getAll();
    }

    // Méthode pour récupérer une catégorie par son ID
    public function getCategorieById($id) {
        $categories = $this->categorieModel->getAll();
        foreach ($categories as $categorie) {
            if ($categorie['id'] == $id) {
                return $categorie;
            }
        }
        return null;
    }

    // Méthode pour mettre à jour une catégorie
    public function updateCategorie($id, $nom, $description) {
        if (empty($nom)) {
            return false;
        }
        return $this->categorieModel->update($id, $nom, $description);
    }

    // Méthode pour supprimer une catégorie
    public function deleteCategorie($id) {
        return $this->categorieModel->delete($id);
    }
}
?>