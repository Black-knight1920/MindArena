<?php

$host = "localhost";
$dbname = "projetj";
$username = "root";
$password = "";

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // PDO error mode => Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // If database connection fails
    die("Database connection failed: " . $e->getMessage());
}
?>
