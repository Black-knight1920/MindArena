<?php
session_start();

// Ensure the user is an admin
if (!isset($_SESSION["admin"])) {
    header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php");
    exit();
}

require_once "controllers/UserController.php";

// Initialize the controller
$controller = new UserController();

// Handle different actions based on POST parameters
if (isset($_POST['edit_user'])) {
    // If the form was submitted for editing a user, call the updateUser method
    $controller->updateUser();
} elseif (isset($_POST['delete_user'])) {
    // If the form was submitted for deleting a user, call the deleteUser method
    $controller->deleteUser();
} elseif (isset($_POST['create_user'])) {
    // If the form was submitted for creating a new user, call the createUser method
    $controller->createUser();
} elseif (isset($_POST['ban_user'])) {
    // If the form was submitted for banning a user, call the banUser method
    $controller->banUser();
} elseif (isset($_POST['unban_user'])) {
    // If the form was submitted for unbanning a user, call the unbanUser method
    $controller->unbanUser();
} else {
    // Default action is to show the user list
    $controller->index();
}
?>
