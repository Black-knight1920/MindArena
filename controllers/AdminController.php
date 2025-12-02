<?php

require_once __DIR__ . '/../models/Forum.php';
require_once __DIR__ . '/../models/Publication.php';
require_once __DIR__ . '/../models/Report.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/User.php';

require_once __DIR__ . '/../services/NotificationService.php';
require_once __DIR__ . '/../services/ReputationService.php';
require_once __DIR__ . '/../services/UserStatsService.php';
// Helpers removed: inline validation used instead of external helper

class AdminController
{
    private $db;
    private $forumModel;
    private $publicationModel;
    private $reportModel;
    private $notificationModel;
    private $userModel;

    private $notificationService;
    private $reputationService;

    public function __construct(PDO $db)
    {
        $this->db = $db;

        $this->forumModel        = new Forum($db);
        $this->publicationModel  = new Publication($db);
        $this->reportModel       = new Report($db);
        $this->notificationModel = new Notification($db);
        $this->userModel         = new User($db);

        $this->notificationService = new NotificationService($db);
        $this->reputationService   = new ReputationService();
    }

    private function render($view, $title, $active, $data = [])
    {
        // Ensure BASE_URL is defined
        if (!defined('BASE_URL')) {
            require_once __DIR__ . '/../config/constants.php';
        }

        $unread         = $this->notificationService->countUnread();
        $notifications  = $this->notificationService->getLatest();

        extract($data);

        $pageTitle = $title;
        $viewFile  = __DIR__ . '/../views/admin/' . $view . '.php';

        if (!file_exists($viewFile)) {
            $viewFile = __DIR__ . '/../views/admin/errorView.php';
        }

        // Make viewFile available to the layout
        $data['viewFile'] = $viewFile;
        $data['active'] = $active;
        $data['unread'] = $unread;
        $data['notifications'] = $notifications;
        $data['pageTitle'] = $pageTitle;
        extract($data);

        include __DIR__ . '/../views/admin/layout/layout.php';
    }

    /* --------------------- DASHBOARD --------------------- */
    public function dashboard()
    {
        $forumsCount       = $this->forumModel->countForums();
        $publicationsCount = $this->publicationModel->countPublications();
        $reportsTotal      = $this->reportModel->count();
        $reportsPending    = $this->reportModel->countPending();

        $userRanking       = $this->userModel->getFakeRanking();
        $usersCount        = $this->userModel->countUsers();

        $this->render("dashboard", "Dashboard", "dashboard", [
            "totalForums"       => $forumsCount,
            "totalPublications" => $publicationsCount,
            "totalReports"      => $reportsTotal,
            "pendingReports"    => $reportsPending,
            "userRanking"       => $userRanking,
            "usersCount"        => $usersCount
        ]);
    }

    /* --------------------- FORUMS --------------------- */
    public function forumList()
    {
        // Utiliser getAllWithStats() pour avoir le nombre de publications avec jointure
        $forums = $this->forumModel->getAllWithStats();

        $this->render("forumList", "Forums", "forums", [
            "forums" => $forums
        ]);
    }

    /* --------------------- REPORTS --------------------- */
    public function reportList()
    {
        // Utiliser getAllWithFullDetails() pour avoir toutes les infos avec jointures complètes
        $reports = $this->reportModel->getAllWithFullDetails();
        $totalReports = $this->reportModel->count();
        $pendingReports = $this->reportModel->countPending();

        $this->render("reportList", "Signalements", "reports", [
            "reports" => $reports,
            "totalReports" => $totalReports,
            "pendingReports" => $pendingReports
        ]);
    }

    public function updateReportStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=reports');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = $_POST['status'] ?? '';

        if ($id > 0 && in_array($status, ['pending', 'seen', 'resolved'], true)) {
            try {
                $this->reportModel->updateStatus($id, $status);
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Statut du signalement mis à jour.'];
            } catch (Exception $e) {
                $_SESSION['_flash'][] = ['type' => 'error', 'message' => 'Erreur lors de la mise à jour.'];
            }
        }

        header('Location: admin.php?action=reports');
        exit;
    }

    /**
     * Supprime un signalement (attendu en POST).
     */
    public function deleteReport()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=reports');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            try {
                $this->reportModel->delete($id);
                // Optionnel : créer une notification système
                $this->notificationService->create(
                    'system',
                    'Signalement supprimé',
                    "Le signalement #{$id} a été supprimé par un administrateur.",
                    'admin.php?action=reports'
                );
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Signalement supprimé.'];
            } catch (Exception $e) {
                $_SESSION['_flash'][] = ['type' => 'error', 'message' => 'Erreur lors de la suppression.'];
            }
        }

        header('Location: admin.php?action=reports');
        exit;
    }

    /* --------------------- FORUM CRUD --------------------- */
    public function forumAdd()
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

            // Validate title
            if ($title === '') {
                $errors['title'] = 'Le titre est obligatoire.';
            } else {
                $lenErrors = [];
                $len = mb_strlen($title);
                if ($len < 3) $lenErrors[] = 'Le titre doit contenir au moins 3 caractères.';
                if ($len > 80) $lenErrors[] = 'Le titre ne doit pas dépasser 80 caractères.';
                if ($lenErrors) $errors['title'] = implode(' ', $lenErrors);
            }

            // Description length (max)
            $descErr = [];
            $dlen = mb_strlen($description);
            if ($dlen > 500) $descErr[] = 'La description ne doit pas dépasser 500 caractères.';
            if ($descErr) $errors['description'] = implode(' ', $descErr);

            if ($createdBy === '') {
                $createdBy = 'Admin';
            } else {
                $cbErr = [];
                $cblen = mb_strlen($createdBy);
                if ($cblen > 50) $cbErr[] = 'Le nom du créateur ne doit pas dépasser 50 caractères.';
                if ($cbErr) $errors['created_by'] = implode(' ', $cbErr);
            }

            if (empty($errors)) {
                try {
                    if ($this->forumModel->create($title, $description, $createdBy)) {
                        $this->notificationService->create(
                            'forum',
                            'Nouveau forum créé',
                            "Le forum \"{$title}\" a été créé.",
                            'admin.php?action=forums'
                        );
                        $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Forum créé avec succès.'];
                        // Stay on the same page (forum-add) after creation
                        $title = '';
                        $description = '';
                        $createdBy = '';
                    } else {
                        $errors['general'] = 'Erreur lors de la création du forum.';
                    }
                } catch (Exception $e) {
                    $errors['general'] = 'Erreur lors de la création du forum.';
                }
            }
        }

        $this->render("forumAdd", "Ajouter un forum", "forums", [
            "errors" => $errors,
            "title" => $title,
            "description" => $description,
            "created_by" => $createdBy
        ]);
    }

    public function forumEdit()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: admin.php?action=forums');
            exit;
        }

        // Utiliser getByIdWithStats() pour avoir les statistiques avec jointure
        $forum = $this->forumModel->getByIdWithStats($id);
        if (!$forum) {
            header('Location: admin.php?action=forums');
            exit;
        }

        $errors = [];
        $title = $forum['title'] ?? '';
        $description = $forum['description'] ?? '';
        $createdBy = $forum['created_by'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // sanitize inputs
            $title = trim(strip_tags($_POST['title'] ?? ''));
            $description = trim(strip_tags($_POST['description'] ?? ''));
            $createdBy = strtolower(preg_replace('/\s+/', ' ', trim($_POST['created_by'] ?? '')));

            // Validate title
            if ($title === '') {
                $errors['title'] = 'Le titre est obligatoire.';
            } else {
                $lenErrors = [];
                $len = mb_strlen($title);
                if ($len < 3) $lenErrors[] = 'Le titre doit contenir au moins 3 caractères.';
                if ($len > 80) $lenErrors[] = 'Le titre ne doit pas dépasser 80 caractères.';
                if ($lenErrors) $errors['title'] = implode(' ', $lenErrors);
            }

            // Description length (max)
            $descErr = [];
            $dlen = mb_strlen($description);
            if ($dlen > 500) $descErr[] = 'La description ne doit pas dépasser 500 caractères.';
            if ($descErr) $errors['description'] = implode(' ', $descErr);

            $cbErr = [];
            $cblen = mb_strlen($createdBy);
            if ($cblen > 50) $cbErr[] = 'Le nom du créateur ne doit pas dépasser 50 caractères.';
            if ($cbErr) $errors['created_by'] = implode(' ', $cbErr);

            if (empty($errors)) {
                try {
                    if ($this->forumModel->update($id, $title, $description, $createdBy)) {
                        $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Forum mis à jour.'];
                        header('Location: admin.php?action=forums');
                        exit;
                    } else {
                        $errors['general'] = 'Erreur lors de la mise à jour du forum.';
                    }
                } catch (Exception $e) {
                    $errors['general'] = 'Erreur lors de la mise à jour du forum.';
                }
            }
        }

        $this->render("ForumEdit", "Modifier un forum", "forums", [
            "errors" => $errors,
            "forum" => $forum,
            "title" => $title,
            "description" => $description,
            "created_by" => $createdBy
        ]);
    }

    public function forumDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=forums');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            try {
                $this->forumModel->delete($id);
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Forum supprimé.'];
            } catch (Exception $e) {
                $_SESSION['_flash'][] = ['type' => 'error', 'message' => 'Erreur lors de la suppression du forum.'];
            }
        }
        header('Location: admin.php?action=forums');
        exit;
    }

    /* --------------------- PUBLICATION CRUD --------------------- */
    public function publicationAdd()
    {
        $errors = [];
        $forum_id = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;
        $author = '';
        $content = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $forum_id = isset($_POST['forum_id']) ? (int)$_POST['forum_id'] : 0;
            $author = strtolower(preg_replace('/\s+/', ' ', trim($_POST['author'] ?? '')));
            $content = trim(strip_tags($_POST['content'] ?? ''));

            if ($forum_id <= 0) {
                $errors['forum_id'] = 'Forum invalide.';
            }

            if ($author !== '') {
                $aErr = [];
                if (mb_strlen($author) > 40) $aErr[] = 'Le nom de l\'auteur ne doit pas dépasser 40 caractères.';
                if ($aErr) $errors['author'] = implode(' ', $aErr);
            }

            $cErr = [];
            if ($content === '') {
                $cErr[] = 'Le contenu est obligatoire.';
            } else {
                $clen = mb_strlen($content);
                if ($clen < 10) $cErr[] = 'Le contenu doit contenir au moins 10 caractères.';
                if ($clen > 1000) $cErr[] = 'Le contenu ne doit pas dépasser 1000 caractères.';
            }
            if ($cErr) $errors['content'] = implode(' ', $cErr);

            if (empty($errors)) {
                try {
                    // Pass author as-is; model will convert empty string to NULL for anonymous posts
                    $result = $this->publicationModel->create($forum_id, $author, $content);
                    if ($result) {
                        $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Publication créée.'];
                        // Stay on the same forum's publication list after creation
                        header("Location: admin.php?action=publications&forum_id=" . (int)$forum_id);
                        exit;
                    } else {
                        $errors['general'] = 'Erreur lors de la création de la publication (create retourna false).';
                    }
                } catch (PDOException $e) {
                    $errors['general'] = 'Erreur base de données : ' . htmlspecialchars($e->getMessage());
                } catch (Exception $e) {
                    $errors['general'] = 'Erreur : ' . htmlspecialchars($e->getMessage());
                }
            }
        }

        // Utiliser getAllWithStats() pour avoir les statistiques avec jointure
        $forums = $this->forumModel->getAllWithStats();

        $this->render("publicationAdd", "Ajouter une publication", "publications", [
            "errors" => $errors,
            "forums" => $forums,
            "forum_id" => $forum_id,
            "author" => $author,
            "content" => $content
        ]);
    }

    public function publicationEdit()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: admin.php?action=publications');
            exit;
        }

        // Utiliser getByIdWithForum() pour avoir les infos du forum avec jointure
        $publication = $this->publicationModel->getByIdWithForum($id);
        if (!$publication) {
            header('Location: admin.php?action=publications');
            exit;
        }

        $errors = [];
        $forum_id = $publication['forum_id'] ?? 0;
        $author = $publication['author'] ?? '';
        $content = $publication['content'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $forum_id = isset($_POST['forum_id']) ? (int)$_POST['forum_id'] : 0;
            $author = strtolower(preg_replace('/\s+/', ' ', trim($_POST['author'] ?? '')));
            $content = trim(strip_tags($_POST['content'] ?? ''));

            if ($forum_id <= 0) {
                $errors['forum_id'] = 'Forum invalide.';
            }

            if ($author !== '') {
                $aErr = [];
                if (mb_strlen($author) > 40) $aErr[] = 'Le nom de l\'auteur ne doit pas dépasser 40 caractères.';
                if ($aErr) $errors['author'] = implode(' ', $aErr);
            }

            $cErr = [];
            if ($content === '') {
                $cErr[] = 'Le contenu est obligatoire.';
            } else {
                $clen = mb_strlen($content);
                if ($clen < 10) $cErr[] = 'Le contenu doit contenir au moins 10 caractères.';
                if ($clen > 1000) $cErr[] = 'Le contenu ne doit pas dépasser 1000 caractères.';
            }
            if ($cErr) $errors['content'] = implode(' ', $cErr);

            if (empty($errors)) {
                try {
                    // Pass author as-is; model will convert empty string to NULL for anonymous posts
                    if ($this->publicationModel->update($id, $forum_id, $author, $content)) {
                        $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Publication mise à jour.'];
                        header("Location: admin.php?action=publications");
                        exit;
                    } else {
                        $errors['general'] = 'Erreur lors de la mise à jour de la publication.';
                    }
                } catch (PDOException $e) {
                    $errors['general'] = 'Erreur base de données : ' . htmlspecialchars($e->getMessage());
                } catch (Exception $e) {
                    $errors['general'] = 'Erreur : ' . htmlspecialchars($e->getMessage());
                }
            }
        }

        // Utiliser getAllWithStats() pour avoir les statistiques avec jointure
        $forums = $this->forumModel->getAllWithStats();

        $this->render("publicationEdit", "Modifier une publication", "publications", [
            "errors" => $errors,
            "publication" => $publication,
            "forums" => $forums,
            "forum_id" => $forum_id,
            "author" => $author,
            "content" => $content
        ]);
    }

    public function publicationDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=publications');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            try {
                $this->publicationModel->delete($id);
                $_SESSION['_flash'][] = ['type' => 'success', 'message' => 'Publication supprimée.'];
            } catch (Exception $e) {
                $_SESSION['_flash'][] = ['type' => 'error', 'message' => 'Erreur lors de la suppression.'];
            }
        }
        header('Location: admin.php?action=publications');
        exit;
    }

    /* --------------------- USER STATS --------------------- */
    public function userStats()
    {
        require_once __DIR__ . '/../services/UserStatsService.php';
        $userStatsService = new UserStatsService($this->db);

        $overview = $userStatsService->getGlobalOverview();
        $topContributors = $userStatsService->getTopContributors(20);

        $this->render("UserStats", "Statistiques utilisateurs", "user-stats", [
            "overview" => $overview,
            "topContributors" => $topContributors
        ]);
    }

    /* --------------------- PUBLICATIONS (for admin) --------------------- */
    public function publicationList()
    {
        // Optionnel: filtrer par forum_id si fourni
        $forum_id = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;
        $forum = null;

        if ($forum_id > 0) {
            $forum = $this->forumModel->getById($forum_id);
            if (!$forum) {
                header('Location: admin.php?action=forums');
                exit;
            }
            // Récupérer les publications pour ce forum spécifique
            $publications = $this->publicationModel->getByForum($forum_id);
        } else {
            // Utiliser getAllWithFullDetails() pour avoir toutes les infos avec jointures complètes
            $publications = $this->publicationModel->getAllWithFullDetails();
        }

        $this->render("publicationList", "Publications", "publications", [
            "publications" => $publications,
            "forum" => $forum,
            "forum_id" => $forum_id
        ]);
    }
}
