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
        $sql = "INSERT INTO forums (title, description, created_by, created_at)
                VALUES (:title, :description, :created_by, NOW())";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':title'       => $title,
            ':description' => $description !== '' ? $description : null,
            ':created_by'  => $createdBy !== '' ? $createdBy : 'Anonyme',
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
        $params = [
            ':id'          => $id,
            ':title'       => $title,
            ':description' => $description !== '' ? $description : null,
        ];

        if ($createdBy !== null) {
            $sql = "UPDATE forums
                    SET title = :title,
                        description = :description,
                        created_by = :created_by
                    WHERE id = :id";
            $params[':created_by'] = $createdBy !== '' ? $createdBy : 'Anonyme';
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
}

