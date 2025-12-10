<?php
// Completely disable error display
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

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
    // If database connection fails - log error instead of displaying
    error_log("Database connection failed: " . $e->getMessage());
    // Return JSON error if called via AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'Database connection failed'));
        exit();
    }
    die("Database connection failed");
}
