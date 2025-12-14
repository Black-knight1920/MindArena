<?php

require_once __DIR__ . '/database.php'; // brings $conn variable

class User {

    private $conn;

    public function __construct() {
        // use your connection
        global $conn;
        $this->conn = $conn;
    }

    public function loginUser($usernameOrEmail, $passwordHash, $passwordPlain = '') {
        // Accept either hashed (md5) or legacy plain passwords; allow login via name or email
        $stmt = $this->conn->prepare(
            "SELECT * FROM user WHERE (name = :id OR email = :id) AND (mdp = :mdp OR mdp = :plain)"
        );
        $stmt->execute(array(":id" => $usernameOrEmail, ":mdp" => $passwordHash, ":plain" => $passwordPlain));
        return $stmt->rowCount() > 0;
    }

    public function loginAdmin($username, $passwordHash, $passwordPlain = '') {
        $stmt = $this->conn->prepare(
            "SELECT * FROM admin WHERE name = :name AND (mdpa = :mdp OR mdpa = :plain)"
        );
        $stmt->execute(array(":name" => $username, ":mdp" => $passwordHash, ":plain" => $passwordPlain));
        return $stmt->rowCount() > 0;
    }

    public function signup($name, $email, $password, $birth, $dateI) {
    // First, insert the user into the user table
    $stmt = $this->conn->prepare(
        "INSERT INTO user (name, email, mdp, `date-naissance`, `date-inscrit`)
         VALUES (:name, :email, :mdp, :dateN, :dateI)"
    );
    $stmt->execute(array(
        ":name" => $name,
        ":email" => $email,
        ":mdp"   => $password,
        ":dateN" => $birth,
        ":dateI" => $dateI
    ));

    // After the user is created, get the last inserted user ID
    $userId = $this->conn->lastInsertId();

    // Now insert the profile data into the profile table
    $stmtProfile = $this->conn->prepare(
        "INSERT INTO profile (user_id, name, email) 
         VALUES (:user_id, :name, :email)"
    );
    return $stmtProfile->execute(array(
        ":user_id" => $userId,
        ":name" => $name,
        ":email" => $email
    ));
}

    public function exists($username){
    $stmt = $this->conn->prepare("SELECT * FROM user WHERE name = :name");
    $stmt->execute(array(':name' => $username));
    return $stmt->rowCount() > 0;
    }

    public function emailExists(string $email): bool {
        $stmt = $this->conn->prepare("SELECT 1 FROM user WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return (bool)$stmt->fetchColumn();
    }

    public function getUserByName($username) {
    $stmt = $this->conn->prepare("SELECT * FROM user WHERE name = :name");
    $stmt->execute(array(":name" => $username));
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function countUsers(): int {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM user");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Temporary ranking placeholder to avoid missing method errors.
     * Returns an empty array; update with real logic if needed.
     */
    public function getFakeRanking(): array {
        return [];
    }


}
?>
