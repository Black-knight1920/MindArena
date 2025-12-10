<?php
/**
 * Database Migration Script for Profile Picture
 * Run this file once to add the 'profile_picture' column to your user table
 * 
 * Usage: Navigate to http://127.0.0.1/project-MVC%20-%20Copie/run_profile_picture_migration.php in your browser
 */

// Start output buffering
ob_start();

require_once "models/database.php";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile Picture Migration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; border: 1px solid #bee5eb; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<?php
try {
    // Check if column already exists
    $checkColumn = $conn->query("SHOW COLUMNS FROM `user` LIKE 'profile_picture'");
    
    if ($checkColumn->rowCount() > 0) {
        echo "<div class='success'>";
        echo "<h2>✓ Migration Status</h2>";
        echo "<p><strong>The 'profile_picture' column already exists in the user table.</strong></p>";
        echo "<p>No migration needed. You can use the profile picture functionality.</p>";
        echo "</div>";
    } else {
        // Add profile_picture column
        $sql = "ALTER TABLE `user` 
                ADD COLUMN `profile_picture` VARCHAR(255) NULL 
                AFTER `banned`";
        
        $conn->exec($sql);
        
        echo "<div class='success'>";
        echo "<h2>✓ Migration Completed Successfully!</h2>";
        echo "<p><strong>The 'profile_picture' column has been added to the user table.</strong></p>";
        echo "<p>You can now upload and display profile pictures.</p>";
        echo "</div>";
    }
    
    // Create uploads directory if it doesn't exist
    $uploadDir = __DIR__ . '/uploads/profile_pics/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        echo "<div class='info'>";
        echo "<p><strong>Upload directory created:</strong> uploads/profile_pics/</p>";
        echo "</div>";
    } else {
        echo "<div class='info'>";
        echo "<p><strong>Upload directory already exists.</strong></p>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<h2>✗ Migration Error</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
    echo "<p><strong>Manual SQL to run:</strong></p>";
    echo "<pre style='background: #f4f4f4; padding: 10px; border-radius: 3px;'>";
    echo "ALTER TABLE `user` \n";
    echo "ADD COLUMN `profile_picture` VARCHAR(255) NULL \n";
    echo "AFTER `banned`;";
    echo "</pre>";
    echo "</div>";
}
?>
<hr>
<p><a href='user_home.php'>← Back to User Home</a></p>
</body>
</html>

