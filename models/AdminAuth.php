<?php

class AdminAuth
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByName(string $name): ?array
    {
        $sql = "SELECT name, mdpa FROM admin WHERE name = :name LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Returns admin row when credentials match (password stored as MD5 hash).
     */
    public function verifyCredentials(string $name, string $password): ?array
    {
        $admin = $this->findByName($name);
        if (!$admin) {
            return null;
        }

        $hash = md5($password);
        if (hash_equals($admin['mdpa'], $hash)) {
            return $admin;
        }

        return null;
    }
}
