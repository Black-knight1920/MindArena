<?php
// Completely disable error display
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../models/UserModel.php';

class UserProfileController {
    
    public function updateProfile() {
        try {
            // Check if request is AJAX
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Invalid request'));
                exit();
            }

            // Validate user session
            if (session_id() === '') {
                session_start();
            }
            
            if (!isset($_SESSION["username"])) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
                exit();
            }

            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                http_response_code(405);
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Method not allowed'));
                exit();
            }

            $currentUsername = $_SESSION["username"];
            $model = new UserModel();
            
            // Get current user data
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User();
            $currentUser = $userModel->getUserByName($currentUsername);
            
            if (!$currentUser) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'User not found'));
                exit();
            }

            $userId = $currentUser['id'];
            $newUsername = isset($_POST['username']) ? filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING) : $currentUsername;
            $newEmail = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : $currentUser['email'];
            $currentPassword = isset($_POST['current_password']) ? trim($_POST['current_password']) : null;
            $newPassword = isset($_POST['new_password']) && !empty($_POST['new_password']) ? trim($_POST['new_password']) : null;
            $profilePicture = null;

            // Validation
            if (empty($newUsername)) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Username is required'));
                exit();
            }

            if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Valid email is required'));
                exit();
            }

            // Validate password if new password is provided
            if ($newPassword !== null) {
                if (empty($currentPassword)) {
                    header('Content-Type: application/json');
                    echo json_encode(array('success' => false, 'message' => 'Current password is required to change password'));
                    exit();
                }
                
                // Verify current password
                if (md5($currentPassword) !== $currentUser['mdp']) {
                    header('Content-Type: application/json');
                    echo json_encode(array('success' => false, 'message' => 'Current password is incorrect'));
                    exit();
                }
                
                if (strlen($newPassword) < 6) {
                    header('Content-Type: application/json');
                    echo json_encode(array('success' => false, 'message' => 'New password must be at least 6 characters'));
                    exit();
                }
            }

            // Handle profile picture upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['profile_picture'], $userId);
                if (!$uploadResult['success']) {
                    header('Content-Type: application/json');
                    echo json_encode($uploadResult);
                    exit();
                }
                $profilePicture = $uploadResult['filename'];
            }

            // Update user in database
            try {
                $result = $model->updateUserProfile($userId, $newUsername, $newEmail, $newPassword, $profilePicture);

                if ($result) {
                    // Update session
                    $_SESSION["username"] = $newUsername;
                    $_SESSION["email"] = $newEmail;
                    
                    $response = array(
                        'success' => true, 
                        'message' => 'Profile updated successfully!',
                        'newUsername' => $newUsername,
                        'newEmail' => $newEmail
                    );
                    
                    if ($profilePicture) {
                        // Add timestamp for cache-busting
                        $response['profilePicture'] = 'uploads/profile_pics/' . $profilePicture . '?t=' . time();
                    }
                    
                    header('Content-Type: application/json');
                    echo json_encode($response);
                } else {
                    error_log("updateUserProfile returned false for user ID: " . $userId);
                    header('Content-Type: application/json');
                    echo json_encode(array('success' => false, 'message' => 'Failed to update profile. Please try again.'));
                }
            } catch (Exception $e) {
                error_log("Error updating profile: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Error updating profile. Please try again.'));
            }
            exit();
        } catch (Exception $e) {
            error_log("Fatal error in updateProfile: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Server error. Please try again.'));
            exit();
        }
    }

    private function handleImageUpload($file, $userId) {
        $allowedTypes = array('image/jpeg', 'image/jpg', 'image/png');
        $allowedExtensions = array('jpg', 'jpeg', 'png');
        $maxSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = __DIR__ . '/../uploads/profile_pics/';
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = array(
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive.',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive.',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
            );
            $errorMsg = isset($errorMessages[$file['error']]) ? $errorMessages[$file['error']] : 'Unknown upload error.';
            error_log("File upload error: " . $errorMsg);
            return array('success' => false, 'message' => 'Upload error: ' . $errorMsg);
        }
        
        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                error_log("Failed to create upload directory: " . $uploadDir);
                return array('success' => false, 'message' => 'Failed to create upload directory. Please contact administrator.');
            }
        }

        // Check if directory is writable
        if (!is_writable($uploadDir)) {
            error_log("Upload directory is not writable: " . $uploadDir);
            return array('success' => false, 'message' => 'Upload directory is not writable. Please contact administrator.');
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return array('success' => false, 'message' => 'Please upload a valid image (JPEG/PNG) under 5MB.');
        }

        // Validate file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return array('success' => false, 'message' => 'Please upload a valid image (JPEG/PNG) under 5MB.');
        }

        // Validate file type using multiple methods (PHP 5.3 compatible)
        $mimeType = null;
        
        // Method 1: Try finfo if available
        if (function_exists('finfo_open')) {
            try {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $mimeType = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);
                }
            } catch (Exception $e) {
                error_log("finfo error: " . $e->getMessage());
            }
        }
        
        // Method 2: Fallback to mime_content_type if available
        if (!$mimeType && function_exists('mime_content_type')) {
            $mimeType = mime_content_type($file['tmp_name']);
        }
        
        // Method 3: Fallback to $_FILES type (less reliable but works)
        if (!$mimeType && isset($file['type'])) {
            $mimeType = $file['type'];
        }
        
        // Validate MIME type if we got one
        if ($mimeType && !in_array($mimeType, $allowedTypes)) {
            return array('success' => false, 'message' => 'Please upload a valid image (JPEG/PNG) under 5MB.');
        }

        // Additional security: Check if file is actually an image by trying to get image info
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return array('success' => false, 'message' => 'Please upload a valid image (JPEG/PNG) under 5MB.');
        }

        // Generate unique filename
        $filename = 'user_' . $userId . '_' . time() . '_' . md5($file['name'] . $userId) . '.' . $extension;

        // Move uploaded file first (before deleting old one)
        $targetPath = $uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Verify file was moved successfully
            if (file_exists($targetPath)) {
                // Delete old profile picture AFTER new one is successfully saved
                try {
                    $oldPicture = $this->getUserProfilePicture($userId);
                    if ($oldPicture && $oldPicture !== $filename && file_exists($uploadDir . $oldPicture)) {
                        @unlink($uploadDir . $oldPicture);
                    }
                } catch (Exception $e) {
                    error_log("Error deleting old picture: " . $e->getMessage());
                    // Continue anyway, not critical
                }
                
                return array('success' => true, 'filename' => $filename);
            } else {
                error_log("File move appeared successful but file doesn't exist: " . $targetPath);
                return array('success' => false, 'message' => 'Failed to save image. Please try again.');
            }
        } else {
            error_log("move_uploaded_file failed. tmp_name: " . $file['tmp_name'] . ", target: " . $targetPath);
            return array('success' => false, 'message' => 'Failed to upload image. Please try again.');
        }
    }

    private function getUserProfilePicture($userId) {
        require_once __DIR__ . '/../models/database.php';
        global $conn;
        
        try {
            $checkColumn = $conn->query("SHOW COLUMNS FROM `user` LIKE 'profile_picture'");
            if ($checkColumn->rowCount() == 0) {
                return null;
            }
            
            $stmt = $conn->prepare("SELECT profile_picture FROM user WHERE id = :id");
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['profile_picture'] : null;
        } catch (PDOException $e) {
            error_log("Error getting profile picture: " . $e->getMessage());
            return null;
        }
    }

    public function getProfile() {
        // Validate user session
        if (session_id() === '') {
            session_start();
        }
        
        if (!isset($_SESSION["username"])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
            exit();
        }

        try {
            $username = $_SESSION["username"];
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User();
            $user = $userModel->getUserByName($username);

            if ($user && is_array($user)) {
                // Don't send password hash to frontend
                unset($user['mdp']);
                
                // Get profile picture path
                $profilePicture = $this->getUserProfilePicture($user['id']);
                if ($profilePicture) {
                    // Add timestamp for cache-busting
                    $user['profile_picture'] = 'uploads/profile_pics/' . $profilePicture . '?t=' . time();
                } else {
                    $user['profile_picture'] = null;
                }
                
                header('Content-Type: application/json');
                echo json_encode(array('success' => true, 'data' => $user));
            } else {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'User not found'));
            }
        } catch (Exception $e) {
            error_log("Error in getProfile: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Error loading profile: ' . $e->getMessage()));
        }
        exit();
    }
}

?>
