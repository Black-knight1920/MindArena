<?php

require_once __DIR__ . '/../Models/ResetPasswordModel.php';

class ResetPasswordController {

    public function reset() {

        $token = isset($_POST['token']) ? trim($_POST['token']) : '';
        $password = isset($_POST['newmdp']) ? trim($_POST['newmdp']) : '';

        $loginUrl = "/mindarena_forum/index.php?action=login";
        $resetUrl = "/mindarena_forum/index.php?action=reset";

        if (empty($token) || empty($password)) {
            $this->alertAndRedirect("Missing Information", "Token or password is missing.", $loginUrl);
        }

        $model = new ResetPasswordModel();
        $tokenData = $model->getTokenInfo($token);

        if (!$tokenData) {
            $this->alertAndRedirect("Invalid Token", "This reset link is invalid or was already used.", $loginUrl);
        }

        // Check expiration
        if (time() > strtotime($tokenData['expire'])) {
            $this->alertAndRedirect("Expired Token", "The reset link has expired. Please request a new one.", $loginUrl);
        }

        // Update password
        if ($model->updatePassword($tokenData['namee'], $password)) {
            $model->deleteToken($token);
            $this->alertAndRedirect("Password Changed", "You can now log in with your new password.", $loginUrl, "success");
        } else {
            $this->alertAndRedirect("Error", "Could not update your password.", $resetUrl);
        }
    }

    private function alertAndRedirect($title, $msg, $redirect, $icon = "error") {
        echo "
        <html><head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head><body>
        <script>
        Swal.fire({
            icon: '$icon',
            title: '$title',
            text: '$msg'
        }).then(() => {
            window.location.href = '$redirect';
        });
        </script>
        </body></html>";
        exit;
    }
}
?>
