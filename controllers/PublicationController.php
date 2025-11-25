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

            if (empty($errors)) {
                $this->publications->create($clean['forum_id'], $clean['author'], $clean['content']);
                header("Location: index.php?action=publications&forum_id=" . $clean['forum_id']);
                exit;
            }

            $old = $clean;
        }

        $this->render('publicationAdd.php', compact('forum', 'errors', 'old'));
    }

    public function deleteFront(int $id, int $forum_id): void
    {
        if ($id > 0) {
            $this->publications->delete($id);
        }

        if ($forum_id > 0) {
            header("Location: index.php?action=publications&forum_id=" . $forum_id);
        } else {
            header("Location: index.php?action=forums");
        }
        exit;
    }
}
