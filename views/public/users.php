<?php
require_once __DIR__ . '/../../config/bootstrap.php';

session_start();

// Ensure the user is an admin
if (!isset($_SESSION["admin"])) {
    header("Location: " . BASE_URL . "/index.php?action=login");
    exit();
}

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
} else {
    // Default action is to show the user list
    $controller->index();
}

