<?php

// Load controller
require_once __DIR__ . '/controllers/ReclamationController.php';

// Determine action
$action = $_GET['action'] ?? 'home';

$controller = new ReclamationController();

switch ($action) {

    case 'send_reclamation':
        $controller->create();
        break;

    case 'reclamation_list':
        $controller->list();
        break;

    case 'delete_reclamation':
        if (isset($_GET['id'])) {
            $controller->delete($_GET['id']);
        }
        break;

    case 'contact': 
        include __DIR__ . '/view/contact.php';
        break;

    case 'contact_success':
        include __DIR__ . '/views/index.html';
        break;

    default:
        // Default to home/front page
        include __DIR__ . '/views/index.html';
}
