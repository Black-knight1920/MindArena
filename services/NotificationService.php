<?php

class NotificationService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Crée une notification (ex: nouveau signalement, nouveau forum, etc.)
     *
     * @param string      $type    ex: 'report', 'forum', 'system'
     * @param string      $title   titre court
     * @param string|null $message texte optionnel
     * @param string|null $url     lien vers la page concernée (facultatif)
     */
    public function create(string $type, string $title, ?string $message = null, ?string $url = null): void
    {
        $sql = "INSERT INTO notifications (type, title, message, url, is_read, created_at)
                VALUES (:type, :title, :message, :url, 0, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':type'    => $type,
            ':title'   => $title,
            ':message' => $message,
            ':url'     => $url,
        ]);
    }

    /**
     * Nombre de notifications non lues (badge dans la topbar admin)
     */
    public function countUnread(): int
    {
        $sql = "SELECT COUNT(*) FROM notifications WHERE is_read = 0";
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Dernières notifications (affichées dans le menu déroulant)
     *
     * @param int $limit nb max de lignes
     * @return array
     */
    public function getLatest(int $limit = 5): array
    {
        $sql = "SELECT id, type, title, message, url, is_read, created_at
                FROM notifications
                ORDER BY created_at DESC
                LIMIT :lim";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marquer toutes les notifications comme lues (si tu veux un bouton plus tard)
     */
    public function markAllRead(): void
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
        $this->pdo->exec($sql);
    }

    /**
     * Marquer une notification précise comme lue.
     */
    public function markRead(int $id): void
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }
}
