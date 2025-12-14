<?php

class Notification
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /** Récupérer toutes les notifications avec jointures */
    public function getAllWithJoins(int $limit = 50): array
    {
        $sql = "
            SELECT 
                n.*,

                -- FORUM
                f.id AS forum_id,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_author,

                -- PUBLICATION
                p.id AS publication_id,
                p.content AS publication_content,
                p.author AS publication_author,
                p.forum_id AS publication_forum_id,

                -- REPORT
                r.id AS report_id,
                r.reason AS report_reason,
                r.status AS report_status,
                r.forum_id AS report_forum_id,
                r.publication_id AS report_publication_id

            FROM notifications n
            
            LEFT JOIN reports r 
                ON n.type = 'report'
                AND (
                    n.url LIKE CONCAT('%report_id=', r.id, '%')
                )

            LEFT JOIN forums f 
                ON (n.type = 'forum' AND n.url LIKE CONCAT('%forum_id=', f.id, '%'))
                OR (r.forum_id = f.id)

            LEFT JOIN publications p 
                ON (n.type = 'publication' AND n.url LIKE CONCAT('%publication_id=', p.id, '%'))
                OR (r.publication_id = p.id)

            ORDER BY n.created_at DESC
            LIMIT :lim
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }


    /** Non lues */
    public function countUnread(): int
    {
        return (int)$this->pdo->query("
            SELECT COUNT(*) FROM notifications WHERE is_read = 0
        ")->fetchColumn();
    }

    /** Dernières notifications */
    public function latest(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM notifications 
            ORDER BY created_at DESC 
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Marquer lue */
    public function markRead(int $id): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE notifications SET is_read = 1 WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
    }

    /** Créer une notif */
    public function create(string $type, string $title, ?string $message = null, ?string $url = null): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (type, title, message, url, is_read, created_at)
            VALUES (:t, :ti, :m, :u, 0, NOW())
        ");
        $stmt->execute([
            't'  => $type,
            'ti' => $title,
            'm'  => $message,
            'u'  => $url
        ]);
    }

    /**
     * Récupérer toutes les notifications avec jointures complètes incluant user_stats
     * et tous les autres tables (forums, publications, reports, user_stats)
     */
    public function getAllWithFullJoins(int $limit = 50): array
    {
        $sql = "
            SELECT 
                n.id AS notification_id,
                n.type AS notification_type,
                n.title AS notification_title,
                n.message AS notification_message,
                n.url AS notification_url,
                n.is_read AS notification_is_read,
                n.created_at AS notification_created_at,

                -- FORUM
                f.id AS forum_id,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at,

                -- PUBLICATION
                p.id AS publication_id,
                p.content AS publication_content,
                p.author AS publication_author,
                p.forum_id AS publication_forum_id,
                p.created_at AS publication_created_at,

                -- REPORT
                r.id AS report_id,
                r.target_type AS report_target_type,
                r.reason AS report_reason,
                r.status AS report_status,
                r.forum_id AS report_forum_id,
                r.publication_id AS report_publication_id,
                r.created_at AS report_created_at,

                -- USER_STATS pour le créateur du forum (fallbacks pour éviter NULL)
                COALESCE(us_forum.username, f.created_by) AS forum_creator_username,
                COALESCE(us_forum.reputation, 0) AS forum_creator_reputation,
                COALESCE(us_forum.forums_count, 0) AS forum_creator_forums_count,
                COALESCE(us_forum.publications_count, 0) AS forum_creator_publications_count,
                us_forum.created_at AS forum_creator_stats_created_at,
                us_forum.updated_at AS forum_creator_stats_updated_at,

                -- USER_STATS pour l'auteur de la publication (fallbacks pour éviter NULL)
                COALESCE(us_pub.username, p.author) AS publication_author_username,
                COALESCE(us_pub.reputation, 0) AS publication_author_reputation,
                COALESCE(us_pub.forums_count, 0) AS publication_author_forums_count,
                COALESCE(us_pub.publications_count, 0) AS publication_author_publications_count,
                us_pub.created_at AS publication_author_stats_created_at,
                us_pub.updated_at AS publication_author_stats_updated_at

            FROM notifications n
            
            LEFT JOIN reports r 
                ON n.type = 'report'
                AND (n.url LIKE CONCAT('%report_id=', r.id, '%'))

            LEFT JOIN forums f 
                ON (n.type = 'forum' AND n.url LIKE CONCAT('%forum_id=', f.id, '%'))
                OR (r.forum_id = f.id AND r.target_type = 'forum')

            LEFT JOIN publications p 
                ON (n.type = 'publication' AND n.url LIKE CONCAT('%publication_id=', p.id, '%'))
                OR (r.publication_id = p.id AND r.target_type = 'publication')

            LEFT JOIN user_stats us_forum 
                ON f.created_by = us_forum.username

            LEFT JOIN user_stats us_pub 
                ON p.author = us_pub.username

            ORDER BY n.created_at DESC
            LIMIT :lim
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupérer les notifications non lues avec toutes les jointures (incluant user_stats)
     */
    public function getUnreadWithFullJoins(int $limit = 50): array
    {
        $sql = "
            SELECT 
                n.id AS notification_id,
                n.type AS notification_type,
                n.title AS notification_title,
                n.message AS notification_message,
                n.url AS notification_url,
                n.is_read AS notification_is_read,
                n.created_at AS notification_created_at,

                -- FORUM
                f.id AS forum_id,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at,

                -- PUBLICATION
                p.id AS publication_id,
                p.content AS publication_content,
                p.author AS publication_author,
                p.forum_id AS publication_forum_id,
                p.created_at AS publication_created_at,

                -- REPORT
                r.id AS report_id,
                r.target_type AS report_target_type,
                r.reason AS report_reason,
                r.status AS report_status,
                r.forum_id AS report_forum_id,
                r.publication_id AS report_publication_id,
                r.created_at AS report_created_at,

                -- USER_STATS pour le créateur du forum (fallbacks pour éviter NULL)
                COALESCE(us_forum.username, f.created_by) AS forum_creator_username,
                COALESCE(us_forum.reputation, 0) AS forum_creator_reputation,
                COALESCE(us_forum.forums_count, 0) AS forum_creator_forums_count,
                COALESCE(us_forum.publications_count, 0) AS forum_creator_publications_count,
                us_forum.created_at AS forum_creator_stats_created_at,
                us_forum.updated_at AS forum_creator_stats_updated_at,

                -- USER_STATS pour l'auteur de la publication (fallbacks pour éviter NULL)
                COALESCE(us_pub.username, p.author) AS publication_author_username,
                COALESCE(us_pub.reputation, 0) AS publication_author_reputation,
                COALESCE(us_pub.forums_count, 0) AS publication_author_forums_count,
                COALESCE(us_pub.publications_count, 0) AS publication_author_publications_count,
                us_pub.created_at AS publication_author_stats_created_at,
                us_pub.updated_at AS publication_author_stats_updated_at

            FROM notifications n
            
            LEFT JOIN reports r 
                ON n.type = 'report'
                AND (n.url LIKE CONCAT('%report_id=', r.id, '%'))

            LEFT JOIN forums f 
                ON (n.type = 'forum' AND n.url LIKE CONCAT('%forum_id=', f.id, '%'))
                OR (r.forum_id = f.id AND r.target_type = 'forum')

            LEFT JOIN publications p 
                ON (n.type = 'publication' AND n.url LIKE CONCAT('%publication_id=', p.id, '%'))
                OR (r.publication_id = p.id AND r.target_type = 'publication')

            LEFT JOIN user_stats us_forum 
                ON f.created_by = us_forum.username

            LEFT JOIN user_stats us_pub 
                ON p.author = us_pub.username

            WHERE n.is_read = 0
            ORDER BY n.created_at DESC
            LIMIT :lim
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupérer une notification par ID avec toutes les jointures
     */
    public function getByIdWithFullJoins(int $id): ?array
    {
        $sql = "
            SELECT 
                n.id AS notification_id,
                n.type AS notification_type,
                n.title AS notification_title,
                n.message AS notification_message,
                n.url AS notification_url,
                n.is_read AS notification_is_read,
                n.created_at AS notification_created_at,

                -- FORUM
                f.id AS forum_id,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at,

                -- PUBLICATION
                p.id AS publication_id,
                p.content AS publication_content,
                p.author AS publication_author,
                p.forum_id AS publication_forum_id,
                p.created_at AS publication_created_at,

                -- REPORT
                r.id AS report_id,
                r.target_type AS report_target_type,
                r.reason AS report_reason,
                r.details AS report_details,
                r.status AS report_status,
                r.forum_id AS report_forum_id,
                r.publication_id AS report_publication_id,
                r.created_at AS report_created_at,

                -- USER_STATS pour le créateur du forum (fallbacks pour éviter NULL)
                COALESCE(us_forum.username, f.created_by) AS forum_creator_username,
                COALESCE(us_forum.reputation, 0) AS forum_creator_reputation,
                COALESCE(us_forum.forums_count, 0) AS forum_creator_forums_count,
                COALESCE(us_forum.publications_count, 0) AS forum_creator_publications_count,
                us_forum.created_at AS forum_creator_stats_created_at,
                us_forum.updated_at AS forum_creator_stats_updated_at,

                -- USER_STATS pour l'auteur de la publication (fallbacks pour éviter NULL)
                COALESCE(us_pub.username, p.author) AS publication_author_username,
                COALESCE(us_pub.reputation, 0) AS publication_author_reputation,
                COALESCE(us_pub.forums_count, 0) AS publication_author_forums_count,
                COALESCE(us_pub.publications_count, 0) AS publication_author_publications_count,
                us_pub.created_at AS publication_author_stats_created_at,
                us_pub.updated_at AS publication_author_stats_updated_at

            FROM notifications n
            
            LEFT JOIN reports r 
                ON n.type = 'report'
                AND (n.url LIKE CONCAT('%report_id=', r.id, '%'))

            LEFT JOIN forums f 
                ON (n.type = 'forum' AND n.url LIKE CONCAT('%forum_id=', f.id, '%'))
                OR (r.forum_id = f.id AND r.target_type = 'forum')

            LEFT JOIN publications p 
                ON (n.type = 'publication' AND n.url LIKE CONCAT('%publication_id=', p.id, '%'))
                OR (r.publication_id = p.id AND r.target_type = 'publication')

            LEFT JOIN user_stats us_forum 
                ON f.created_by = us_forum.username

            LEFT JOIN user_stats us_pub 
                ON p.author = us_pub.username

            WHERE n.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
