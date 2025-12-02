<?php

class UserStatsService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Vue globale : nombre total de contributeurs, forums, publications.
     */
    public function getGlobalOverview(): array
    {
        // contributeurs distincts basés sur created_by (forums) et author (publications)
        $sql = "
            SELECT COUNT(DISTINCT u.name) AS total_users,
                   SUM(u.forums)         AS total_forums,
                   SUM(u.publications)   AS total_publications
            FROM (
                SELECT created_by AS name,
                       COUNT(*)   AS forums,
                       0          AS publications
                FROM forums
                WHERE created_by IS NOT NULL AND created_by <> ''
                GROUP BY created_by

                UNION ALL

                SELECT author AS name,
                       0      AS forums,
                       COUNT(*) AS publications
                FROM publications
                WHERE author IS NOT NULL AND author <> ''
                GROUP BY author
            ) AS u
        ";

        $stmt = $this->pdo->query($sql);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_users'        => (int)($row['total_users'] ?? 0),
            'total_forums'       => (int)($row['total_forums'] ?? 0),
            'total_publications' => (int)($row['total_publications'] ?? 0),
        ];
    }

    /**
     * Top contributeurs : pseudo + nb forums/publications + score.
     */
    public function getTopContributors(int $limit = 20): array
    {
        $sql = "
            SELECT
                u.name,
                SUM(u.forums)       AS forums_count,
                SUM(u.publications) AS publications_count,
                (SUM(u.forums) * 3 + SUM(u.publications) * 1) AS score
            FROM (
                SELECT created_by AS name,
                       COUNT(*)   AS forums,
                       0          AS publications
                FROM forums
                WHERE created_by IS NOT NULL AND created_by <> ''
                GROUP BY created_by

                UNION ALL

                SELECT author AS name,
                       0      AS forums,
                       COUNT(*) AS publications
                FROM publications
                WHERE author IS NOT NULL AND author <> ''
                GROUP BY author
            ) AS u
            GROUP BY u.name
            ORDER BY score DESC, name ASC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère les statistiques globales avec jointures pour plus d'efficacité.
     * Utilise des jointures au lieu d'UNION pour de meilleures performances.
     */
    public function getGlobalOverviewWithJoins(): array
    {
        $sql = "
            SELECT 
                COUNT(DISTINCT COALESCE(f.created_by, p.author)) AS total_users,
                COUNT(DISTINCT f.id) AS total_forums,
                COUNT(DISTINCT p.id) AS total_publications,
                COUNT(DISTINCT CASE WHEN f.created_by IS NOT NULL AND f.created_by <> '' THEN f.created_by END) AS forum_creators,
                COUNT(DISTINCT CASE WHEN p.author IS NOT NULL AND p.author <> '' THEN p.author END) AS publication_authors
            FROM (SELECT 1) AS dummy
            LEFT JOIN forums f ON f.created_by IS NOT NULL AND f.created_by <> ''
            LEFT JOIN publications p ON p.author IS NOT NULL AND p.author <> ''
        ";
        
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        
        return [
            'total_users'        => (int)($row['total_users'] ?? 0),
            'total_forums'       => (int)($row['total_forums'] ?? 0),
            'total_publications' => (int)($row['total_publications'] ?? 0),
            'forum_creators'     => (int)($row['forum_creators'] ?? 0),
            'publication_authors' => (int)($row['publication_authors'] ?? 0),
        ];
    }

    /**
     * Top contributeurs avec jointures vers user_stats si disponible.
     */
    public function getTopContributorsWithUserStats(int $limit = 20): array
    {
        $sql = "
            SELECT 
                COALESCE(us.username, u.name) AS name,
                COALESCE(us.forums_count, u.forums_count, 0) AS forums_count,
                COALESCE(us.publications_count, u.publications_count, 0) AS publications_count,
                COALESCE(us.reputation, (u.forums_count * 3 + u.publications_count)) AS score,
                us.reputation AS user_stats_reputation,
                us.created_at AS user_stats_created_at,
                us.updated_at AS user_stats_updated_at
            FROM (
                SELECT 
                    created_by AS name,
                    COUNT(*) AS forums_count,
                    0 AS publications_count
                FROM forums
                WHERE created_by IS NOT NULL AND created_by <> ''
                GROUP BY created_by
                
                UNION ALL
                
                SELECT 
                    author AS name,
                    0 AS forums_count,
                    COUNT(*) AS publications_count
                FROM publications
                WHERE author IS NOT NULL AND author <> ''
                GROUP BY author
            ) AS u
            LEFT JOIN user_stats us ON us.username = u.name
            GROUP BY COALESCE(us.username, u.name)
            ORDER BY score DESC, name ASC
            LIMIT :limit
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
