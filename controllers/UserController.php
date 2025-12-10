<?php
require_once __DIR__ . '/../models/UserModel.php';

class UserController {

    // Show list
    public function index() {
        $model = new UserModel();
        $data['users'] = $model->getAllUsers();
        require __DIR__ . '/../views/backend/list.php';
    }

    // Handle delete request
    public function deleteUser() {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["user_id"])) {
        $userId = (int)$_POST["user_id"];
        $model = new UserModel();
        $model->deleteUser($userId);
    }
    header("Location: users.php");
    exit();
}
    public function updateUser() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_user'])) {
            // Validate admin session (PHP 5.3 compatible)
            if (session_id() === '') {
                session_start();
            }
            if (!isset($_SESSION["admin"])) {
                header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php");
                exit();
            }

            // Sanitize and validate input
            $id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
            $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $dob = filter_var(trim($_POST['date_naissance']), FILTER_SANITIZE_STRING);
            $donation = filter_var($_POST['donation'], FILTER_VALIDATE_FLOAT);
            $password = isset($_POST['password']) && !empty($_POST['password']) ? trim($_POST['password']) : null;

            // Validation
            if (!$id || !$name || !$email || !$dob || $donation === false) {
                header("Location: users.php?error=Invalid input data");
                exit();
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: users.php?error=Invalid email format");
                exit();
            }

            // Validate password if provided
            if ($password !== null && strlen($password) < 6) {
                header("Location: users.php?error=Password must be at least 6 characters");
                exit();
            }

            $model = new UserModel();
            $result = $model->updateUser($id, $name, $email, $dob, $donation, $password);

            if ($result) {
                header("Location: users.php?message=User updated successfully");
            } else {
                header("Location: users.php?error=Failed to update user");
            }
            exit();
        }
    }

    public function banUser() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ban_user'])) {
            // Validate admin session (PHP 5.3 compatible)
            if (session_id() === '') {
                session_start();
            }
            if (!isset($_SESSION["admin"])) {
                header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php");
                exit();
            }

            $userId = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
            
            if (!$userId) {
                header("Location: users.php?error=Invalid user ID");
                exit();
            }

            $model = new UserModel();
            $result = $model->banUser($userId);

            if ($result) {
                header("Location: users.php?message=User banned successfully");
            } else {
                header("Location: users.php?error=Failed to ban user. Please ensure the database migration has been run.");
            }
            exit();
        }
    }

    public function unbanUser() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['unban_user'])) {
            // Validate admin session (PHP 5.3 compatible)
            if (session_id() === '') {
                session_start();
            }
            if (!isset($_SESSION["admin"])) {
                header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php");
                exit();
            }

            $userId = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
            
            if (!$userId) {
                header("Location: users.php?error=Invalid user ID");
                exit();
            }

            $model = new UserModel();
            $result = $model->unbanUser($userId);

            if ($result) {
                header("Location: users.php?message=User unbanned successfully");
            } else {
                header("Location: users.php?error=Failed to unban user. Please ensure the database migration has been run.");
            }
            exit();
        }
    }
public function createUser() {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_user"])) {
        // Validate admin session (PHP 5.3 compatible)
        if (session_id() === '') {
            session_start();
        }
        if (!isset($_SESSION["admin"])) {
            header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php");
            exit();
        }

        // Sanitize and validate input
        $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST["mdp"]);
        $dob = filter_var(trim($_POST["date_naissance"]), FILTER_SANITIZE_STRING);
        $dateInscribed = date("Y-m-d");

        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($dob)) {
            header("Location: users.php?error=All fields are required");
            exit();
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: users.php?error=Invalid email format");
            exit();
        }

        // Validate password length
        if (strlen($password) < 6) {
            header("Location: users.php?error=Password must be at least 6 characters");
            exit();
        }

        // Hash password using MD5 (PHP 5.3 compatible)
        $hashedPassword = md5($password);

        // Initialize the model
        $model = new UserModel();
        $userCreated = $model->createUser($name, $email, $hashedPassword, $dob, $dateInscribed);

        if ($userCreated) {
            // Redirect back to the users page with a success message
            header("Location: users.php?message=User Created Successfully");
            exit();
        } else {
            // If user creation fails, redirect with error message
            header("Location: users.php?error=Error Creating User");
            exit();
        }
    }
}




}

// Routes

?>
