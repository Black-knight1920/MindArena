<?php
// index.php

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/ForumController.php';

$db   = new Database();
$pdo  = $db->getConnection();
$forumController = new ForumController($pdo);

// action passée en GET, ex: index.php?action=forums
$action = $_GET['action'] ?? 'home';

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

    default:
        $forumController->home();
        break;
}
