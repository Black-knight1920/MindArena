<?php
// Completely disable error display
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

require_once "database.php";

class AdminModel {
    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;
    }

    public function getAdminByName($username) {
        try {
            $sql = "SELECT * FROM admin WHERE name = :name";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $username);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If admin found but email column doesn't exist, set a default empty value
            if ($admin && is_array($admin)) {
                if (!isset($admin['email'])) {
                    $admin['email'] = '';
                }
            }
            
            return $admin;
        } catch (PDOException $e) {
            error_log("Database error in getAdminByName: " . $e->getMessage());
            return false;
        }
    }

    public function updateAdmin($username, $newUsername, $email = '', $password = null) {
        try {
            // Admin table only has name and mdpa columns, so we don't update email
            if ($password !== null && $password !== '') {
                // Hash password using MD5 (PHP 5.3 compatible)
                $hashedPassword = md5($password);
                $sql = "UPDATE admin SET name = :new_name, mdpa = :password WHERE name = :old_name";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':new_name', $newUsername);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':old_name', $username);
            } else {
                // Only update username, keep password unchanged
                $sql = "UPDATE admin SET name = :new_name WHERE name = :old_name";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':new_name', $newUsername);
                $stmt->bindParam(':old_name', $username);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in updateAdmin: " . $e->getMessage());
            return false;
        }
    }
}
