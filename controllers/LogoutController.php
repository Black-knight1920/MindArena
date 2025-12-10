<?php
class LogoutController {
    public function logout() {
        // PHP 5.3 compatible session check
        if (session_id() === '') {
            session_start();
        }
        
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php");
        exit();
    }
}
?>