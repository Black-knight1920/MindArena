<?php

class ReportController
{
    private Report $reportModel;

    public function __construct(PDO $pdo)
    {
        require_once __DIR__ . '/../models/Report.php';
        $this->reportModel = new Report($pdo);
    }

    /**
     * Front : formulaire + envoi d'un signalement
     * URL : /mindarena_forum/front/report?target_type=forum|publication&target_id=XX
     */
    public function addFront()
    {
        $targetType = $_GET['target_type'] ?? $_POST['target_type'] ?? null;
        $targetId   = isset($_GET['target_id'])
            ? (int) $_GET['target_id']
            : (isset($_POST['target_id']) ? (int) $_POST['target_id'] : 0);

        $errors  = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason  = trim($_POST['reason']  ?? '');
            $details = trim($_POST['details'] ?? '');

            if (!$targetType || !in_array($targetType, ['forum', 'publication'], true)) {
                $errors[] = "Type de contenu invalide.";
            }

            if ($targetId <= 0) {
                $errors[] = "ID de contenu invalide.";
            }

            if ($reason === '') {
                $errors[] = "Veuillez choisir une raison de signalement.";
            }

            if (mb_strlen($details) < 5) {
                $errors[] = "Veuillez donner quelques détails (au moins 5 caractères).";
            }

            if (!$errors) {
                // 👉 On laisse Report::create gérer forum_id / publication_id
                $this->reportModel->create($targetType, $targetId, $reason, $details);
                $success = true;
                $_POST   = [];
            }
        }

        // Petit label pour afficher "Forum #X" ou "Publication #X"
        if ($targetType === 'publication') {
            $targetLabel = "Publication #{$targetId}";
        } else {
            $targetLabel = "Forum #{$targetId}";
        }

        include __DIR__ . '/../views/front/report.php';
    }
}
