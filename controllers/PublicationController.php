<?php

require_once __DIR__ . '/../Models/Publication.php';
require_once __DIR__ . '/../Models/Forum.php';
require_once __DIR__ . '/../Models/Report.php';
class PublicationController
{
    private Publication $publications;
    private Forum $forums;
    private Report $reportModel;

    public function __construct(PDO $pdo)
    {
        $this->publications = new Publication($pdo);
        $this->forums       = new Forum($pdo);
        $this->reportModel  = new Report($pdo);
    }

    private function render(string $view, array $vars = []): void
    {
        extract($vars);
        include VIEW_PATH . '/front/' . $view;
    }

    private function currentUsername(): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user']['username'] ?? null;
    }

    private function isAdmin(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return ($_SESSION['user']['role'] ?? '') === 'admin';
    }

    private function sortPublications(array $items, string $sort, string $dir): array
    {
        $direction = strtolower($dir) === 'asc' ? 1 : -1;
        $sort = in_array($sort, ['date', 'title', 'author'], true) ? $sort : 'date';

        usort($items, function ($a, $b) use ($sort, $direction) {
            switch ($sort) {
                case 'title':
                    $aval = strtolower($a['title'] ?? '');
                    $bval = strtolower($b['title'] ?? '');
                    break;
                case 'author':
                    $aval = strtolower($a['author'] ?? '');
                    $bval = strtolower($b['author'] ?? '');
                    break;
                case 'date':
                default:
                    $aval = strtotime($a['created_at'] ?? $a['date'] ?? 'now');
                    $bval = strtotime($b['created_at'] ?? $b['date'] ?? 'now');
                    break;
            }
            if ($aval == $bval) return 0;
            return ($aval < $bval ? -1 : 1) * $direction;
        });
        return $items;
    }

    private function validatePublication(array $data): array
    {
        $errors = [];
        $clean  = [];

        $clean['forum_id'] = (int)($data['forum_id'] ?? 0);
        $clean['author']   = trim($data['author'] ?? '');
        $clean['content']  = trim($data['content'] ?? '');

        if ($clean['forum_id'] <= 0) {
            $errors[] = "Forum invalide.";
        }

        if ($clean['author'] === '') {
            $errors[] = "L'auteur est obligatoire.";
        } elseif (mb_strlen($clean['author']) < 3) {
            $errors[] = "Le nom de l'auteur doit contenir au moins 3 caracteres.";
        }

        if ($clean['content'] === '') {
            $errors[] = "Le contenu est obligatoire.";
        } elseif (mb_strlen($clean['content']) < 5) {
            $errors[] = "Le contenu doit contenir au moins 5 caracteres.";
        }

        return [$clean, $errors];
    }

    private function ensureOwner(array $publication): void
    {
        $currentUser = $this->currentUsername();
        if (!$currentUser || strcasecmp($currentUser, (string)($publication['author'] ?? '')) !== 0) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['_flash'][] = ['type' => 'error', 'message' => 'Action non autorisAc.'];
            header('Location: index.php?action=publications&forum_id=' . (int)($publication['forum_id'] ?? 0));
            exit;
        }
    }

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
        foreach ($publications as &$p) {
            $p['report_count'] = $this->reportModel->countByTarget('publication', (int)($p['id'] ?? 0));
        }
        unset($p);

        $sort = isset($_GET['sort']) ? strtolower($_GET['sort']) : 'date';
        $dir  = isset($_GET['dir']) ? strtolower($_GET['dir']) : 'desc';
        $publications = $this->sortPublications($publications, $sort, $dir);

        $currentUser = $this->currentUsername();
        $isAdmin = $this->isAdmin();

        $this->render('publicationList.php', compact('forum', 'publications', 'currentUser', 'isAdmin', 'sort', 'dir'));
    }

    public function addFront(): void
    {
        $currentUser = $this->currentUsername();
        if (!$currentUser) {
            header('Location: login.php');
            exit;
        }

        $forum_id = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;
        if ($forum_id <= 0) {
            die("Forum invalide.");
        }

        $forum = $this->forums->getById($forum_id);
        if (!$forum) {
            die("Forum introuvable.");
        }

        $errors = [];
        $old    = ['author' => $currentUser, 'content' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST['author'] = $currentUser;
            $_POST['forum_id'] = $forum_id;

            [$clean, $errorsFromValidation] = $this->validatePublication($_POST);
            $errors = array_merge($errors, $errorsFromValidation);

            $clean['author'] = strtolower(preg_replace('/\s+/', ' ', trim($clean['author'])));
            $clean['content'] = trim(strip_tags($clean['content']));

            if (empty($errors)) {
                $this->publications->create($clean['forum_id'], $clean['author'], $clean['content']);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Publication crAcAce.'];
                header("Location: index.php?action=publications&forum_id=" . $clean['forum_id']);
                exit;
            }

            $old = $clean;
        }

        $currentUser = $currentUser; // already set above
        $this->render('publicationAdd.php', compact('forum', 'errors', 'old', 'currentUser'));
    }

    public function editFront(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: index.php?action=forums');
            exit;
        }

        $publication = $this->publications->getById($id);
        if (!$publication) {
            header('Location: index.php?action=forums');
            exit;
        }

        $forum = $this->forums->getById((int)($publication['forum_id'] ?? 0));

        // Admin -> panneau admin seulement si signale
        if ($this->isAdmin()) {
            $reports = $this->reportModel->countByTarget('publication', $id);
            if ($reports > 0) {
                header('Location: admin.php?action=publication-edit&id=' . $id);
                exit;
            }
        }

        $currentUser = $this->currentUsername();
        if (!$currentUser) {
            header('Location: login.php');
            exit;
        }
        $this->ensureOwner($publication);

        $errors = [];
        $old = [
            'forum_id' => (int)($publication['forum_id'] ?? 0),
            'author'   => $publication['author'] ?? $currentUser,
            'content'  => $publication['content'] ?? '',
        ];
        

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST['author'] = $currentUser;
            $_POST['forum_id'] = $old['forum_id'];
            [$clean, $errorsFromValidation] = $this->validatePublication($_POST);
            $errors = array_merge($errors, $errorsFromValidation);

            $clean['author'] = strtolower(preg_replace('/\s+/', ' ', trim($clean['author'])));
            $clean['content'] = trim(strip_tags($clean['content']));

            if (empty($errors)) {
                $this->publications->update($id, $clean['forum_id'], $clean['author'], $clean['content']);
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Publication mise a jour.'];
                header('Location: index.php?action=publications&forum_id=' . $clean['forum_id']);
                exit;
            }

            $old = $clean;
        }

        $this->render('publicationEdit.php', compact('forum', 'publication', 'errors', 'old', 'currentUser'));
    }

    public function deleteConfirmFront(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $forumId = isset($_GET['forum_id']) ? (int) $_GET['forum_id'] : 0;
        if ($id <= 0) {
            header("Location: index.php?action=publications&forum_id={$forumId}");
            exit;
        }

        $publication = $this->publications->getById($id);
        if (!$publication) {
            header("Location: index.php?action=publications&forum_id={$forumId}");
            exit;
        }

        if ($this->isAdmin()) {
            $reports = $this->reportModel->countByTarget('publication', $id);
            if ($reports > 0) {
                header('Location: admin.php?action=publication-delete&id=' . $id);
                exit;
            }
        }

        $currentUser = $this->currentUsername();
        if (!$currentUser) {
            header('Location: login.php');
            exit;
        }
        $this->ensureOwner($publication);

        $this->render('publicationDeleteConfirm.php', [
            'publication' => $publication,
            'forumId' => $forumId ?: (int)($publication['forum_id'] ?? 0),
        ]);
    }

    public function deleteFront(int $id, int $forum_id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=publications&forum_id=" . $forum_id);
            exit;
        }

        $publication = $this->publications->getById($id);
        if (!$publication) {
            header("Location: index.php?action=publications&forum_id=" . $forum_id);
            exit;
        }

        if ($this->isAdmin()) {
            $reports = $this->reportModel->countByTarget('publication', $id);
            if ($reports > 0) {
                header('Location: admin.php?action=publication-delete&id=' . $id);
                exit;
            }
        }

        $currentUser = $this->currentUsername();
        if (!$currentUser) {
            header('Location: login.php');
            exit;
        }
        $this->ensureOwner($publication);

        $this->publications->delete($id);
        $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Publication supprimee.'];
        header("Location: index.php?action=publications&forum_id=" . (int)($publication['forum_id'] ?? $forum_id));
        exit;
    }
}
