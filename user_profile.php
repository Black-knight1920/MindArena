<?php
// Completely disable error display
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

// Start output buffering
ob_start();

// PHP 5.3 compatible session check
if (session_id() === '') {
    session_start();
}

// Clear any output
ob_clean();

// Ensure the user is logged in
if (!isset($_SESSION["username"])) {
    ob_clean();
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
    exit();
}

try {
    require_once "controllers/UserProfileController.php";
    
    $controller = new UserProfileController();
    
    // Handle different actions
    if (isset($_POST['action'])) {
        ob_clean();
        if ($_POST['action'] === 'update') {
            $controller->updateProfile();
        } elseif ($_POST['action'] === 'get') {
            header('Content-Type: application/json');
            $controller->getProfile();
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Invalid action'));
        }
    } else {
        ob_clean();
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'No action specified'));
    }
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    header('Content-Type: application/json');
    $errorMessage = 'Server error. Please try again.';
    // In production, don't expose error details to user
    // But log them for debugging
    error_log("Error in user_profile.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(array('success' => false, 'message' => $errorMessage));
}

