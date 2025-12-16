<?php

// Load controller
require_once __DIR__ . '/Controller/ReclamationController.php'; // <-- double underscore

// Determine action
$action = $_GET['action'] ?? 'home';

$controller = new ReclamationController();

switch ($action) {

    case 'send_reclamation':
        $reclamation = new Reclamation(
            null,
            trim($_POST['full_name']),
            trim($_POST['email']),
            trim($_POST['subject']),
            trim($_POST['message'])
        );
        $controller->create($reclamation);
        break;

    case 'reclamation_list':
        $controller->listReclamation();
        break;

    case 'delete_reclamation':
        if (isset($_GET['id'])) {
            $controller->deleteReclamation($_GET['id']);
        }
        break;

    case 'contact': 
        include __DIR__ . '/views/contact.html'; // <-- double underscore
        break;

    case 'contact_success':
        include __DIR__ . '/views/index.html';
        break;

    default:
        // Default to home/front page
        include __DIR__ . '/views/index.html';
}