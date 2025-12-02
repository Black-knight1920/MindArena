<?php

class PublicationController
{
    private Publication $publications;
    private Forum $forums;

    public function __construct(PDO $pdo)
    {
        require_once __DIR__ . '/../models/Publication.php';
        require_once __DIR__ . '/../models/Forum.php';

        $this->publications = new Publication($pdo);
        $this->forums       = new Forum($pdo);
    }

    private function render(string $view, array $vars = []): void
    {
        extract($vars);
        include __DIR__ . '/../views/front/' . $view;
    }

    private function clean(string $v): string
    {
        return trim($v);
    }

    private function validatePublication(array $data): array
    {
        $errors = [];
        $clean  = [];

        $clean['forum_id'] = (int)($data['forum_id'] ?? 0);
        $clean['author']   = $this->clean($data['author'] ?? '');
        $clean['content']  = $this->clean($data['content'] ?? '');

        if ($clean['forum_id'] <= 0) {
            $errors[] = "Forum invalide.";
        }

        if ($clean['author'] === '') {
            $errors[] = "L'auteur est obligatoire.";
        } elseif (mb_strlen($clean['author']) < 3) {
            $errors[] = "Le nom de l'auteur doit contenir au moins 3 caractères.";
        }

        if ($clean['content'] === '') {
            $errors[] = "Le contenu est obligatoire.";
        } elseif (mb_strlen($clean['content']) < 5) {
            $errors[] = "Le contenu doit contenir au moins 5 caractères.";
        }

        return [$clean, $errors];
    }

    /* ---------- Actions FRONT ---------- */

    public function listFront(): void
    {
        $forum_id = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;
        if ($forum_id <= 0) {
            die("Forum invalide.");
        }

        $forum = $this->forums->getById($forum_id);
        if (!$forum) {
            die("Forum introuvable.");
        }

        $publications = $this->publications->getByForum($forum_id);

        $this->render('publicationList.php', compact('forum', 'publications'));
    }

    public function addFront(): void
    {
        $forum_id = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;
        if ($forum_id <= 0) {
            die("Forum invalide.");
        }

        $forum = $this->forums->getById($forum_id);
        if (!$forum) {
            die("Forum introuvable.");
        }

        $errors = [];
        $old    = ['author' => '', 'content' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            [$clean, $errors] = $this->validatePublication($_POST);

            // sanitize a bit (inline replacement for helper)
            $clean['author'] = strtolower(preg_replace('/\s+/', ' ', trim($clean['author'])));
            $clean['content'] = trim(strip_tags($clean['content']));

            if (empty($errors)) {
                $this->publications->create($clean['forum_id'], $clean['author'], $clean['content']);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Publication créée.'];
                header("Location: index.php?action=publications&forum_id=" . $clean['forum_id']);
                exit;
            }

            $old = $clean;
        }

        $this->render('publicationAdd.php', compact('forum', 'errors', 'old'));
    }

    /* =========================================================
       ÉDITION D'UNE PUBLICATION CÔTÉ FRONT
    ========================================================= */
    public function editFront(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $forum_id = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;

        if ($id <= 0 || $forum_id <= 0) {
            header('Location: index.php?action=forums');
            exit;
        }

        $forum = $this->forums->getById($forum_id);
        if (!$forum) {
            header('Location: index.php?action=forums');
            exit;
        }

        $publication = $this->publications->getById($id);
        if (!$publication) {
            header('Location: index.php?action=publications&forum_id=' . $forum_id);
            exit;
        }

        $errors = [];
        $old    = ['author' => $publication['author'] ?? '', 'content' => $publication['content'] ?? ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify the user is the author
            $creator_name = strtolower(preg_replace('/\s+/', ' ', trim($_POST['creator_name'] ?? '')));
            $publication_author = strtolower(preg_replace('/\s+/', ' ', trim($publication['author'])));

            if ($creator_name !== $publication_author) {
                $errors['creator_name'] = 'Nom incorrect. Tu dois être l\'auteur pour modifier cette publication.';
            }

            if (empty($errors)) {
                [$clean, $validation_errors] = $this->validatePublication($_POST);
                $clean['author'] = strtolower(preg_replace('/\\s+/', ' ', trim($clean['author'])));
                $clean['content'] = trim(strip_tags($clean['content']));

                if (!empty($validation_errors)) {
                    $errors = array_merge($errors, $validation_errors);
                } else {
                    $this->publications->update($id, $clean['forum_id'], $clean['author'], $clean['content']);
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Publication mise à jour.'];
                    header('Location: index.php?action=publications&forum_id=' . $forum_id);
                    exit;
                }
            }

            [$clean, $validation_errors] = $this->validatePublication($_POST);
            $old = $clean;
        }

        $this->render('publicationEdit.php', compact('forum', 'publication', 'errors', 'old'));
    }

    /* =========================================================
       PAGE DE CONFIRMATION DE SUPPRESSION D'UNE PUBLICATION
    ========================================================= */
    public function deleteConfirmFront(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $forum_id = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;

        if ($id <= 0 || $forum_id <= 0) {
            header('Location: index.php?action=forums');
            exit;
        }

        $forum = $this->forums->getById($forum_id);
        if (!$forum) {
            header('Location: index.php?action=forums');
            exit;
        }

        $publication = $this->publications->getById($id);
        if (!$publication) {
            header('Location: index.php?action=publications&forum_id=' . $forum_id);
            exit;
        }

        $errors = [];
        $this->render('publicationDeleteConfirm.php', compact('forum', 'publication', 'errors'));
    }

    public function deleteFront(int $id, int $forum_id): void
    {
        if ($id <= 0 || $forum_id <= 0) {
            header('Location: index.php?action=forums');
            exit;
        }

        $publication = $this->publications->getById($id);
        if (!$publication) {
            if ($forum_id > 0) {
                header("Location: index.php?action=publications&forum_id=" . $forum_id);
            } else {
                header("Location: index.php?action=forums");
            }
            exit;
        }

        // Verify creator authorization
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $creator_name = strtolower(preg_replace('/\s+/', ' ', trim($_POST['creator_name'] ?? '')));
            $publication_author = strtolower(preg_replace('/\s+/', ' ', trim($publication['author'])));

            if ($creator_name === $publication_author) {
                $this->publications->delete($id);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Publication supprimée.'];
                if ($forum_id > 0) {
                    header("Location: index.php?action=publications&forum_id=" . $forum_id);
                } else {
                    header("Location: index.php?action=forums");
                }
                exit;
            }
        }

        // If not POST or authorization failed, show error
        $forum = $this->forums->getById($forum_id);
        $errors = ['creator_name' => 'Nom incorrect. Tu dois être l\'auteur pour supprimer cette publication.'];
        $this->render('publicationDeleteConfirm.php', compact('forum', 'publication', 'errors'));
    }
}
