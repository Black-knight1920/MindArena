<?php
require "db.php";

$sql = "INSERT INTO reclamation(full_name,email,subject,message)
        VALUES (?,?,?,?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $_POST['full_name'],
    $_POST['email'],
    $_POST['subject'],
    $_POST['message']
]);

header("Location: ../contact.html?success=1");
exit;
