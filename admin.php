<?php
// ============ MindArena Admin Router ============

// Load DB
require_once __DIR__ . '/config/Database.php';

// Load AdminController
require_once __DIR__ . '/controllers/AdminController.php';

// Create DB connection
$db = new Database();
$pdo = $db->getConnection();

// Create admin controller
$admin = new AdminController($pdo);

// Detect action
$action = $_GET['action'] ?? 'dashboard';

// Routing
switch ($action) {

    /* ======================================================
       DASHBOARD
    ====================================================== */
    case 'dashboard':
        $admin->dashboard();
        break;

    /* ======================================================
       FORUMS
    ====================================================== */
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

    /* ======================================================
       PUBLICATIONS
    ====================================================== */
    case 'publications':
        $admin->publicationList();
        break;

    case 'publication-add':
        $admin->publicationAdd();
        break;

    case 'publication-edit':
        $admin->publicationEdit();
        break;

    case 'publication-delete':
        $admin->publicationDelete();
        break;

    /* ======================================================
       REPORTS
    ====================================================== */
    case 'reports':
        $admin->reportList();
        break;

    case 'report-status':
        $admin->updateReportStatus();
        break;

    /* ======================================================
       DEFAULT → DASHBOARD
    ====================================================== */
    default:
        $admin->dashboard();
        break;
}

