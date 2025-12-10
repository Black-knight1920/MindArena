<?php
// Completely disable error display
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../models/AdminModel.php';

class AdminController {
    
    public function updateProfile() {
        // Check if request is AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(array('success' => false, 'message' => 'Invalid request'));
            exit();
        }

        // Validate admin session
        if (session_id() === '') {
            session_start();
        }
        
        if (!isset($_SESSION["admin"])) {
            http_response_code(401);
            echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(array('success' => false, 'message' => 'Method not allowed'));
            exit();
        }

        // Get current admin username from session
        $currentUsername = $_SESSION["admin"];

        // Sanitize and validate input
        $newUsername = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
        $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
        $password = isset($_POST['password']) && !empty($_POST['password']) ? trim($_POST['password']) : null;

        // Validation
        if (empty($newUsername)) {
            echo json_encode(array('success' => false, 'message' => 'Username is required'));
            exit();
        }

        // Validate email format only if email is provided (email column may not exist)
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array('success' => false, 'message' => 'Invalid email format'));
            exit();
        }

        // Validate password if provided
        if ($password !== null && strlen($password) < 6) {
            echo json_encode(array('success' => false, 'message' => 'Password must be at least 6 characters'));
            exit();
        }

        // Validate username length
        if (strlen($newUsername) < 3) {
            echo json_encode(array('success' => false, 'message' => 'Username must be at least 3 characters'));
            exit();
        }

        $model = new AdminModel();
        $result = $model->updateAdmin($currentUsername, $newUsername, $email, $password);

        if ($result) {
            // Update session with new username
            $_SESSION["admin"] = $newUsername;
            
            echo json_encode(array(
                'success' => true, 
                'message' => 'Profile updated successfully!',
                'newUsername' => $newUsername
            ));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to update profile. Please try again.'));
        }
        exit();
    }

    public function getProfile() {
        // Validate admin session
        if (session_id() === '') {
            session_start();
        }
        
        if (!isset($_SESSION["admin"])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
            exit();
        }

        try {
            $username = $_SESSION["admin"];
            
            if (empty($username)) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Admin username is empty'));
                exit();
            }
            
            $model = new AdminModel();
            $admin = $model->getAdminByName($username);

            if ($admin && is_array($admin)) {
                // Don't send password hash to frontend
                if (isset($admin['mdpa'])) {
                    unset($admin['mdpa']);
                }
                // Ensure email field exists
                if (!isset($admin['email'])) {
                    $admin['email'] = '';
                }
                // Ensure name field exists
                if (!isset($admin['name'])) {
                    $admin['name'] = $username;
                }
                header('Content-Type: application/json');
                echo json_encode(array('success' => true, 'data' => $admin));
            } else {
                // Fallback: return session data if admin not found in DB
                header('Content-Type: application/json');
                echo json_encode(array(
                    'success' => true, 
                    'data' => array(
                        'name' => $username,
                        'email' => ''
                    )
                ));
            }
        } catch (Exception $e) {
            error_log("Error in getProfile: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Error loading profile: ' . $e->getMessage()));
        }
        exit();
    }
}
