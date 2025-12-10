<?php
// Completely disable error display
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

require_once "database.php";

/**
 * Model for tracking login attempts and rate limiting
 */
class LoginAttemptModel {
    private $db;
    
    // Configuration
    private $maxAttempts = 5; // Maximum failed attempts before CAPTCHA
    private $timeWindow = 600; // 10 minutes in seconds
    private $lockoutTime = 900; // 15 minutes lockout in seconds
    
    public function __construct() {
        global $conn;
        $this->db = $conn;
    }
    
    /**
     * Record a failed login attempt
     * 
     * @param string $identifier Username or email
     * @param string $ipAddress IP address
     * @return array Array with 'attempts' (int) and 'locked' (bool)
     */
    public function recordFailedAttempt($identifier, $ipAddress) {
        try {
            // Check if table exists, create if not
            $this->ensureTableExists();
            
            // Clean old attempts
            $this->cleanOldAttempts();
            
            // Insert new attempt
            $stmt = $this->db->prepare(
                "INSERT INTO login_attempts (identifier, ip_address, attempt_time, success) 
                 VALUES (:identifier, :ip_address, NOW(), 0)"
            );
            $stmt->execute(array(
                ':identifier' => $identifier,
                ':ip_address' => $ipAddress
            ));
            
            // Get current attempt count
            $attempts = $this->getFailedAttempts($identifier, $ipAddress);
            
            return array(
                'attempts' => $attempts,
                'locked' => $attempts >= $this->maxAttempts,
                'requires_captcha' => $attempts >= $this->maxAttempts
            );
        } catch (PDOException $e) {
            error_log("Error recording failed attempt: " . $e->getMessage());
            // Return safe defaults
            return array(
                'attempts' => 0,
                'locked' => false,
                'requires_captcha' => false
            );
        }
    }
    
    /**
     * Record a successful login attempt
     * 
     * @param string $identifier Username or email
     * @param string $ipAddress IP address
     */
    public function recordSuccessAttempt($identifier, $ipAddress) {
        try {
            $this->ensureTableExists();
            
            // Insert success record
            $stmt = $this->db->prepare(
                "INSERT INTO login_attempts (identifier, ip_address, attempt_time, success) 
                 VALUES (:identifier, :ip_address, NOW(), 1)"
            );
            $stmt->execute(array(
                ':identifier' => $identifier,
                ':ip_address' => $ipAddress
            ));
            
            // Clear failed attempts for this identifier/IP
            $this->clearFailedAttempts($identifier, $ipAddress);
        } catch (PDOException $e) {
            error_log("Error recording success attempt: " . $e->getMessage());
        }
    }
    
    /**
     * Get number of failed attempts in the time window
     * 
     * @param string $identifier Username or email
     * @param string $ipAddress IP address
     * @return int Number of failed attempts
     */
    public function getFailedAttempts($identifier, $ipAddress) {
        try {
            $this->ensureTableExists();
            
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count 
                 FROM login_attempts 
                 WHERE (identifier = :identifier OR ip_address = :ip_address)
                 AND success = 0 
                 AND attempt_time > DATE_SUB(NOW(), INTERVAL :window SECOND)"
            );
            $stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
            $stmt->bindValue(':ip_address', $ipAddress, PDO::PARAM_STR);
            $stmt->bindValue(':window', $this->timeWindow, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return isset($result['count']) ? (int)$result['count'] : 0;
        } catch (PDOException $e) {
            error_log("Error getting failed attempts: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Check if login is locked for identifier/IP
     * 
     * @param string $identifier Username or email
     * @param string $ipAddress IP address
     * @return bool
     */
    public function isLocked($identifier, $ipAddress) {
        $attempts = $this->getFailedAttempts($identifier, $ipAddress);
        return $attempts >= $this->maxAttempts;
    }
    
    /**
     * Clear failed attempts for identifier/IP
     * 
     * @param string $identifier Username or email
     * @param string $ipAddress IP address
     */
    private function clearFailedAttempts($identifier, $ipAddress) {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM login_attempts 
                 WHERE (identifier = :identifier OR ip_address = :ip_address) 
                 AND success = 0"
            );
            $stmt->execute(array(
                ':identifier' => $identifier,
                ':ip_address' => $ipAddress
            ));
        } catch (PDOException $e) {
            error_log("Error clearing failed attempts: " . $e->getMessage());
        }
    }
    
    /**
     * Clean old attempts (older than lockout time)
     */
    private function cleanOldAttempts() {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM login_attempts 
                 WHERE attempt_time < DATE_SUB(NOW(), INTERVAL :lockout SECOND)"
            );
            $stmt->bindValue(':lockout', $this->lockoutTime, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error cleaning old attempts: " . $e->getMessage());
        }
    }
    
    /**
     * Ensure login_attempts table exists
     */
    private function ensureTableExists() {
        try {
            // Check if table exists
            $checkTable = $this->db->query("SHOW TABLES LIKE 'login_attempts'");
            if ($checkTable->rowCount() == 0) {
                // Create table
                $this->db->exec("
                    CREATE TABLE IF NOT EXISTS login_attempts (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        identifier VARCHAR(255) NOT NULL,
                        ip_address VARCHAR(45) NOT NULL,
                        attempt_time DATETIME NOT NULL,
                        success TINYINT(1) NOT NULL DEFAULT 0,
                        INDEX idx_identifier (identifier),
                        INDEX idx_ip (ip_address),
                        INDEX idx_time (attempt_time),
                        INDEX idx_success (success)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
                ");
            }
        } catch (PDOException $e) {
            error_log("Error ensuring table exists: " . $e->getMessage());
        }
    }
    
    /**
     * Get client IP address
     */
    public function getClientIp() {
        $ipKeys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
}

?>

