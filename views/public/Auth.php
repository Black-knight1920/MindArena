<?php

require_once __DIR__ . '/../../config/bootstrap.php';

$auth = new AuthController();

// Check if form_type exists
if (isset($_POST["form_type"])) {

    if ($_POST["form_type"] === "login") {
        $auth->login();
        exit();
    }

    if ($_POST["form_type"] === "signup") {
        $auth->signup();
        exit();
    }
}

// If someone accesses this page directly, redirect to login
header("Location: " . BASE_URL . "/index.php?action=login");
exit();

