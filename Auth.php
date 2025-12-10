<?php

require_once "controllers/AuthController.php";

$auth = new AuthController();

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

header("Location: views/frontend/login.php");
exit();

?>
