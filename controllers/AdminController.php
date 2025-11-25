<?php

class AdminController
{
    private PDO $pdo;
    private Forum $forumModel;
    private Publication $publicationModel;
    private Report $reportModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        require_once __DIR__ . '/../models/Forum.php';
        require_once __DIR__ . '/../models/Publication.php';
        require_once __DIR__ . '/../models/Report.php';

        $this->forumModel       = new Forum($pdo);
        $this->publicationModel = new Publication($pdo);
        $this->reportModel      = new Report($pdo);
    }

    private function render(string $view, string $pageTitle = "", string $activeMenu = "", array $data = []): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../views/admin/' . $view . '.php';
        $layout   = __DIR__ . '/../views/admin/layout.php'; // <— adapte si ton layout est ailleurs

        include $layout;
    }

    /* ===================== DASHBOARD ===================== */
    public function dashboard(): void
    {
        $totalForums       = $this->forumModel->count();
        $totalPublications = $this->publicationModel->count();
        $totalReports      = $this->reportModel->count();
        $pendingReports    = $this->reportModel->countPending();

        $this->render(
            'dashboard',
            'Dashboard',
            'dashboard',
            compact('totalForums', 'totalPublications', 'totalReports', 'pendingReports')
        );
    }

    /* ===================== FORUMS ===================== */

    public function forumList(): void
    {
        $forums = $this->forumModel->getAll();

        $this->render(
            'forumList',
            'Forums',
            'forums',
            compact('forums')
        );
    }

    public function forumAdd(): void
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title       = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $createdBy   = trim($_POST['created_by'] ?? '');

            if ($title === '') {
                $errors['title'] = "Le titre est obligatoire.";
            }

            if (empty($errors)) {
                $this->forumModel->create($title, $description, $createdBy ?: 'Admin');
                header('Location: admin.php?action=forums');
                exit;
            }
        }

        $this->render(
            'forumAdd',
            'Nouveau forum',
            'forums',
            compact('errors')
        );
    }

    public function forumEdit(): void
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            die("ID invalide");
        }
        $id = (int)$_GET['id'];

        $forum = $this->forumModel->getById($id);
        if (!$forum) {
            die("Forum introuvable");
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title       = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $createdBy   = trim($_POST['created_by'] ?? '');

            if ($title === '') {
                $errors['title'] = "Le titre est obligatoire.";
            }

            if (empty($errors)) {
                $this->forumModel->update($id, $title, $description, $createdBy ?: null);
                header('Location: admin.php?action=forums');
                exit;
            }

            // renvoyer les valeurs saisies en cas d’erreur
            $forum['title']       = $title;
            $forum['description'] = $description;
            $forum['created_by']  = $createdBy;
        }

        $this->render(
            'forumEdit',
            'Modifier un forum',
            'forums',
            compact('forum', 'errors')
        );
    }

    public function forumDelete(): void
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            die("ID invalide");
        }

        $id = (int)$_GET['id'];
        $this->forumModel->delete($id);

        header('Location: admin.php?action=forums');
        exit;
    }

    /* ===================== PUBLICATIONS ===================== */

    public function publicationList(): void
    {
        $forumId = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : null;

        if ($forumId) {
            $publications = $this->publicationModel->getByForum($forumId);
            $forum        = $this->forumModel->getById($forumId);
        } else {
            $publications = $this->publicationModel->getAll();
            $forum        = null;
        }

        $this->render(
            'publicationList',
            'Publications',
            'publications',
            compact('publications', 'forum')
        );
    }

    public function publicationAdd(): void
    {
        $forums = $this->forumModel->getAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $forumId = (int)($_POST['forum_id'] ?? 0);
            $author  = trim($_POST['author'] ?? '');
            $content = trim($_POST['content'] ?? '');

            if ($forumId <= 0) {
                $errors['forum_id'] = "Veuillez choisir un forum.";
            }
            if ($content === '') {
                $errors['content'] = "Le contenu est obligatoire.";
            }

            if (empty($errors)) {
                $this->publicationModel->create($forumId, $author, $content);
                header("Location: admin.php?action=publications&forum_id={$forumId}");
                exit;
            }
        }

        $this->render(
            'publicationAdd',
            'Nouvelle publication',
            'publications',
            compact('forums', 'errors')
        );
    }

    public function publicationEdit(): void
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            die("ID invalide");
        }

        $id = (int)$_GET['id'];
        $publication = $this->publicationModel->getById($id);
        if (!$publication) {
            die("Publication introuvable");
        }

        $forums = $this->forumModel->getAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $forumId = (int)($_POST['forum_id'] ?? 0);
            $author  = trim($_POST['author'] ?? '');
            $content = trim($_POST['content'] ?? '');

            if ($forumId <= 0) {
                $errors['forum_id'] = "Veuillez choisir un forum.";
            }
            if ($content === '') {
                $errors['content'] = "Le contenu est obligatoire.";
            }

            if (empty($errors)) {
                $this->publicationModel->update($id, $forumId, $author, $content);
                header("Location: admin.php?action=publications&forum_id={$forumId}");
                exit;
            }

            // renvoyer les valeurs saisies
            $publication['forum_id'] = $forumId;
            $publication['author']   = $author;
            $publication['content']  = $content;
        }

        $this->render(
            'publicationEdit',
            'Modifier une publication',
            'publications',
            compact('publication', 'forums', 'errors')
        );
    }

    public function publicationDelete(): void
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            die("ID invalide");
        }

        $id  = (int)$_GET['id'];
        $pub = $this->publicationModel->getById($id);

        $this->publicationModel->delete($id);

        $forumId = $pub['forum_id'] ?? null;
        if ($forumId) {
            header("Location: admin.php?action=publications&forum_id={$forumId}");
        } else {
            header("Location: admin.php?action=publications");
        }
        exit;
    }

    /* ===================== REPORTS ===================== */

       public function reportList()
    {
        // Liste avec jointures
        $reports        = $this->reportModel->getAllWithTargets();
        $totalReports   = $this->reportModel->count();
        $pendingReports = $this->reportModel->countPending();

        $this->render(
            "reportList",
            "Signalements",
            "reports",
            compact("reports", "totalReports", "pendingReports")
        );
    }


    public function updateReportStatus(): void
    {
        if (!isset($_GET['id'], $_GET['status'])) {
            die("Requête invalide");
        }

        $id     = (int)$_GET['id'];
        $status = $_GET['status'];

        if (!in_array($status, ['pending', 'seen', 'resolved'], true)) {
            die("Statut invalide");
        }

        $this->reportModel->updateStatus($id, $status);

        header("Location: admin.php?action=reports");
        exit;
    }
}
