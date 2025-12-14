<?php
require_once __DIR__ . '/../Models/User.php';

// Initialize User model
$user = new User(); // Adjust if your constructor is different
$username = $_POST['username'];

// Check if username exists
if ($user->exists($username)) {
    echo 'taken';
} else {
    echo 'available';
}
