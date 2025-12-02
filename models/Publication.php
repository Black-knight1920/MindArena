<?php

class Publication {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function count() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM publications");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Alias pour count() utilisé par AdminController
     */
    public function countPublications(): int {
        return $this->count();
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT p.*, f.title AS forum_title
            FROM publications p
            LEFT JOIN forums f ON f.id = p.forum_id
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Alias pour getAll() utilisé par AdminController
     */
    public function getAllWithForum(): array {
        return $this->getAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM publications WHERE id=?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Récupère une publication avec les informations du forum.
     */
    public function getByIdWithForum($id) {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*,
                f.id AS forum_id,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at
            FROM publications p
            LEFT JOIN forums f ON f.id = p.forum_id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }


    public function getByForum($forum_id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, f.title AS forum_title
            FROM publications p
            LEFT JOIN forums f ON f.id = p.forum_id
            WHERE p.forum_id = ?
            ORDER BY p.id DESC
        ");
        $stmt->execute([$forum_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère toutes les publications d'un forum avec les infos complètes du forum.
     */
    public function getByForumWithDetails($forum_id) {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*,
                f.id AS forum_id,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at
            FROM publications p
            INNER JOIN forums f ON f.id = p.forum_id
            WHERE p.forum_id = ?
            ORDER BY p.created_at DESC, p.id DESC
        ");
        $stmt->execute([$forum_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function create($forum_id, $author, $content) {
        $stmt = $this->pdo->prepare("
            INSERT INTO publications (forum_id, author, content, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        // Store 'Anonyme' if author is empty/null
        $authorValue = !empty(trim($author)) ? $author : 'Anonyme';
        return $stmt->execute([$forum_id, $authorValue, $content]);
    }

    public function update($id, $forum_id, $author, $content) {
        $stmt = $this->pdo->prepare("
            UPDATE publications
            SET forum_id=?, author=?, content=?
            WHERE id=?
        ");
        // Store 'Anonyme' if author is empty/null
        $authorValue = !empty(trim($author)) ? $author : 'Anonyme';
        return $stmt->execute([$forum_id, $authorValue, $content, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM publications WHERE id=?");
        return $stmt->execute([$id]);
    }
    public function countByDay(int $days = 7): array
{
    $sql = "SELECT DATE(created_at) AS d, COUNT(*) AS c
            FROM publications
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY d ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':days', $days, PDO::PARAM_INT);
    $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère toutes les publications avec toutes les informations du forum.
     */
    public function getAllWithFullDetails(): array
    {
        $sql = "
            SELECT 
                p.*,
                f.id AS forum_id,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at,
                COUNT(DISTINCT r.id) AS reports_count,
                us.reputation AS author_reputation,
                us.forums_count AS author_forums_count,
                us.publications_count AS author_publications_count
            FROM publications p
            LEFT JOIN forums f ON f.id = p.forum_id
            LEFT JOIN reports r ON r.publication_id = p.id AND r.target_type = 'publication'
            LEFT JOIN user_stats us ON us.username = p.author
            GROUP BY p.id
            ORDER BY p.created_at DESC, p.id DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère une publication avec toutes les informations liées :
     * - Informations complètes du forum
     * - Nombre de signalements
     * - Statistiques de l'auteur
     */
    public function getByIdWithFullDetails($id): ?array
    {
        $sql = "
            SELECT 
                p.*,
                f.id AS forum_id,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at,
                COUNT(DISTINCT r.id) AS reports_count,
                us.reputation AS author_reputation,
                us.forums_count AS author_forums_count,
                us.publications_count AS author_publications_count,
                (SELECT COUNT(*) 
                 FROM publications p2 
                 WHERE p2.author = p.author) AS author_total_publications,
                (SELECT COUNT(*) 
                 FROM forums f2 
                 WHERE f2.created_by = p.author) AS author_total_forums
            FROM publications p
            LEFT JOIN forums f ON f.id = p.forum_id
            LEFT JOIN reports r ON r.publication_id = p.id AND r.target_type = 'publication'
            LEFT JOIN user_stats us ON us.username = p.author
            WHERE p.id = :id
            GROUP BY p.id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Récupère toutes les publications d'un forum avec toutes les informations liées.
     */
    public function getByForumWithFullDetails($forum_id): array
    {
        $sql = "
            SELECT 
                p.*,
                f.id AS forum_id,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                f.created_at AS forum_created_at,
                COUNT(DISTINCT r.id) AS reports_count,
                us.reputation AS author_reputation,
                us.forums_count AS author_forums_count,
                us.publications_count AS author_publications_count
            FROM publications p
            INNER JOIN forums f ON f.id = p.forum_id
            LEFT JOIN reports r ON r.publication_id = p.id AND r.target_type = 'publication'
            LEFT JOIN user_stats us ON us.username = p.author
            WHERE p.forum_id = :forum_id
            GROUP BY p.id
            ORDER BY p.created_at DESC, p.id DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':forum_id' => $forum_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère les publications d'un auteur avec les informations des forums.
     */
    public function getByAuthorWithFullDetails(string $author, int $limit = 50): array
    {
        $sql = "
            SELECT 
                p.*,
                f.id AS forum_id,
                f.title AS forum_title,
                f.description AS forum_description,
                f.created_by AS forum_created_by,
                COUNT(DISTINCT r.id) AS reports_count
            FROM publications p
            LEFT JOIN forums f ON f.id = p.forum_id
            LEFT JOIN reports r ON r.publication_id = p.id AND r.target_type = 'publication'
            WHERE p.author = :author
            GROUP BY p.id
            ORDER BY p.created_at DESC, p.id DESC
            LIMIT :limit
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':author', $author, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

}
