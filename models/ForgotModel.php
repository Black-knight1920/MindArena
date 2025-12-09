<?php
// Include your database connection
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../Services/libraries/PHPMailer/class.phpmailer.php';
require_once __DIR__ . '/../Services/libraries/PHPMailer/class.smtp.php';


class ForgotModel {

    // Function to process the password reset request
    public function processPasswordResetRequest($email) {
        // Establish database connection
        try {
            global $conn; // use the PDO connection from database.php
            // Check if the user exists
            $stmt = $conn->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->execute(array(':email' => $email));


            if ($stmt->rowCount() == 0) {
                return 'email_not_found'; // User not found
            }

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $namee = $user['name'];

            // Generate token
            if (function_exists('random_bytes')) {
                $token = bin2hex(random_bytes(16));
            } else {
                $token = bin2hex(substr(md5(uniqid(mt_rand(), true)), 0, 16));
            }
            $expire = date("Y-m-d H:i:s", time() + 3600);

            // Insert or update token in the password_resets table
            $stmt = $conn->prepare("REPLACE INTO password_resets (namee, token, expire) VALUES (:namee, :token, :expire)");
            $stmt->execute(array(':namee' => $namee, ':token' => $token, ':expire' => $expire));

            // Build reset link (absolute URL for emails)
            $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $reset_link = $scheme . "://" . $host . "/mindarena_forum/index.php?action=reset&token=" . urlencode($token);


            // Send email using PHPMailer
            $this->sendResetEmail($email, $namee, $reset_link);

            return 'success'; // Return success if email was sent successfully
        } catch (PDOException $e) {
            return 'db_error'; // Return error if database operation fails
        }
    }

    // Function to send the reset email using PHPMailer
    private function sendResetEmail($email, $namee, $reset_link) {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->isHTML(true);
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->Username = 'gheaithjbelli20@gmail.com';  // Your Gmail
        $mail->Password = 'mccozztxrlissesu';           // Your App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->SetFrom('gheaithjbelli20@gmail.com', 'MICK');
        $mail->AddAddress($email, $namee);

        $mail->Subject = 'Password Reset Request';
        $mail->Body = "Hello $namee,<br><br>
            To reset your password, click the link below:<br><br>
            <a href=\"$reset_link\">Reset Link</a><br><br>
            This link expires in 1 hour.<br><br>
            If you didn’t request this, ignore it.";

        if (!$mail->Send()) {
            // If sending fails
            throw new Exception('Email sending failed');
        }
    }
}
?>
