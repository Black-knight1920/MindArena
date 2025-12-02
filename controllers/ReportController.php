<?php

class ReportController
{
    private PDO $pdo;
    private Report $reportModel;
    private NotificationService $notificationService;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        require_once __DIR__ . '/../models/Report.php';
        require_once __DIR__ . '/../services/NotificationService.php';

        $this->reportModel         = new Report($pdo);
        $this->notificationService = new NotificationService($pdo);
    }

    /**
     * Création d'un signalement depuis le front.
     * URL genre : /mindarena_forum/front/report?target_type=forum&target_id=12
     */
    public function addFront(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type      = $_POST['target_type'] ?? '';
            $targetId  = (int)($_POST['target_id'] ?? 0);
            $reason    = trim($_POST['reason'] ?? '');
            $details   = trim($_POST['details'] ?? '');

            // Sanitize inputs
            $reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');
            $details = htmlspecialchars($details, ENT_QUOTES, 'UTF-8');

            // Validation
            if (!in_array($type, ['forum', 'publication'], true) || $targetId <= 0 || $reason === '') {
                header('Location: index.php?action=forums');
                exit;
            }

            if (mb_strlen($reason) > 100) {
                header('Location: index.php?action=forums');
                exit;
            }

            if (mb_strlen($details) > 1000) {
                header('Location: index.php?action=forums');
                exit;
            }

            try {
            // Création du report
            $this->reportModel->create($type, $targetId, $reason, $details);

                // Notification admin avec infos complètes
                require_once __DIR__ . '/../config/constants.php';
                require_once __DIR__ . '/../models/Forum.php';
                require_once __DIR__ . '/../models/Publication.php';
                
                $baseUrl = defined('BASE_URL') ? BASE_URL : '';
                $forumModel = new Forum($this->pdo);
                $publicationModel = new Publication($this->pdo);
                
                $message = '';
                if ($type === 'forum') {
                    // Utiliser getByIdWithStats() pour avoir les statistiques avec jointure
                    $forum = $forumModel->getByIdWithStats($targetId);
                    $forumTitle = $forum['title'] ?? 'Forum #' . $targetId;
                    $createdBy = $forum['created_by'] ?? 'Inconnu';
                    $publicationsCount = $forum['publications_count'] ?? 0;
                    $message = "Signalement du forum \"{$forumTitle}\" (ID: {$targetId}, Créé par: {$createdBy}, Publications: {$publicationsCount}). Raison: {$reason}";
                } else {
                    // Utiliser getByIdWithFullDetails() pour avoir toutes les infos avec jointures complètes
                    $publication = $publicationModel->getByIdWithFullDetails($targetId);
                    $author = $publication['author'] ?? 'Inconnu';
                    $contentPreview = mb_substr($publication['content'] ?? '', 0, 50) . (mb_strlen($publication['content'] ?? '') > 50 ? '...' : '');
                    $forumTitle = $publication['forum_title'] ?? 'Forum #' . ($publication['forum_id'] ?? 0);
                    $forumId = $publication['forum_id'] ?? 0;
                    $message = "Signalement de la publication #{$targetId} par {$author} dans \"{$forumTitle}\" (ID: {$forumId}). Contenu: {$contentPreview}. Raison: {$reason}";
                }
                
            $this->notificationService->create(
                'report',
                'Nouveau signalement',
                    $message,
                    $baseUrl . '/admin.php?action=reports'
            );
            } catch (Exception $e) {
                // Log error if needed, but don't expose to user
            }

            // Redirection après signalement
            header('Location: index.php?action=forums');
            exit;
        }

        // GET : afficher le formulaire de signalement
        include __DIR__ . '/../views/front/report.php';
    }
}
