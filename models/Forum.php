<?php
// models/Forum.php

class Forum
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /* ---------------------------------------------------------
       COMPTE & LISTES
    --------------------------------------------------------- */

    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM forums";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    /**
     * Liste complète pour l’admin.
     */
    public function getAll(): array
    {
        $sql = "SELECT *
                FROM forums
                ORDER BY created_at DESC, id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Liste pour le front (home + /front/forums).
     * Tu peux filtrer/limiter ici si tu veux.
     */
    public function getAllFront(): array
    {
        $sql = "SELECT id, title, description, created_by, created_at
                FROM forums
                ORDER BY created_at DESC, id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Liste des forums créés par un utilisateur donné.
     */
    public function getByCreator(string $creator, int $limit = 50): array
    {
        $sql = "SELECT id, title, description, created_by, created_at
                FROM forums
                WHERE created_by = :creator
                ORDER BY created_at DESC, id DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':creator', $creator, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /* ---------------------------------------------------------
       CRUD
    --------------------------------------------------------- */

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM forums WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Création d'un forum (admin ou front).
     *
     * @param string      $title
     * @param string|null $description
     * @param string      $createdBy  Pseudo / nom du créateur
     */
    public function create(string $title, ?string $description, string $createdBy): bool
    {
        $normalizedCreator = $createdBy !== '' ? $createdBy : 'Anonyme';
        $this->ensureUserStatsExists($normalizedCreator);
        $descValue = ($description !== '' && $description !== null) ? $description : '';

        $sql = "INSERT INTO forums (title, description, created_by, created_at)
                VALUES (:title, :description, :created_by, NOW())";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':title'       => $title,
            ':description' => $descValue,
            ':created_by'  => $normalizedCreator,
        ]);
    }

    /**
     * Mise à jour d’un forum.
     * Si $createdBy est null, on ne touche pas à la colonne created_by.
     */
    public function update(
        int $id,
        string $title,
        ?string $description,
        ?string $createdBy = null
    ): bool {
        $descValue = ($description !== '' && $description !== null) ? $description : '';
        $params = [
            ':id'          => $id,
            ':title'       => $title,
            ':description' => $descValue,
        ];

        if ($createdBy !== null) {
            $normalizedCreator = $createdBy !== '' ? $createdBy : 'Anonyme';
            $this->ensureUserStatsExists($normalizedCreator);

            $sql = "UPDATE forums
                    SET title = :title,
                        description = :description,
                        created_by = :created_by
                    WHERE id = :id";
            $params[':created_by'] = $normalizedCreator;
        } else {
            $sql = "UPDATE forums
                    SET title = :title,
                        description = :description
                    WHERE id = :id";
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM forums WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    public function countByDay(int $days = 7): array
{
    $sql = "SELECT DATE(created_at) AS d, COUNT(*) AS c
            FROM forums
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY d ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':days', $days, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
public function getByIdWithStats(int $id): ?array
{
    $sql = "
        SELECT 
            f.*,
            COUNT(DISTINCT p.id) AS publications_count,
            COUNT(DISTINCT r.id) AS reports_count
        FROM forums f
        LEFT JOIN publications p ON p.forum_id = f.id
        LEFT JOIN reports r ON r.forum_id = f.id
        WHERE f.id = :id
        GROUP BY f.id
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $data ?: null;
}
public function getAllWithStats(): array
    {
        $sql = "
            SELECT 
                f.*,
                COUNT(DISTINCT p.id) AS publications_count,
                COUNT(DISTINCT r.id) AS reports_count
            FROM forums f
            LEFT JOIN publications p ON p.forum_id = f.id
            LEFT JOIN reports r ON r.forum_id = f.id
            GROUP BY f.id
            ORDER BY f.created_at DESC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
public function countForums(): int
    {
        $sql = "SELECT COUNT(*) FROM forums";
        return (int)$this->pdo->query($sql)->fetchColumn();
    }    

    /**
     * Ensure a minimal user_stats row exists for a username (to satisfy FK).
     */
    private function ensureUserStatsExists(string $username): void
    {
        if ($username === '' || $username === null) {
            return;
        }

        $sql = "INSERT INTO user_stats (username, reputation, forums_count, publications_count, created_at, updated_at)
                VALUES (:u, 0, 0, 0, NOW(), NOW())
                ON DUPLICATE KEY UPDATE username = username";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':u' => $username]);
    }
}
