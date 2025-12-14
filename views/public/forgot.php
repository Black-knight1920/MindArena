<?php
require_once __DIR__ . '/../../config/bootstrap.php';
// Simple redirect helper for the forgot-password page
header('Location: ' . BASE_URL . '/index.php?action=forgot');
exit;

