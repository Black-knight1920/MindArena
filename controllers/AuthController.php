<?php
// Enable errors TEMPORARILY during debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../models/User.php';

class AuthController {

    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login() {

        if (session_id() === '') {
            session_start();
        }

        $username = filter_var(trim($_POST["userl"]), FILTER_SANITIZE_STRING);
        $password = $_POST["mdpl"];

        // ----------------------
        // ADMIN LOGIN
        // ----------------------
        if ($this->user->loginAdmin($username, $password)) {

            $_SESSION["admin"] = $username;

            header("Location: http://127.0.0.1/project-MVC%20-%20Copie/admin_index.php");
            exit();
        }

        // ----------------------
        // USER LOGIN
        // ----------------------
        if ($this->user->loginUser($username, $password)) {

            $userData = $this->user->getUserByName($username);

            if (isset($userData['banned']) && $userData['banned'] == 1) {
                header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php?error=user_banned");
                exit();
            }

            $_SESSION["username"] = $userData["name"];
            $_SESSION["email"]    = $userData["email"];

            header("Location: http://127.0.0.1/project-MVC%20-%20Copie/user_home.php");
            exit();
        }

        // ----------------------
        // LOGIN FAILED
        // ----------------------
        header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php?error=user_not_found");
        exit();
    }

    public function signup() {

        if (session_id() === '') {
            session_start();
        }

        $name  = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $pass  = md5($_POST["mdp"]);  // not secure, but your choice
        $birth = $_POST["date"];
        $dateI = date("Y-m-d");

        // FIXED: defaults for donation, banned, admin
        $s = $this->user->signup($name, $email, $pass, $birth, $dateI);

        if ($s) {
            header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php?error=acces");
        } else {
            header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php?error=e_utiliser");
        }

        exit();
    }
}
?>
