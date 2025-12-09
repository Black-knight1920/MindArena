<?php

class Account
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Finds a user by email or username.
     */
    public function findByIdentifier(string $identifier): ?array
    {
        $sql = "
            SELECT 
                id,
                name,
                email,
                mdp,
                `date-naissance` AS birth_date,
                `date-inscrit` AS signup_date,
                donation
            FROM user
            WHERE email = :identifier OR name = :identifier
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':identifier' => $identifier]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT 
                id,
                name,
                email,
                mdp,
                `date-naissance` AS birth_date,
                `date-inscrit` AS signup_date,
                donation
            FROM user
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Returns user row when credentials match (password stored as MD5 hash).
     */
    public function verifyCredentials(string $identifier, string $password): ?array
    {
        $user = $this->findByIdentifier($identifier);
        if (!$user) {
            return null;
        }

        $stored = (string) ($user['mdp'] ?? '');

        // Modern hash check
        if ($stored && password_get_info($stored)['algo'] !== 0 && password_verify($password, $stored)) {
            // Rehash if needed
            if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
                $this->updatePassword((int) $user['id'], $password);
            }
            return $user;
        }

        // Legacy MD5 fallback with upgrade
        if ($stored && hash_equals($stored, md5($password))) {
            $this->updatePassword((int) $user['id'], $password);
            return $this->findById((int)$user['id']); // return refreshed row with new hash
        }

        return null;
    }

    public function updatePassword(int $userId, string $rawPassword): void
    {
        $hash = password_hash($rawPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE user SET mdp = :mdp WHERE id = :id");
        $stmt->execute([
            ':mdp' => $hash,
            ':id'  => $userId,
        ]);
    }

    /**
     * Creates a user and returns the inserted user row (id + columns) or null on failure.
     */
    public function createUser(string $name, string $email, string $rawPassword, string $birthDate, int $donation = 0): ?array
    {
        $hash = password_hash($rawPassword, PASSWORD_DEFAULT);
        $today = (new DateTimeImmutable())->format('Y-m-d');

        $sql = "
            INSERT INTO user (name, email, mdp, `date-naissance`, `date-inscrit`, donation)
            VALUES (:name, :email, :mdp, :birth, :signup, :donation)
        ";

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':mdp'      => $hash,
            ':birth'    => $birthDate,
            ':signup'   => $today,
            ':donation' => $donation,
        ]);

        if (!$ok) {
            return null;
        }

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id);
    }
}
