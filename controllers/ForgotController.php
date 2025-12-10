<?php
// Completely disable error display
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

// Import model for database connection and logic
require_once __DIR__ . '/../models/ForgotModel.php';

if (isset($_GET['action']) && $_GET['action'] === 'handleForgotRequest') {
    $controller = new ForgotController();
    $controller->handleForgotRequest();
    exit();
}

class ForgotController {

    public function showForgotForm() {
        // Load the forgot form view
        include __DIR__ . '/../views/frontend/forgot.php';
    }

    public function handleForgotRequest() {
        // PHP 5.3 compatible session check
        if (session_id() === '') {
            session_start();
        }
        
        // Get email from POST request
        $email = isset($_POST["email"]) ? filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL) : "";

        // Call the model to process the reset request
        $forgotModel = new ForgotModel();
        $result = $forgotModel->processPasswordResetRequest($email);

        // Handle the result and redirect accordingly
        if ($result === 'success') {
            header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/forgot.php?error=link_sent");
            exit();
        } else {
            header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/forgot.php?error=" . $result);
            exit();
        }
    }
}
?>
