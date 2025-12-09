<?php
require_once __DIR__ . '/../../config/bootstrap.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, send user to the right area.
if (isset($_SESSION['user']['role'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: ' . BASE_URL . '/admin.php?action=dashboard');
        exit;
    }
    header('Location: ' . BASE_URL . '/index.php?action=home');
    exit;
}

// Delegate login/signup to the unified auth views
header('Location: ' . BASE_URL . '/index.php?action=login');
exit;

