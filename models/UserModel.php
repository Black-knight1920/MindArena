<?php
require_once __DIR__ . '/database.php';

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
    public function updateUser($id, $name, $email, $dob, $donation) {
    $sql = "UPDATE user SET name = :name, email = :email, `date-naissance` = :dob, donation = :donation WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':dob', $dob);
    $stmt->bindParam(':donation', $donation);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
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
