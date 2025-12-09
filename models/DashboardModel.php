<?php

require_once __DIR__ . '/database.php';

class DashboardModel {

    private $db;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    public function getTotalUsers() {
        return $this->db->query("SELECT COUNT(*) FROM user")->fetchColumn();
    }

    public function getNewUsers() {
        return $this->db->query("
            SELECT COUNT(*) 
            FROM user 
            WHERE `date-inscrit` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ")->fetchColumn();
    }

    public function getActiveSessions() {
        return $this->db->query("
            SELECT COUNT(*) 
            FROM user 
            WHERE `donation` >= 1
        ")->fetchColumn();
    }

    public function getRecentUsers() {
        $stmt = $this->db->query("
            SELECT name, `date-inscrit` 
            FROM user 
            ORDER BY id DESC 
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
