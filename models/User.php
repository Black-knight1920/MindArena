<?php

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retourne un classement fictif des utilisateurs basé sur leurs contributions
     */
    public function getFakeRanking(int $limit = 10): array
    {
        $sql = "
            SELECT 
                name,
                SUM(forums_count) AS forums,
                SUM(publications_count) AS publications,
                (SUM(forums_count) * 3 + SUM(publications_count)) AS score
            FROM (
                SELECT created_by AS name, COUNT(*) AS forums_count, 0 AS publications_count
                FROM forums
                WHERE created_by IS NOT NULL AND created_by <> ''
                GROUP BY created_by
                
                UNION ALL
                
                SELECT author AS name, 0 AS forums_count, COUNT(*) AS publications_count
                FROM publications
                WHERE author IS NOT NULL AND author <> ''
                GROUP BY author
            ) AS contributions
            GROUP BY name
            ORDER BY score DESC, name ASC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Compte le nombre total d'utilisateurs distincts
     */
    public function countUsers(): int
    {
        $sql = "
            SELECT COUNT(DISTINCT name) AS total
            FROM (
                SELECT created_by AS name FROM forums WHERE created_by IS NOT NULL AND created_by <> ''
                UNION
                SELECT author AS name FROM publications WHERE author IS NOT NULL AND author <> ''
            ) AS users
        ";

        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Récupère les statistiques d'un utilisateur depuis user_stats avec jointure
     * Si l'utilisateur n'existe pas dans user_stats, calcule depuis forums et publications.
     */
    public function getUserStats(string $username): ?array
    {
        // D'abord, essayer de récupérer depuis user_stats
        $sql = "
            SELECT 
                us.*,
                COUNT(DISTINCT f.id) AS actual_forums_count,
                COUNT(DISTINCT p.id) AS actual_publications_count
            FROM user_stats us
            LEFT JOIN forums f ON f.created_by = us.username
            LEFT JOIN publications p ON p.author = us.username
            WHERE us.username = :username
            GROUP BY us.id
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result;
        }
        
        // Si pas dans user_stats, calculer depuis forums et publications
        $sql = "
            SELECT 
                :username AS username,
                COUNT(DISTINCT f.id) AS forums_count,
                COUNT(DISTINCT p.id) AS publications_count,
                (COUNT(DISTINCT f.id) * 3 + COUNT(DISTINCT p.id)) AS reputation,
                MIN(COALESCE(f.created_at, p.created_at)) AS first_activity,
                MAX(COALESCE(f.created_at, p.created_at)) AS last_activity
            FROM (SELECT 1) AS dummy
            LEFT JOIN forums f ON f.created_by = :username
            LEFT JOIN publications p ON p.author = :username
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Récupère tous les utilisateurs avec leurs statistiques complètes depuis user_stats
     * et les tables forums/publications avec jointures.
     */
    public function getAllUsersWithStats(int $limit = 50): array
    {
        $sql = "
            SELECT 
                COALESCE(us.username, u.username) AS username,
                COALESCE(us.reputation, u.reputation) AS reputation,
                COALESCE(us.forums_count, u.forums_count, 0) AS forums_count,
                COALESCE(us.publications_count, u.publications_count, 0) AS publications_count,
                COALESCE(us.created_at, u.first_activity) AS created_at,
                COALESCE(us.updated_at, u.last_activity) AS updated_at
            FROM (
                SELECT 
                    created_by AS username,
                    COUNT(*) AS forums_count,
                    0 AS publications_count,
                    MIN(created_at) AS first_activity,
                    MAX(created_at) AS last_activity,
                    (COUNT(*) * 3) AS reputation
                FROM forums
                WHERE created_by IS NOT NULL AND created_by <> ''
                GROUP BY created_by
                
                UNION ALL
                
                SELECT 
                    author AS username,
                    0 AS forums_count,
                    COUNT(*) AS publications_count,
                    MIN(created_at) AS first_activity,
                    MAX(created_at) AS last_activity,
                    COUNT(*) AS reputation
                FROM publications
                WHERE author IS NOT NULL AND author <> ''
                GROUP BY author
            ) AS u
            LEFT JOIN user_stats us ON us.username = u.username
            GROUP BY COALESCE(us.username, u.username)
            ORDER BY reputation DESC, username ASC
            LIMIT :limit
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère un utilisateur avec toutes ses statistiques complètes et activités.
     */
    public function getUserWithFullDetails(string $username): ?array
    {
        $sql = "
            SELECT 
                COALESCE(us.username, :username) AS username,
                COALESCE(us.reputation, 
                    (SELECT COUNT(*) * 3 FROM forums WHERE created_by = :username) +
                    (SELECT COUNT(*) FROM publications WHERE author = :username)
                ) AS reputation,
                COALESCE(us.forums_count, (SELECT COUNT(*) FROM forums WHERE created_by = :username), 0) AS forums_count,
                COALESCE(us.publications_count, (SELECT COUNT(*) FROM publications WHERE author = :username), 0) AS publications_count,
                COALESCE(us.created_at, 
                    (SELECT MIN(created_at) FROM (
                        SELECT created_at FROM forums WHERE created_by = :username
                        UNION ALL
                        SELECT created_at FROM publications WHERE author = :username
                    ) AS activities)
                ) AS created_at,
                COALESCE(us.updated_at,
                    (SELECT MAX(created_at) FROM (
                        SELECT created_at FROM forums WHERE created_by = :username
                        UNION ALL
                        SELECT created_at FROM publications WHERE author = :username
                    ) AS activities)
                ) AS updated_at,
                -- Statistiques supplémentaires
                (SELECT COUNT(DISTINCT forum_id) FROM publications WHERE author = :username) AS forums_participated,
                (SELECT COUNT(*) FROM reports WHERE 
                    (target_type = 'forum' AND forum_id IN (SELECT id FROM forums WHERE created_by = :username))
                    OR (target_type = 'publication' AND publication_id IN (SELECT id FROM publications WHERE author = :username))
                ) AS reports_received
            FROM user_stats us
            WHERE us.username = :username
            LIMIT 1
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si pas dans user_stats, calculer depuis les tables
        if (!$result) {
            $sql = "
                SELECT 
                    :username AS username,
                    (SELECT COUNT(*) * 3 FROM forums WHERE created_by = :username) +
                    (SELECT COUNT(*) FROM publications WHERE author = :username) AS reputation,
                    (SELECT COUNT(*) FROM forums WHERE created_by = :username) AS forums_count,
                    (SELECT COUNT(*) FROM publications WHERE author = :username) AS publications_count,
                    (SELECT MIN(created_at) FROM (
                        SELECT created_at FROM forums WHERE created_by = :username
                        UNION ALL
                        SELECT created_at FROM publications WHERE author = :username
                    ) AS activities) AS created_at,
                    (SELECT MAX(created_at) FROM (
                        SELECT created_at FROM forums WHERE created_by = :username
                        UNION ALL
                        SELECT created_at FROM publications WHERE author = :username
                    ) AS activities) AS updated_at,
                    (SELECT COUNT(DISTINCT forum_id) FROM publications WHERE author = :username) AS forums_participated,
                    (SELECT COUNT(*) FROM reports WHERE 
                        (target_type = 'forum' AND forum_id IN (SELECT id FROM forums WHERE created_by = :username))
                        OR (target_type = 'publication' AND publication_id IN (SELECT id FROM publications WHERE author = :username))
                    ) AS reports_received
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':username' => $username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $result ?: null;
    }

    /**
     * Récupère tous les utilisateurs avec leurs activités récentes (forums et publications).
     */
    public function getAllUsersWithActivities(int $limit = 50): array
    {
        $sql = "
            SELECT 
                u.username,
                u.reputation,
                u.forums_count,
                u.publications_count,
                u.created_at,
                u.updated_at,
                (SELECT f.title FROM forums f WHERE f.created_by = u.username ORDER BY f.created_at DESC LIMIT 1) AS last_forum_title,
                (SELECT f.created_at FROM forums f WHERE f.created_by = u.username ORDER BY f.created_at DESC LIMIT 1) AS last_forum_date,
                (SELECT p.content FROM publications p WHERE p.author = u.username ORDER BY p.created_at DESC LIMIT 1) AS last_publication_content,
                (SELECT p.created_at FROM publications p WHERE p.author = u.username ORDER BY p.created_at DESC LIMIT 1) AS last_publication_date
            FROM (
                SELECT 
                    COALESCE(us.username, u.username) AS username,
                    COALESCE(us.reputation, u.reputation) AS reputation,
                    COALESCE(us.forums_count, u.forums_count, 0) AS forums_count,
                    COALESCE(us.publications_count, u.publications_count, 0) AS publications_count,
                    COALESCE(us.created_at, u.first_activity) AS created_at,
                    COALESCE(us.updated_at, u.last_activity) AS updated_at
                FROM (
                    SELECT 
                        created_by AS username,
                        COUNT(*) AS forums_count,
                        0 AS publications_count,
                        MIN(created_at) AS first_activity,
                        MAX(created_at) AS last_activity,
                        (COUNT(*) * 3) AS reputation
                    FROM forums
                    WHERE created_by IS NOT NULL AND created_by <> ''
                    GROUP BY created_by
                    
                    UNION ALL
                    
                    SELECT 
                        author AS username,
                        0 AS forums_count,
                        COUNT(*) AS publications_count,
                        MIN(created_at) AS first_activity,
                        MAX(created_at) AS last_activity,
                        COUNT(*) AS reputation
                    FROM publications
                    WHERE author IS NOT NULL AND author <> ''
                    GROUP BY author
                ) AS u
                LEFT JOIN user_stats us ON us.username = u.username
                GROUP BY COALESCE(us.username, u.username)
            ) AS u
            ORDER BY u.reputation DESC, u.username ASC
            LIMIT :limit
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère le profil complet d'un utilisateur incluant :
     * - ses statistiques (user_stats ou calculées)
     * - la liste des forums créés
     * - la liste des publications avec infos du forum
     */
    public function getCompleteProfile(string $username, int $limitForums = 50, int $limitPubs = 50): array
    {
        $profile = $this->getUserWithFullDetails($username) ?? ['username' => $username];

        // Forums créés par l'utilisateur
        $sqlF = "SELECT id, title, description, created_at FROM forums WHERE created_by = :username ORDER BY created_at DESC LIMIT :limit";
        $stmtF = $this->pdo->prepare($sqlF);
        $stmtF->bindValue(':username', $username, PDO::PARAM_STR);
        $stmtF->bindValue(':limit', $limitForums, PDO::PARAM_INT);
        $stmtF->execute();
        $forums = $stmtF->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Publications écrites par l'utilisateur, avec infos du forum
        $sqlP = "SELECT p.*, f.id AS forum_id, f.title AS forum_title, f.created_by AS forum_created_by
                 FROM publications p
                 LEFT JOIN forums f ON f.id = p.forum_id
                 WHERE p.author = :username
                 ORDER BY p.created_at DESC
                 LIMIT :limit";
        $stmtP = $this->pdo->prepare($sqlP);
        $stmtP->bindValue(':username', $username, PDO::PARAM_STR);
        $stmtP->bindValue(':limit', $limitPubs, PDO::PARAM_INT);
        $stmtP->execute();
        $publications = $stmtP->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [
            'profile' => $profile,
            'forums' => $forums,
            'publications' => $publications,
        ];
    }

    /**
     * Récupère une timeline d'activités (forums + publications) triée par date.
     */
    public function getRecentActivity(string $username, int $limit = 50): array
    {
        $sql = "
            SELECT 'forum' AS type, f.id AS id, f.title AS title, f.description AS content, f.created_at AS created_at
            FROM forums f
            WHERE f.created_by = :username

            UNION ALL

            SELECT 'publication' AS type, p.id AS id, NULL AS title, p.content AS content, p.created_at AS created_at
            FROM publications p
            WHERE p.author = :username

            ORDER BY created_at DESC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

