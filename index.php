<?php
// index.php

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/ForumController.php';

$db   = new Database();
$pdo  = $db->getConnection();
$forumController = new ForumController($pdo);

// action passée en GET, ex: index.php?action=forums
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
        require_once __DIR__ . '/controllers/PublicationController.php';
        $publicationController = new PublicationController($pdo);
        $publicationController->listFront();
        break;

    case 'add-publication':
        require_once __DIR__ . '/controllers/PublicationController.php';
        $publicationController = new PublicationController($pdo);
        $publicationController->addFront();
        break;

    case 'edit-publication':
        require_once __DIR__ . '/controllers/PublicationController.php';
        $publicationController = new PublicationController($pdo);
        $publicationController->editFront();
        break;

    case 'delete-publication-confirm':
        require_once __DIR__ . '/controllers/PublicationController.php';
        $publicationController = new PublicationController($pdo);
        $publicationController->deleteConfirmFront();
        break;

    case 'delete-publication':
        require_once __DIR__ . '/controllers/PublicationController.php';
        $publicationController = new PublicationController($pdo);
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $forum_id = isset($_GET['forum_id']) ? (int)$_GET['forum_id'] : 0;
        $publicationController->deleteFront($id, $forum_id);
        break;

    case 'report':
        require_once __DIR__ . '/controllers/ReportController.php';
        $reportController = new ReportController($pdo);
        $reportController->addFront();
        break;

    case 'top-contributors':
        require_once __DIR__ . '/services/UserStatsService.php';
        $userStatsService = new UserStatsService($pdo);
        $topContributors = $userStatsService->getTopContributors(20);
        $overview = $userStatsService->getGlobalOverview();
        include __DIR__ . '/views/front/topContributors.php';
        break;

    default:
        $forumController->home();
        break;
}
