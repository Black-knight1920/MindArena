<?php
// Include PHPMailer 5.2.x files (adjust path if needed)
require 'PHPMailer/class.phpmailer.php';
require 'PHPMailer/class.smtp.php';

// --- Connect to database using PDO ---
try {
    $conn = new PDO("mysql:host=localhost;dbname=projetj;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header("Location: forgot.html?error=db_connection");
    exit();
}

// --- Get email from POST ---
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";

// --- Check if user exists ---
try {
    $r = $conn->prepare("SELECT * FROM user WHERE email = :email");
    $r->execute(array(':email' => $email));

    if ($r->rowCount() == 0) {
        header("Location: forgot.html?error=email_not_found");
        exit();
    }

    $user = $r->fetch(PDO::FETCH_ASSOC);
    $namee = $user['name'];

    // --- Generate token (simple fallback method for PHP <7) ---
    if (function_exists('random_bytes')) {
        $token = bin2hex(random_bytes(16));
    } else {
        $token = bin2hex(substr(md5(uniqid(mt_rand(), true)), 0, 16));
    }

    $expire = date("Y-m-d H:i:s", time() + 3600);

    // --- Insert or update token ---
    $insert = $conn->prepare("REPLACE INTO password_resets (namee, token, expire) VALUES (:namee, :token, :expire)");
    $insert->execute(array(':namee' => $namee, ':token' => $token, ':expire' => $expire));

    // --- Build reset link (adjust to your path) ---
    $reset_link = "http://localhost/projet-Copie/resetmdp/reset_password.html?token=" . $token;

    // --- Configure and send email using PHPMailer ---
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

    if ($mail->Send()) {
        header("Location: forgot.html?error=link_sent");
        exit();
    } else {
        header("Location: forgot.html?error=none");
        exit();
    }

} catch (PDOException $e) {
    header("Location: forgot.html?error=db_error");
    exit();
}
?>
