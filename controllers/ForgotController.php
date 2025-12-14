<?php
// Import model for database connection and logic
require_once __DIR__ . '/../Models/ForgotModel.php';

class ForgotController {

    public function showForgotForm() {
        // Load the forgot form view
        include VIEW_PATH . '/frontend/forgot.php';
    }

    public function handleForgotRequest() {
        // Get email from POST request
        $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";

        // Call the model to process the reset request
        $forgotModel = new ForgotModel();
        $result = $forgotModel->processPasswordResetRequest($email);

        // Handle the result and redirect accordingly
        $forgotUrl = "/mindarena_forum/index.php?action=forgot";
        if ($result === 'success') {
            header("Location: {$forgotUrl}&error=link_sent");
            exit();
        }

        header("Location: {$forgotUrl}&error=" . $result);
        exit();
    }
}
?>
