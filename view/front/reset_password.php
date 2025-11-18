<?php
// --- Connect to DB using PDO ---
try {
    $conn = new PDO("mysql:host=localhost;dbname=projetj;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: "error",
        title: "Server Error",
        text: "Database connection failed."
    }).then(() => {
        window.location.href = "../login/login.html";
    });
    </script>
    </body>
    </html>';
    exit();
}

// --- Get POST data ---
$token = isset($_POST['token']) ? trim($_POST['token']) : '';
$newPassword = isset($_POST['newmdp']) ? $_POST['newmdp'] : '';

if (empty($token) || empty($newPassword)) {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: "error",
        title: "Missing Information",
        text: "Token or password is missing."
    }).then(() => {
        window.history.back();
    });
    </script>
    </body>
    </html>';
    exit();
}

try {
    // --- Check if token exists ---
    $q = $conn->prepare("SELECT namee, expire FROM password_resets WHERE token = :token");
    $q->execute(array(':token' => $token));
    $row = $q->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo '<!DOCTYPE html>
        <html>
        <head>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
        Swal.fire({
            icon: "error",
            title: "Invalid Token",
            text: "The reset link is invalid or has already been used."
        }).then(() => {
            window.location.href = "../login/login.html";
        });
        </script>
        </body>
        </html>';
        exit();
    }

    $name = $row['namee'];
    $expire = strtotime($row['expire']);

    // --- Check expiration ---
    if (time() > $expire) {
        echo '<!DOCTYPE html>
        <html>
        <head>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
        Swal.fire({
            icon: "error",
            title: "Expired Token",
            text: "The reset link has expired. Please request a new one."
        }).then(() => {
            window.location.href = "../login/login.html";
        });
        </script>
        </body>
        </html>';
        exit();
    }

    // --- Hash password (MD5 for compatibility) ---
    $hashedPassword = md5($newPassword);

    // --- Update user password ---
    $update = $conn->prepare("UPDATE user SET mdp = :mdp WHERE name = :name");
    $update->execute(array(':mdp' => $hashedPassword, ':name' => $name));

    // --- Delete token to prevent reuse ---
    $delete = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
    $delete->execute(array(':token' => $token));

    // --- Success message ---
    echo '<!DOCTYPE html>
    <html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: "success",
        title: "Password Changed",
        text: "You can now log in with your new password"
    }).then(() => {
        window.location.href = "../login/login.html";
    });
    </script>
    </body>
    </html>';
    exit();

} catch (PDOException $e) {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: "error",
        title: "Server Error",
        text: "A database error occurred. Please try again later."
    }).then(() => {
        window.history.back();
    });
    </script>
    </body>
    </html>';
    exit();
}
?>
