<?php

require_once __DIR__ . '/../Models/User.php';

class AuthController {

    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login() {
    session_start(); // start PHP session

    $username = trim($_POST["userl"]);
    $passwordPlain = trim($_POST["mdpl"]);
    $password = md5($passwordPlain);

    // ----------------------
    // ADMIN LOGIN
    // ----------------------
    if ($this->user->loginAdmin($username, $password, $passwordPlain)) {
        $_SESSION['user'] = [
            'username' => $username,
            'email'    => null,
            'role'     => 'admin'
        ];
        header("Location: /mindarena_forum/admin.php?action=dashboard");
        exit();
    }

    // ----------------------
    // USER LOGIN
    // ----------------------
    if ($this->user->loginUser($username, $password, $passwordPlain)) {
        // Get user info from DB
        $userData = $this->user->getUserByName($username);

        // Store user session in unified structure
        $_SESSION['user'] = [
            'username' => $userData["name"],
            'email'    => $userData["email"],
            'role'     => 'user'
        ];

        // Redirect to user home page
        header("Location: /mindarena_forum/index.php?action=home");
        exit();
    }

    // ----------------------
    // LOGIN FAILED
    // ----------------------
        header("Location: /mindarena_forum/index.php?action=login&error=user_not_found");
    exit();
}

    public function signup() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $name  = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $pass  = md5($_POST["mdp"]);
        $birth = trim($_POST["date"]);
        $dateI = date("Y-m-d");

        // Vérifier doublons avant insert pour éviter l'erreur 1062
        if ($this->user->emailExists($email) || $this->user->exists($name)) {
            header("Location: /mindarena_forum/index.php?action=login&error=e_utiliser");
            exit();
        }

        $s = $this->user->signup($name, $email, $pass, $birth, $dateI);

        if ($s) {
            // Auto login after signup
            $_SESSION['user'] = [
                'username' => $name,
                'email'    => $email,
                'role'     => 'user'
            ];
            header("Location: /mindarena_forum/index.php?action=home");
        } else {
            header("Location: /mindarena_forum/index.php?action=login&error=e_utiliser");
        }

        exit();
    }
}
?>
