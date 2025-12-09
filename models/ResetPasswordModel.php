<?php
require_once __DIR__ . '/database.php';

class ResetPasswordModel {

    public function getTokenInfo($token) {
        global $conn;

        $q = $conn->prepare("SELECT namee, expire FROM password_resets WHERE token = :token");
        $q->execute(array(':token' => $token));
        return $q->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($name, $newPassword) {
        global $conn;

        $hashed = md5($newPassword);

        $u = $conn->prepare("UPDATE user SET mdp = :mdp WHERE name = :name");
        return $u->execute(array(':mdp' => $hashed, ':name' => $name));
    }

    public function deleteToken($token) {
        global $conn;

        $d = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
        $d->execute(array(':token' => $token));
    }
}
?>
