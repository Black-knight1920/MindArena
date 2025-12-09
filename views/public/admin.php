<?php
// admin.php

require_once __DIR__ . '/../../config/bootstrap.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only allow admin user from the hardcoded login.
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$db  = new Database();
$pdo = $db->getConnection();

$admin = new AdminController($pdo);

// action via ?action=...
$action = isset($_GET['action']) ? trim($_GET['action']) : 'dashboard';

switch ($action) {

    case 'dashboard':
        $admin->dashboard();
        break;

    case 'forums':
        $admin->forumList();
        break;

    case 'forum-add':
        $admin->forumAdd();
        break;

    case 'forum-edit':
        $admin->forumEdit();
        break;

    case 'forum-delete':
        $admin->forumDelete();
        break;

    case 'publication-add':
        $admin->publicationAdd();
        break;

    case 'publication-edit':
        $admin->publicationEdit();
        break;

    case 'publication-delete':
        // deletion handled by controller (GET for convenience, mirror forumDelete)
        $admin->publicationDelete();
        break;

    case 'admin-add':
        $admin->adminAdd();
        break;

    case 'reports':
        $admin->reportList();
        break;

    case 'report-status':
        $admin->updateReportStatus();
        break;

    case 'delete-report':
        $admin->deleteReport();
        break;

    case 'user-stats':                 // dY\"ť NOUVELLE PAGE
        $admin->userStats();
        break;

    case 'publications':
        $admin->publicationList();
        break;

    default:
        $admin->dashboard();
        break;
}

