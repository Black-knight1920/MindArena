<?php
require_once __DIR__ . "/../Model/jeu.php";

class JeuxBackController {
    private $jeuModel;

    public function __construct() {
        $this->jeuModel = new Jeu();
    }

    public function getAllJeux() {
        return $this->jeuModel->getAll();
    }

    public function deleteJeu($id) {
        return $this->jeuModel->delete($id);
    }
}

// Gestion des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $controller = new JeuxBackController();
    $id = intval($_POST['id']);
    $controller->deleteJeu($id);
    header("Location: ../View/BackOffice/back.php?success=Jeu supprimé");
    exit;
    
}
