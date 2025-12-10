<?php
require_once "database.php";

class UserModel {
    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;
    }

    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM user ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($userId) {
        $sql = "SELECT * FROM user WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteProfileByUserId($userId) {
        $sql = "DELETE FROM profile WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function deleteUser($userId) {
        // First delete profile(s) linked to user
        $this->deleteProfileByUserId($userId);

        // Then delete the user
        $sql = "DELETE FROM user WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function updateUser($id, $name, $email, $dob, $donation, $password = null) {
        // Build the SQL query dynamically based on whether password is provided
        if ($password !== null && $password !== '') {
            // Hash password using MD5 (PHP 5.3 compatible)
            $hashedPassword = md5($password);
            $sql = "UPDATE user SET name = :name, email = :email, `date-naissance` = :dob, donation = :donation, mdp = :password WHERE id = :id";
        } else {
            $sql = "UPDATE user SET name = :name, email = :email, `date-naissance` = :dob, donation = :donation WHERE id = :id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':donation', $donation);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($password !== null && $password !== '') {
            $stmt->bindParam(':password', $hashedPassword);
        }
        
        return $stmt->execute();
    }

    public function updateUserProfile($userId, $username, $email, $password = null, $profilePicture = null) {
        try {
            // Check if profile_picture column exists
            $hasProfilePictureColumn = false;
            try {
                $checkColumn = $this->db->query("SHOW COLUMNS FROM `user` LIKE 'profile_picture'");
                $hasProfilePictureColumn = $checkColumn->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Error checking profile_picture column: " . $e->getMessage());
                // Continue without profile picture column
            }
            
            // Build SQL query dynamically
            $updates = array();
            $params = array();
            
            $updates[] = "name = :username";
            $params[':username'] = $username;
            
            $updates[] = "email = :email";
            $params[':email'] = $email;
            
            if ($password !== null && $password !== '') {
                $hashedPassword = md5($password);
                $updates[] = "mdp = :password";
                $params[':password'] = $hashedPassword;
            }
            
            if ($profilePicture !== null && $hasProfilePictureColumn) {
                $updates[] = "profile_picture = :profile_picture";
                $params[':profile_picture'] = $profilePicture;
            } elseif ($profilePicture !== null && !$hasProfilePictureColumn) {
                error_log("Warning: profile_picture column does not exist, but picture was uploaded. Run migration script.");
            }
            
            if (empty($updates)) {
                error_log("No updates to perform");
                return false;
            }
            
            $sql = "UPDATE user SET " . implode(', ', $updates) . " WHERE id = :id";
            $params[':id'] = $userId;
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $result = $stmt->execute();
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQL execution failed: " . print_r($errorInfo, true));
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in updateUserProfile: " . $e->getMessage());
            error_log("SQL: " . (isset($sql) ? $sql : 'N/A'));
            error_log("Params: " . print_r($params, true));
            return false;
        } catch (Exception $e) {
            error_log("General error in updateUserProfile: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($userId, $newPassword) {
        // Hash password using MD5 (PHP 5.3 compatible)
        $hashedPassword = md5($newPassword);
        $sql = "UPDATE user SET mdp = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function banUser($userId) {
        try {
            // Check if banned column exists
            $checkColumn = $this->db->query("SHOW COLUMNS FROM `user` LIKE 'banned'");
            if ($checkColumn->rowCount() == 0) {
                // Column doesn't exist, return false with a message
                error_log("Error: 'banned' column does not exist in user table. Please run the database migration script.");
                return false;
            }
            
            $sql = "UPDATE user SET banned = 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in banUser: " . $e->getMessage());
            return false;
        }
    }

    public function unbanUser($userId) {
        try {
            // Check if banned column exists
            $checkColumn = $this->db->query("SHOW COLUMNS FROM `user` LIKE 'banned'");
            if ($checkColumn->rowCount() == 0) {
                // Column doesn't exist, return false with a message
                error_log("Error: 'banned' column does not exist in user table. Please run the database migration script.");
                return false;
            }
            
            $sql = "UPDATE user SET banned = 0 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in unbanUser: " . $e->getMessage());
            return false;
        }
    }
    public function createUser($name, $email, $password, $dob, $dateInscribed) {
    // Start a transaction to ensure both user and profile are inserted together
    $this->db->beginTransaction();

    try {
        // Step 1: Insert into the 'user' table
        $sql = "INSERT INTO user (name, email, mdp, `date-naissance`, `date-inscrit`) 
                VALUES (:name, :email, :password, :dob, :date_inscribed)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':date_inscribed', $dateInscribed);
        $stmt->execute();

        // Get the last inserted user ID
        $userId = $this->db->lastInsertId();

        // Step 2: Insert into the 'profile' table
        // (Assuming 'bio' is nullable, and you can modify this to add more fields as necessary)
        $profileSql = "INSERT INTO profile (user_id, name, email, bio) 
                       VALUES (:user_id, :name, :email, :bio)";
        $profileStmt = $this->db->prepare($profileSql);
        $profileStmt->bindParam(':user_id', $userId);
        $profileStmt->bindParam(':name', $name);
        $profileStmt->bindParam(':email', $email);
        $bio = '';  // Default bio if not provided, or modify as needed
        $profileStmt->bindParam(':bio', $bio);
        $profileStmt->execute();

        // Commit the transaction
        $this->db->commit();

        return true;
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        $this->db->rollBack();
        throw $e;
    }
}


    

}

?>
