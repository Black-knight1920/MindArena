<?php

class Report
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Créer un nouveau signalement.
     *
     * $type      : 'forum' ou 'publication'
     * $targetId  : id du forum OU de la publication (selon $type)
     */
    public function create(string $type, int $targetId, string $reason, ?string $details = null): void
    {
        if ($type === 'forum') {
            $sql = "INSERT INTO reports
                        (target_type, forum_id, publication_id, reason, details, status, created_at)
                    VALUES
                        ('forum', :forum_id, NULL, :reason, :details, 'pending', NOW())";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':forum_id' => $targetId,
                ':reason'   => $reason,
                ':details'  => $details,
            ]);

        } elseif ($type === 'publication') {
            // On récupère le forum_id de la publication pour que reportList puisse construire le lien
            $sql = "INSERT INTO reports
                        (target_type, forum_id, publication_id, reason, details, status, created_at)
                    VALUES
                        ('publication',
                         (SELECT forum_id FROM publications WHERE id = :pub_id),
                         :pub_id,
                         :reason,
                         :details,
                         'pending',
                         NOW()
                        )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':pub_id'  => $targetId,
                ':reason'  => $reason,
                ':details' => $details,
            ]);

        } else {
            // type invalide → tu peux lever une exception si tu veux
            throw new InvalidArgumentException('Type de cible invalide pour Report::create');
        }
    }

    /**
     * Nombre total de signalements.
     */
    public function count(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM reports");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Nombre de signalements en attente.
     */
    public function countPending(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM reports WHERE status = 'pending'");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Mettre à jour le statut d’un signalement.
     * $status : 'pending', 'seen', 'resolved'
     */
    public function updateStatus(int $id, string $status): void
    {
        if (!in_array($status, ['pending', 'seen', 'resolved'], true)) {
            throw new InvalidArgumentException('Statut invalide pour Report::updateStatus');
        }

        $sql = "UPDATE reports
                SET status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':id'     => $id,
        ]);
    }

    /**
     * Récupère tous les reports + infos sur la cible
     * pour l’écran admin des signalements (reportList.php).
     *
     * Retourne un tableau d’array avec :
     * - id
     * - target_type
     * - forum_id
     * - publication_id
     * - reason
     * - details
     * - status
     * - created_at
     * - target_label     (nom du forum ou info publication)
     * - target_excerpt   (description forum ou contenu publication)
     */
    public function getAllWithTarget(): array
    {
        $sql = "
            SELECT
                r.id,
                r.target_type,
                r.forum_id,
                r.publication_id,
                r.reason,
                r.details,
                r.status,
                r.created_at,

                -- Forum fields
                f.title AS forum_title,
                f.description AS forum_description,

                -- Publication fields
                p.author AS publication_author,
                p.content AS publication_content,
                p.forum_id AS publication_forum_id,

                -- label pour l'admin
                CASE
                    WHEN r.target_type = 'forum' THEN
                        IFNULL(CONCAT('Forum #', f.id, ' – ', f.title), CONCAT('Forum #', r.forum_id))
                    WHEN r.target_type = 'publication' THEN
                        IFNULL(CONCAT('Pub #', p.id, ' – ', LEFT(p.author, 30)),
                               CONCAT('Publication #', r.publication_id))
                    ELSE
                        'Cible inconnue'
                END AS target_label,

                -- extrait pour l'admin
                CASE
                    WHEN r.target_type = 'forum' THEN
                        f.description
                    WHEN r.target_type = 'publication' THEN
                        p.content
                    ELSE
                        NULL
                END AS target_excerpt

            FROM reports r
            LEFT JOIN forums f
                ON r.forum_id = f.id
            LEFT JOIN publications p
                ON r.publication_id = p.id

            ORDER BY r.created_at DESC, r.id DESC
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un signalement avec toutes les informations des tables liées.
     */
    public function getByIdWithDetails(int $id): ?array
    {
        $sql = "
            SELECT 
                r.*,
                -- Forum details
                f.id AS forum_id_full,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at,
                -- Publication details
                p.id AS publication_id_full,
                p.author AS publication_author,
                p.content AS publication_content,
                p.created_at AS publication_created_at,
                p.forum_id AS publication_forum_id,
                -- Forum de la publication (si le report est sur une publication)
                pf.id AS pub_forum_id,
                pf.title AS pub_forum_title
            FROM reports r
            LEFT JOIN forums f ON r.forum_id = f.id
            LEFT JOIN publications p ON r.publication_id = p.id
            LEFT JOIN forums pf ON p.forum_id = pf.id
            WHERE r.id = :id
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Récupère les signalements avec comptage des signalements par type.
     */
    public function getAllWithStats(): array
    {
        $sql = "
            SELECT 
                r.*,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at,
                p.author AS publication_author,
                p.content AS publication_content,
                p.forum_id AS publication_forum_id,
                p.created_at AS publication_created_at,
                pf.title AS pub_forum_title,
                pf.id AS pub_forum_id,
                -- Statistiques
                (SELECT COUNT(*) FROM reports r2 WHERE r2.target_type = r.target_type) AS total_same_type,
                (SELECT COUNT(*) FROM reports r3 WHERE r3.status = r.status) AS total_same_status
            FROM reports r
            LEFT JOIN forums f ON r.forum_id = f.id
            LEFT JOIN publications p ON r.publication_id = p.id
            LEFT JOIN forums pf ON p.forum_id = pf.id
            ORDER BY r.created_at DESC, r.id DESC
        ";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère tous les signalements avec toutes les informations complètes des tables liées.
     */
    public function getAllWithFullDetails(): array
    {
        $sql = "
            SELECT 
                r.*,
                -- Forum details (si le report est sur un forum)
                f.id AS forum_id_full,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at,
                -- Publication details (si le report est sur une publication)
                p.id AS publication_id_full,
                p.author AS publication_author,
                p.content AS publication_content,
                p.created_at AS publication_created_at,
                -- Forum de la publication (si le report est sur une publication)
                pf.id AS pub_forum_id,
                pf.title AS pub_forum_title,
                pf.description AS pub_forum_description,
                pf.created_by AS pub_forum_created_by,
                -- Statistiques de l'auteur/créateur
                CASE 
                    WHEN r.target_type = 'forum' THEN
                        (SELECT us.reputation FROM user_stats us WHERE us.username = f.created_by)
                    WHEN r.target_type = 'publication' THEN
                        (SELECT us.reputation FROM user_stats us WHERE us.username = p.author)
                    ELSE NULL
                END AS target_author_reputation,
                -- Comptage des autres signalements sur la même cible
                CASE 
                    WHEN r.target_type = 'forum' THEN
                        (SELECT COUNT(*) FROM reports r2 WHERE r2.forum_id = r.forum_id AND r2.id != r.id)
                    WHEN r.target_type = 'publication' THEN
                        (SELECT COUNT(*) FROM reports r3 WHERE r3.publication_id = r.publication_id AND r3.id != r.id)
                    ELSE 0
                END AS other_reports_count
            FROM reports r
            LEFT JOIN forums f ON r.forum_id = f.id AND r.target_type = 'forum'
            LEFT JOIN publications p ON r.publication_id = p.id AND r.target_type = 'publication'
            LEFT JOIN forums pf ON p.forum_id = pf.id
            ORDER BY r.created_at DESC, r.id DESC
        ";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère les signalements en attente avec toutes les informations.
     */
    public function getPendingWithFullDetails(): array
    {
        $sql = "
            SELECT 
                r.*,
                f.id AS forum_id_full,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                p.id AS publication_id_full,
                p.author AS publication_author,
                p.content AS publication_content,
                p.forum_id AS publication_forum_id,
                pf.title AS pub_forum_title
            FROM reports r
            LEFT JOIN forums f ON r.forum_id = f.id AND r.target_type = 'forum'
            LEFT JOIN publications p ON r.publication_id = p.id AND r.target_type = 'publication'
            LEFT JOIN forums pf ON p.forum_id = pf.id
            WHERE r.status = 'pending'
            ORDER BY r.created_at DESC, r.id DESC
        ";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère les signalements par statut avec toutes les informations.
     */
    public function getByStatusWithFullDetails(string $status): array
    {
        $sql = "
            SELECT 
                r.*,
                f.id AS forum_id_full,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                p.id AS publication_id_full,
                p.author AS publication_author,
                p.content AS publication_content,
                p.forum_id AS publication_forum_id,
                pf.title AS pub_forum_title
            FROM reports r
            LEFT JOIN forums f ON r.forum_id = f.id AND r.target_type = 'forum'
            LEFT JOIN publications p ON r.publication_id = p.id AND r.target_type = 'publication'
            LEFT JOIN forums pf ON p.forum_id = pf.id
            WHERE r.status = :status
            ORDER BY r.created_at DESC, r.id DESC
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Supprime un signalement par son ID.
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM reports WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
