<?php

require_once __DIR__ . '/../Models/Forum.php';
require_once __DIR__ . '/../Models/Report.php';
class ForumController
{
    private PDO $pdo;
    private Forum $forumModel;
    private Report $reportModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->forumModel = new Forum($pdo);
        $this->reportModel = new Report($pdo);
    }

    private function getAllForumsForFront(): array
    {
        if (method_exists($this->forumModel, 'getAllFront')) {
            return $this->forumModel->getAllFront();
        }

        if (method_exists($this->forumModel, 'getAll')) {
            return $this->forumModel->getAll();
        }

        return [];
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

    private function sortForums(array $forums, string $sort, string $dir): array
    {
        $direction = strtolower($dir) === 'asc' ? 1 : -1;
        $sort = in_array($sort, ['date', 'title', 'author'], true) ? $sort : 'date';

        usort($forums, function ($a, $b) use ($sort, $direction) {
            switch ($sort) {
                case 'title':
                    $aval = strtolower($a['title'] ?? '');
                    $bval = strtolower($b['title'] ?? '');
                    break;
                case 'author':
                    $aval = strtolower($a['created_by'] ?? '');
                    $bval = strtolower($b['created_by'] ?? '');
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
        return $forums;
    }

    private function ensureOwner(array $forum): void
    {
        $currentUser = $this->currentUsername();
        if (!$currentUser || strcasecmp($currentUser, (string)($forum['created_by'] ?? '')) !== 0) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['_flash'][] = ['type' => 'error', 'message' => 'Action non autorisAc.'];
            header('Location: index.php?action=forums');
            exit;
        }
    }

    public function home(): void
    {
        $forums = $this->getAllForumsForFront();
        $currentUser = $this->currentUsername();
        $isAdmin = $this->isAdmin();

        include VIEW_PATH . '/front/home.php';
    }

    public function listFront(): void
    {
        $forums = $this->getAllForumsForFront();
        foreach ($forums as &$f) {
            $f['report_count'] = $this->reportModel->countByTarget('forum', (int)($f['id'] ?? 0));
        }
        unset($f);
        $sort = isset($_GET['sort']) ? strtolower($_GET['sort']) : 'date';
        $dir  = isset($_GET['dir']) ? strtolower($_GET['dir']) : 'desc';
        $forums = $this->sortForums($forums, $sort, $dir);
        $currentUser = $this->currentUsername();
        $isAdmin = $this->isAdmin();

        include VIEW_PATH . '/front/forumList.php';
    }

    public function addFront(): void
    {
        $currentUser = $this->currentUsername();
        if (!$currentUser) {
            header('Location: login.php');
            exit;
        }

        $errors = [];
        $title = '';
        $description = '';
        $createdBy = $currentUser;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim(strip_tags($_POST['title'] ?? ''));
            $description = trim(strip_tags($_POST['description'] ?? ''));

            if ($title === '') {
                $errors['title'] = 'Le titre est obligatoire.';
            } else {
                $err = [];
                $len = mb_strlen($title);
                if ($len < 3) $err[] = 'Le titre doit contenir au moins 3 caractAres.';
                if ($len > 80) $err[] = 'Le titre ne doit pas dAcpasser 80 caractAres.';
                if ($err) $errors['title'] = implode(' ', $err);
            }

            // Description: optional but max 500 chars
            if ($description !== '') {
                $dlen = mb_strlen($description);
                if ($dlen > 500) {
                    $errors['description'] = 'La description ne doit pas dAcpasser 500 caractAres.';
                }
            }

            if (empty($errors)) {
                $this->forumModel->create($title, $description, $createdBy);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Forum crAcAc.'];
                header('Location: index.php?action=forums');
                exit;
            }
        }

        include VIEW_PATH . '/front/forumAdd.php';
    }

    public function editFront(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: index.php?action=forums');
            exit;
        }

        $forum = $this->forumModel->getById($id);
        if (!$forum) {
            header('Location: index.php?action=forums');
            exit;
        }

        // Admin: passe par panneau admin seulement si signale
        if ($this->isAdmin()) {
            $reports = $this->reportModel->countByTarget('forum', $id);
            if ($reports > 0) {
                header('Location: admin.php?action=forum-edit&id=' . $id);
                exit;
            }
        }

        $currentUser = $this->currentUsername();
        if (!$currentUser) {
            header('Location: login.php');
            exit;
        }

        $this->ensureOwner($forum);

        $errors = [];
        $title = $forum['title'] ?? '';
        $description = $forum['description'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim(strip_tags($_POST['title'] ?? ''));
            $description = trim(strip_tags($_POST['description'] ?? ''));

            if ($title === '') {
                $errors['title'] = 'Le titre est obligatoire.';
            } else {
                $err = [];
                $len = mb_strlen($title);
                if ($len < 3) $err[] = 'Le titre doit contenir au moins 3 caracteres.';
                if ($len > 80) $err[] = 'Le titre ne doit pas depasser 80 caracteres.';
                if ($err) $errors['title'] = implode(' ', $err);
            }
            if ($description !== '' && mb_strlen($description) > 500) {
                $errors['description'] = 'La description ne doit pas depasser 500 caracteres.';
            }

            if (empty($errors)) {
                $this->forumModel->update($id, $title, $description);
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Forum mis a jour.'];
                header('Location: index.php?action=forums');
                exit;
            }
        }

        include VIEW_PATH . '/front/forumEdit.php';
    }

    public function deleteConfirmFront(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: index.php?action=forums');
            exit;
        }

        $forum = $this->forumModel->getById($id);
        if (!$forum) {
            header('Location: index.php?action=forums');
            exit;
        }

        if ($this->isAdmin()) {
            $reports = $this->reportModel->countByTarget('forum', $id);
            if ($reports > 0) {
                header('Location: admin.php?action=forum-delete&id=' . $id);
                exit;
            }
        }

        $currentUser = $this->currentUsername();
        if (!$currentUser) {
            header('Location: login.php');
            exit;
        }
        $this->ensureOwner($forum);

        include VIEW_PATH . '/front/forumDeleteConfirm.php';
    }

    public function deleteFront(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=forums');
            exit;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            header('Location: index.php?action=forums');
            exit;
        }

        $forum = $this->forumModel->getById($id);
        if (!$forum) {
            header('Location: index.php?action=forums');
            exit;
        }

        if ($this->isAdmin()) {
            $reports = $this->reportModel->countByTarget('forum', $id);
            if ($reports > 0) {
                header('Location: admin.php?action=forum-delete&id=' . $id);
                exit;
            }
        }

        $currentUser = $this->currentUsername();
        if (!$currentUser) {
            header('Location: login.php');
            exit;
        }
        $this->ensureOwner($forum);

        $this->forumModel->delete($id);
        $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Forum supprime.'];
        header('Location: index.php?action=forums');
        exit;
    }
}
