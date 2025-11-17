<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/config/Database.php";
require_once __DIR__ . "/controllers/AdminController.php";

$db = new Database();
$pdo = $db->getConnection();

$admin = new AdminController($pdo);

// simple action routing
$action = $_GET['action'] ?? 'dashboard';

function needId() {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("<h2 style='color:red;text-align:center;margin-top:40px;'>Invalid ID</h2>");
    }
}

switch ($action) {

    case "dashboard": 
        $admin->dashboard(); 
        break;

    /* FORUMS */
    case "forums":
        $admin->forumList();
        break;

    case "forum-add":
        $admin->forumAdd();
        break;

    case "forum-edit":
        needId();
        $admin->forumEdit();
        break;

    case "forum-delete":
        needId();
        $admin->forumDelete();
        break;

    /* PUBLICATIONS */
    case "publications":
        $admin->publicationList();
        break;

    case "publication-add":
        $admin->publicationAdd();
        break;

    case "publication-edit":
        needId();
        $admin->publicationEdit();
        break;

    case "publication-delete":
        needId();
        $admin->publicationDelete();
        break;

    default:
        echo "<h1 style='color:red;text-align:center;margin-top:40px;'>❌ Unknown action: $action</h1>";
}
