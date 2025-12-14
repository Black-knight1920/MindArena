<?php

class Profile
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByUserId(int $userId): ?array
    {
        $sql = "
            SELECT 
                id,
                user_id,
                name,
                email,
                bio
            FROM profile
            WHERE user_id = :user_id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function upsert(int $userId, string $name, string $email, ?string $bio = null): void
    {
        // Replace ensures profile stays 1-per-user_id
        $sql = "
            REPLACE INTO profile (user_id, name, email, bio)
            VALUES (:user_id, :name, :email, :bio)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':name'    => $name,
            ':email'   => $email,
            ':bio'     => $bio,
        ]);
    }
}
