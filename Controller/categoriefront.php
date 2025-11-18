<?php
require_once '../Model/categorie.php';

class CategorieFrontController {
    private $categorieModel;

    public function __construct() {
        $this->categorieModel = new Categorie();
    }

    public function getAllCategories() {
        return $this->categorieModel->getAllCategories();
    }

    public function getCategorie($id) {
        return $this->categorieModel->getCategorieById($id);
    }
}
?>