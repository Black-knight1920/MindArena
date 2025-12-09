<?php
require_once __DIR__ . '/../Models/UserModel.php';

class UserController {

    // Show list
    public function index() {
        $model = new UserModel();
        $data['users'] = $model->getAllUsers();
        require VIEW_PATH . '/backend/list.php';
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
        $id = $_POST['user_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $dob = $_POST['date_naissance'];
        $donation = $_POST['donation'];

        $model = new UserModel();
        $model->updateUser($id, $name, $email, $dob, $donation);

        header("Location: users.php");
        exit();
    }
}
public function createUser() {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_user"])) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = md5($_POST["mdp"]);  // Use md5 or password_hash() for better security
        $dob = $_POST["date_naissance"];
        $dateInscribed = date("Y-m-d");

        // Initialize the model
        $model = new UserModel();
        $userCreated = $model->createUser($name, $email, $password, $dob, $dateInscribed);

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
