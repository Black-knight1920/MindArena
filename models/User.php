<?php

require_once "database.php"; // brings $conn variable

class User {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /* ============================
       USER LOGIN
       ============================ */
    public function loginUser($username, $password) {

        $stmt = $this->conn->prepare(
            "SELECT * FROM user WHERE name = :name"
        );
        $stmt->execute(array(":name" => $username));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        // banned?
        if (isset($user['banned']) && $user['banned'] == 1) {
            return false;
        }

        // MD5 password match
        return (md5($password) === $user['mdp']);
    }


    /* ============================
       ADMIN LOGIN
       ============================ */
    public function loginAdmin($username, $password) {

        $stmt = $this->conn->prepare(
            "SELECT * FROM user 
             WHERE name = :name 
             AND mdp = :mdp
             AND admin = 1"
        );

        $stmt->execute(array(
            ":name" => $username,
            ":mdp"  => md5($password)
        ));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /* ============================
       SIGNUP (PHP 5.3 SAFE)
       ============================ */
    public function signup($name, $email, $password, $birth, $dateI) {

        $stmt = $this->conn->prepare(
            "INSERT INTO user 
            (name, email, mdp, `date-naissance`, `date-inscrit`, donation, banned, profile_picture, admin)
            VALUES 
            (:name, :email, :mdp, :birth, :dateI, 0, 0, NULL, 0)"
        );

        $stmt->execute(array(
            ":name"  => $name,
            ":email" => $email,
            ":mdp"   => $password,
            ":birth" => $birth,
            ":dateI" => $dateI
        ));

        // Get last inserted user's ID
        $userId = $this->conn->lastInsertId();

        // Insert into profile table
        $stmt2 = $this->conn->prepare(
            "INSERT INTO profile (user_id, name, email)
             VALUES (:id, :name, :email)"
        );

        return $stmt2->execute(array(
            ":id"    => $userId,
            ":name"  => $name,
            ":email" => $email
        ));
    }


    /* ============================
       CHECK IF USER EXISTS
       ============================ */
    public function exists($username) {
        $stmt = $this->conn->prepare(
            "SELECT id FROM user WHERE name = :name"
        );
        $stmt->execute(array(':name' => $username));
        return ($stmt->rowCount() > 0);
    }


    /* ============================
       GET USER BY NAME
       ============================ */
    public function getUserByName($username) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM user WHERE name = :name"
        );
        $stmt->execute(array(":name" => $username));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
