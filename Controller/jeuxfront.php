<?php
require_once '../Model/jeux.php';

class JeuxFrontController {
    private $jeuxModel;

    public function __construct() {
        $this->jeuxModel = new Jeux();
    }

    public function getAllJeux() {
        return $this->jeuxModel->getAllJeux();
    }

    public function getJeux($id) {
        return $this->jeuxModel->getJeuxById($id);
    }

    public function getJeuxByCategorie($categorie_id) {
        $allJeux = $this->getAllJeux();
        return array_filter($allJeux, function($jeu) use ($categorie_id) {
            return $jeu['categorie_id'] == $categorie_id;
        });
    }
}
?>