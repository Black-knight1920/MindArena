<?php
/**
 * Database Migration Script
 * Run this file once to add the 'banned' column to your user table
 * 
 * Usage: Navigate to http://127.0.0.1/project-MVC%20-%20Copie/run_migration.php in your browser
 */

// Start output buffering for better error handling
ob_start();

require_once "models/database.php";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Migration</title>
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
    $checkColumn = $conn->query("SHOW COLUMNS FROM `user` LIKE 'banned'");
    
    if ($checkColumn->rowCount() > 0) {
        echo "<div class='success'>";
        echo "<h2>✓ Migration Status</h2>";
        echo "<p><strong>The 'banned' column already exists in the user table.</strong></p>";
        echo "<p>No migration needed. You can use the ban/unban functionality.</p>";
        echo "</div>";
    } else {
        // Add banned column
        $sql = "ALTER TABLE `user` 
                ADD COLUMN `banned` TINYINT(1) NOT NULL DEFAULT 0 
                AFTER `donation`";
        
        $conn->exec($sql);
        
        // Add index for better performance
        try {
            $conn->exec("CREATE INDEX `idx_banned` ON `user` (`banned`)");
        } catch (PDOException $e) {
            // Index might already exist, that's okay
            if (strpos($e->getMessage(), 'Duplicate key name') === false && 
                strpos($e->getMessage(), 'Duplicate') === false) {
                throw $e;
            }
        }
        
        echo "<div class='success'>";
        echo "<h2>✓ Migration Completed Successfully!</h2>";
        echo "<p><strong>The 'banned' column has been added to the user table.</strong></p>";
        echo "<p>You can now use the ban/unban functionality in the admin panel.</p>";
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
    echo "ADD COLUMN `banned` TINYINT(1) NOT NULL DEFAULT 0 \n";
    echo "AFTER `donation`;\n\n";
    echo "CREATE INDEX `idx_banned` ON `user` (`banned`);";
    echo "</pre>";
    echo "</div>";
}

?>
<hr>
<p><a href='users.php'>← Back to User Management</a></p>
</body>
</html>

