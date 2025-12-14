<?php
// index.php - routeur principal front forum

require_once __DIR__ . '/../../config/bootstrap.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db  = new Database();
$pdo = $db->getConnection();
$forumController = new ForumController($pdo);

// action passAce en GET, ex: index.php?action=forums
$action = isset($_GET['action']) ? trim($_GET['action']) : 'home';

switch ($action) {
    case 'home':
        $forumController->home();
        break;

    case 'forums':          // liste des forums (front/forums)
        $forumController->listFront();
        break;

    case 'add-forum':       // front/add-forum
        $forumController->addFront();
        break;

    case 'edit-forum':
        $forumController->editFront();
        break;

    case 'delete-forum-confirm':
        $forumController->deleteConfirmFront();
        break;

    case 'delete-forum':
        $forumController->deleteFront();
        break;

    case 'publications':
        $publicationController = new PublicationController($pdo);
        $publicationController->listFront();
        break;

    case 'add-publication':
        $publicationController = new PublicationController($pdo);
        $publicationController->addFront();
        break;

    case 'edit-publication':
        $publicationController = new PublicationController($pdo);
        $publicationController->editFront();
        break;

    case 'delete-publication-confirm':
        $publicationController = new PublicationController($pdo);
        $publicationController->deleteConfirmFront();
        break;

    case 'delete-publication':
        $publicationController = new PublicationController($pdo);
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $forum_id = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;
        $publicationController->deleteFront($id, $forum_id);
        break;

    case 'report':
        $reportController = new ReportController($pdo);
        $reportController->addFront();
        break;

    // Auth views (login/forgot/reset) routed via index.php pour Acviter les 404
    case 'login':
        include VIEW_PATH . '/frontend/login.php';
        break;

    case 'forgot':
        $forgotController = new ForgotController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $forgotController->handleForgotRequest();
        } else {
            $forgotController->showForgotForm();
        }
        break;

    case 'reset':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resetController = new ResetPasswordController();
            $resetController->reset();
        } else {
            include VIEW_PATH . '/frontend/reset_password.php';
        }
        break;

    case 'top-contributors':
        $userStatsService = new UserStatsService($pdo);
        $topContributors = $userStatsService->getTopContributors(20);
        $overview = $userStatsService->getGlobalOverview();
        include VIEW_PATH . '/front/topContributors.php';
        break;

    case 'profile':
        $profileController = new ProfileController($pdo);
        $profileController->show();
        break;

    default:
        $forumController->home();
        break;
}

