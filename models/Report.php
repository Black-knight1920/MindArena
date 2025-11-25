<?php

class Report
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Créer un signalement
     * $targetType = 'forum' ou 'publication'
     * $targetId   = id du forum ou id de la publication
     */
    public function create(string $targetType, int $targetId, string $reason, string $details = ''): bool
    {
        if (!in_array($targetType, ['forum', 'publication'], true)) {
            throw new InvalidArgumentException("Type de contenu invalide pour le report.");
        }

        $forumId       = null;
        $publicationId = null;

        if ($targetType === 'forum') {
            $forumId = $targetId;
        } else {
            $publicationId = $targetId;
        }

        $sql = "INSERT INTO reports (target_type, forum_id, publication_id, reason, details)
                VALUES (:target_type, :forum_id, :publication_id, :reason, :details)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':target_type'    => $targetType,
            ':forum_id'       => $forumId,
            ':publication_id' => $publicationId,
            ':reason'         => $reason,
            ':details'        => $details,
        ]);
    }

    /**
     * Liste complète des signalements avec jointure vers forums/publications
     */
    public function getAllWithTargets(): array
    {
        $sql = "
            SELECT
                r.*,
                f.title        AS forum_title,
                p.content      AS publication_content,
                p.author       AS publication_author,
                p.forum_id     AS publication_forum_id
            FROM reports r
            LEFT JOIN forums f
                ON r.forum_id = f.id
            LEFT JOIN publications p
                ON r.publication_id = p.id
            ORDER BY r.created_at DESC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) AS c FROM reports";
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        return (int)($row['c'] ?? 0);
    }

    public function countPending(): int
    {
        $sql = "SELECT COUNT(*) AS c FROM reports WHERE status = 'pending'";
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        return (int)($row['c'] ?? 0);
    }

    /**
     * Met à jour le statut : pending | seen | resolved
     */
    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['pending', 'seen', 'resolved'], true)) {
            throw new InvalidArgumentException("Statut de report invalide.");
        }

        $sql = "UPDATE reports SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $id,
        ]);
    }
}
