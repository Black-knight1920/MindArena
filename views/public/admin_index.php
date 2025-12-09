<?php
require_once __DIR__ . '/../../config/bootstrap.php';

session_start();

// If admin session is not set, redirect to login
if (!isset($_SESSION["admin"])) {
    header("Location: " . BASE_URL . "/index.php?action=login");
    exit();
}

$controller = new DashboardController();
$controller->index();

