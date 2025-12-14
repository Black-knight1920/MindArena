<?php

class PasswordReset
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $username, string $token, DateTimeInterface $expiresAt): void
    {
        $sql = "
            REPLACE INTO password_resets (namee, token, expire)
            VALUES (:namee, :token, :expire)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':namee'  => $username,
            ':token'  => $token,
            ':expire' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function findByToken(string $token): ?array
    {
        $sql = "
            SELECT namee, token, expire
            FROM password_resets
            WHERE token = :token
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function deleteByToken(string $token): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute([':token' => $token]);
    }

    public function deleteByUsername(string $username): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE namee = :namee");
        $stmt->execute([':namee' => $username]);
    }
}
