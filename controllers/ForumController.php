<?php

class ForumController
{
    private PDO $pdo;
    private Forum $forumModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        require_once __DIR__ . '/../models/Forum.php';
        $this->forumModel = new Forum($pdo);
    }

    /**
     * Récupère tous les forums pour le front.
     * Utilise getAllFront() si la méthode existe, sinon getAll().
     */
    private function getAllForumsForFront(): array
    {
        if (method_exists($this->forumModel, 'getAllFront')) {
            return $this->forumModel->getAllFront();
        }

        if (method_exists($this->forumModel, 'getAll')) {
            return $this->forumModel->getAll();
        }

        // Sécurité : si aucune méthode n’existe, renvoyer un tableau vide
        return [];
    }

    /* =========================================================
       PAGE ACCUEIL (home) – hero + grid de forums
    ========================================================= */
    public function home(): void
    {
        $forums = $this->getAllForumsForFront();

        // Vue home.php (celle que tu as envoyée)
        include __DIR__ . '/../views/front/home.php';
    }

    /* =========================================================
       LISTE DES FORUMS (page "Forums")
    ========================================================= */
    public function listFront(): void
    {
        $forums = $this->getAllForumsForFront();

        // Vue forumList.php (style carte, liste uniquement)
        include __DIR__ . '/../views/front/forumList.php';
    }

    /* =========================================================
       CRÉATION DE FORUM CÔTÉ FRONT (page "New Forum")
    ========================================================= */
    public function addFront(): void
    {
        $errors = [];
        $title = '';
        $description = '';
        $createdBy = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title       = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $createdBy   = trim($_POST['created_by'] ?? '');

            // Petites règles de validation
            if ($title === '') {
                $errors['title'] = 'Le titre est obligatoire.';
            } elseif (mb_strlen($title) < 3 || mb_strlen($title) > 80) {
                $errors['title'] = 'Le titre doit contenir entre 3 et 80 caractères.';
            }

            if ($createdBy === '') {
                // si tu veux que ce soit optionnel, on met une valeur par défaut
                $createdBy = 'Invité';
            }

            if (empty($errors)) {
                // Assure-toi que Forum::create($title, $description, $createdBy) existe
                $this->forumModel->create($title, $description, $createdBy);

                header('Location: index.php?action=forums');
                exit;
            }
        }

        // On passe $errors, $title, $description, $createdBy à la vue
        include __DIR__ . '/../views/front/forumAdd.php';
    }

    /* =========================================================
       SUPPRESSION D’UN FORUM CÔTÉ FRONT (optionnel)
    ========================================================= */
    public function deleteFront(): void
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            die('ID de forum invalide.');
        }

        $id = (int) $_GET['id'];

        if (method_exists($this->forumModel, 'delete')) {
            $this->forumModel->delete($id);
        }

        // Rediriger vers la page d'où on vient (forums ou home)
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'action=forums') !== false) {
            header('Location: index.php?action=forums');
        } else {
            header('Location: index.php?action=home');
        }
        exit;
    }
}
