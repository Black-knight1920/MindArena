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
            // sanitize inputs
            $title = trim(strip_tags($_POST['title'] ?? ''));
            $description = trim(strip_tags($_POST['description'] ?? ''));
            $createdBy = strtolower(preg_replace('/\s+/', ' ', trim($_POST['created_by'] ?? '')));

            if ($title === '') {
                $errors['title'] = 'Le titre est obligatoire.';
            } else {
                $err = [];
                $len = mb_strlen($title);
                if ($len < 3) $err[] = 'Le titre doit contenir au moins 3 caractères.';
                if ($len > 80) $err[] = 'Le titre ne doit pas dépasser 80 caractères.';
                if ($err) $errors['title'] = implode(' ', $err);
            }

            if ($createdBy === '') {
                $createdBy = 'Invité';
            }

            if (empty($errors)) {
                $this->forumModel->create($title, $description, $createdBy);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Forum créé.'];
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
        $forum = $this->forumModel->getById($id);

        if (!$forum) {
            header('Location: index.php?action=forums');
            exit;
        }

        // Verify creator authorization
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $creator_name = strtolower(preg_replace('/\s+/', ' ', trim($_POST['creator_name'] ?? '')));
            $forum_creator = strtolower(preg_replace('/\s+/', ' ', trim($forum['created_by'])));

            if ($creator_name === $forum_creator) {
                if (method_exists($this->forumModel, 'delete')) {
                    $this->forumModel->delete($id);
                }

                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Forum supprimé.'];
                header('Location: index.php?action=forums');
                exit;
            }
        }

        // If not POST or authorization failed, show error
        $errors = ['creator_name' => 'Nom incorrect. Tu dois être le créateur pour supprimer ce forum.'];
        include __DIR__ . '/../views/front/forumDeleteConfirm.php';
    }

    /* =========================================================
       ÉDITION D'UN FORUM CÔTÉ FRONT
    ========================================================= */
    public function editFront(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: index.php?action=forums');
            exit;
        }

        $forum = $this->forumModel->getById($id);
        if (!$forum) {
            header('Location: index.php?action=forums');
            exit;
        }

        $errors = [];
        $title = $forum['title'] ?? '';
        $description = $forum['description'] ?? '';
        $createdBy = $forum['created_by'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify the user is the creator
            $creator_name = strtolower(preg_replace('/\s+/', ' ', trim($_POST['creator_name'] ?? '')));
            $forum_creator = strtolower(preg_replace('/\s+/', ' ', trim($forum['created_by'])));

            if ($creator_name !== $forum_creator) {
                $errors['creator_name'] = 'Nom incorrect. Tu dois être le créateur pour modifier ce forum.';
            }

            $title = trim(strip_tags($_POST['title'] ?? ''));
            $description = trim(strip_tags($_POST['description'] ?? ''));
            $createdBy = strtolower(preg_replace('/\\s+/', ' ', trim($_POST['created_by'] ?? '')));

            if ($title === '') {
                $errors['title'] = 'Le titre est obligatoire.';
            } else {
                $err = [];
                $len = mb_strlen($title);
                if ($len < 3) $err[] = 'Le titre doit contenir au moins 3 caractères.';
                if ($len > 80) $err[] = 'Le titre ne doit pas dépasser 80 caractères.';
                if ($err) $errors['title'] = implode(' ', $err);
            }

            if (empty($errors)) {
                $this->forumModel->update($id, $title, $description, $createdBy);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Forum mis à jour.'];
                header('Location: index.php?action=forums');
                exit;
            }
        }

        include __DIR__ . '/../views/front/forumEdit.php';
    }

    /* =========================================================
       PAGE DE CONFIRMATION DE SUPPRESSION D'UN FORUM
    ========================================================= */
    public function deleteConfirmFront(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: index.php?action=forums');
            exit;
        }

        $forum = $this->forumModel->getById($id);
        if (!$forum) {
            header('Location: index.php?action=forums');
            exit;
        }

        $errors = [];
        include __DIR__ . '/../views/front/forumDeleteConfirm.php';
    }
}
