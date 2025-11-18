<?php
require_once __DIR__ . "/../Model/categorie.php";

class CategorieBackController {
    private $categorieModel;

    public function __construct() {
        $this->categorieModel = new Categorie();
    }

    public function getAllCategories() {
        return $this->categorieModel->getAll();
    }

    public function deleteCategorie($id) {
        return $this->categorieModel->delete($id);
    }

    public function createCategorie($nom, $description) {
        if (!$this->categorieModel->existe($nom)) {
            return $this->categorieModel->ajouter($nom, $description);
        }
        return false;
    }
}

// Gestion des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $controller = new CategorieBackController();
    $id = intval($_POST['id']);
    $controller->deleteCategorie($id);
    header("Location: ../View/BackOffice/back.php?success=Catégorie supprimée");
    exit;
}
