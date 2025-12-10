<?php
session_start();
require_once('models/database.php');

// If user session is not set → redirect to login
if (!isset($_SESSION["username"])) {
    header("Location: http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php");
    exit();
}

// Get the user's current profile data from the session or database
$username = $_SESSION["username"];
$email    = $_SESSION["email"];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate the input data
    $newUsername = htmlspecialchars($_POST['username']);
    $newEmail = htmlspecialchars($_POST['email']);

    // Using PDO connection from database.php
    try {
        $query = "UPDATE user SET name = :username, email = :email WHERE name = :old_username";
        $stmt = $conn->prepare($query);
        $stmt->execute(array(
            ':username' => $newUsername,
            ':email' => $newEmail,
            ':old_username' => $username
        ));
        
        // Update the session variables after successful update
        $_SESSION['username'] = $newUsername;
        $_SESSION['email'] = $newEmail;

        // Set success message
        $message = "Profile updated successfully!";
        $messageType = "success";
    } catch (PDOException $e) {
        // Handle database error
        $message = "Error updating profile. Please try again.";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game On! - Mind Arena</title>
    <link rel="stylesheet" href="assets/css/user_home.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Top Right Buttons -->
    <div class="top-actions">
        <button class="user-details-btn" onclick="openModal()">
            <i class="fas fa-user"></i>
            <span><?php echo htmlspecialchars($username); ?></span>
        </button>
        <a href="http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>

    <!-- Main Content Container -->
    <div class="main-container">
        <!-- Header Section -->
        <header class="main-header">
            <div class="header-content">
                <div class="game-icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <h1 class="glow-text">Game On!</h1>
                <p class="subheading">Découvrez les meilleures équipes esports et leurs joueurs professionnels.</p>
            </div>
        </header>

        <!-- Action Button -->
        <div class="action-section">
            <button class="discover-btn">
                <span>GAMES</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <a href="#" class="footer-link">
                <i class="fas fa-home"></i>
                <span>Accueil</span>
            </a>
            <a href="#" class="footer-link">
                <i class="fas fa-users"></i>
                <span>Équipes</span>
            </a>
            <a href="#" class="footer-link">
                <i class="fas fa-trophy"></i>
                <span>Tournois</span>
            </a>
            <a href="#" class="footer-link">
                <i class="fas fa-info-circle"></i>
                <span>À propos</span>
            </a>
        </div>
    </footer>

    <!-- PROFILE MODAL -->
    <div id="profileModal" class="modal">
        <div class="modal-content profile-modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Your Profile</h2>
            
            <!-- Profile Picture Section -->
            <div class="profile-picture-section">
                <div class="profile-picture-container">
                    <img id="profilePictureDisplay" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Ccircle cx='75' cy='75' r='75' fill='%238b00ff'/%3E%3Ccircle cx='75' cy='60' r='25' fill='%23ffffff'/%3E%3Cpath d='M25 120 Q25 100 75 100 Q125 100 125 120 L125 150 L25 150 Z' fill='%23ffffff'/%3E%3C/svg%3E" alt="Profile Picture" class="profile-picture">
                    <button class="edit-picture-btn" onclick="openEditModal()">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
            </div>

            <div class="profile-info">
                <p><strong>Username:</strong> <span id="profileUsername"><?php echo htmlspecialchars($username); ?></span></p>
                <p><strong>Email:</strong> <span id="profileEmail"><?php echo htmlspecialchars($email); ?></span></p>
            </div>
            <button class="edit-btn" onclick="openEditModal()">
                <i class="fas fa-edit"></i>
                Edit Profile
            </button>
        </div>
    </div>

    <!-- EDIT PROFILE MODAL -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content edit-modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Your Profile</h2>

            <div id="profileMessage" style="display: none; padding: 15px; margin-bottom: 20px; border-radius: 10px; font-size: 14px; text-align: center; font-weight: 600;"></div>
            
            <!-- Profile Edit Form -->
            <form id="profileEditForm" enctype="multipart/form-data">
                <!-- Profile Picture Upload -->
                <div class="form-group">
                    <label>Profile Picture:</label>
                    <div class="profile-picture-upload-section">
                        <div class="profile-picture-preview-container">
                            <img id="profilePicturePreview" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Ccircle cx='60' cy='60' r='60' fill='%238b00ff'/%3E%3Ccircle cx='60' cy='48' r='20' fill='%23ffffff'/%3E%3Cpath d='M20 96 Q20 80 60 80 Q100 80 100 96 L100 120 L20 120 Z' fill='%23ffffff'/%3E%3C/svg%3E" alt="Profile Picture Preview" class="profile-picture-preview">
                            <button type="button" class="change-picture-btn" onclick="document.getElementById('profilePictureInput').click()">
                                <i class="fas fa-camera"></i>
                                Change Picture
                            </button>
                        </div>
                        <input type="file" id="profilePictureInput" name="profile_picture" accept="image/jpeg,image/jpg,image/png" style="display: none;" onchange="previewProfilePicture(this)">
                        <small class="file-info">JPEG/PNG, max 5MB</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="editUsername">Username:</label>
                    <input type="text" id="editUsername" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="editEmail">Email:</label>
                    <input type="email" id="editEmail" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="currentPassword">Current Password (required to change password):</label>
                    <input type="password" id="currentPassword" name="current_password" placeholder="Enter current password">
                </div>

                <div class="form-group">
                    <label for="newPassword">New Password (leave blank to keep current):</label>
                    <input type="password" id="newPassword" name="new_password" minlength="6" placeholder="Enter new password (min 6 characters)">
                </div>
                
                <button type="submit" class="submit-btn" id="saveProfileBtn">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Load user profile data
        function loadUserProfile() {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'user_profile.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success && response.data) {
                            // Update profile display
                            const defaultAvatar = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Ccircle cx='75' cy='75' r='75' fill='%238b00ff'/%3E%3Ccircle cx='75' cy='60' r='25' fill='%23ffffff'/%3E%3Cpath d='M25 120 Q25 100 75 100 Q125 100 125 120 L125 150 L25 150 Z' fill='%23ffffff'/%3E%3C/svg%3E";
                            const defaultAvatarSmall = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Ccircle cx='60' cy='60' r='60' fill='%238b00ff'/%3E%3Ccircle cx='60' cy='48' r='20' fill='%23ffffff'/%3E%3Cpath d='M20 96 Q20 80 60 80 Q100 80 100 96 L100 120 L20 120 Z' fill='%23ffffff'/%3E%3C/svg%3E";
                            
                            if (response.data.profile_picture) {
                                // Add cache-busting if not already present
                                let pictureUrl = response.data.profile_picture;
                                if (pictureUrl.indexOf('?t=') === -1) {
                                    pictureUrl += '?t=' + new Date().getTime();
                                }
                                document.getElementById('profilePictureDisplay').src = pictureUrl;
                                document.getElementById('profilePicturePreview').src = pictureUrl;
                            } else {
                                document.getElementById('profilePictureDisplay').src = defaultAvatar;
                                document.getElementById('profilePicturePreview').src = defaultAvatarSmall;
                            }
                            document.getElementById('profileUsername').textContent = response.data.name || '';
                            document.getElementById('profileEmail').textContent = response.data.email || '';
                            if (document.getElementById('editUsername')) {
                                document.getElementById('editUsername').value = response.data.name || '';
                            }
                            if (document.getElementById('editEmail')) {
                                document.getElementById('editEmail').value = response.data.email || '';
                            }
                        }
                    } catch (e) {
                        console.error('Error loading profile:', e);
                    }
                }
            };
            
            xhr.send('action=get');
        }

        // Preview profile picture before upload
        function previewProfilePicture(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    showMessage('Please upload a valid image (JPEG/PNG) under 5MB.', 'error');
                    input.value = '';
                    return;
                }
                
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showMessage('Please upload a valid image (JPEG/PNG) under 5MB.', 'error');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePicturePreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function openModal() {
            loadUserProfile();
            document.getElementById("profileModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("profileModal").style.display = "none";
        }

        function openEditModal() {
            closeModal();
            loadUserProfile();
            document.getElementById("editProfileModal").style.display = "flex";
        }

        function closeEditModal() {
            document.getElementById("editProfileModal").style.display = "none";
            document.getElementById('profileMessage').style.display = 'none';
        }

        // Show message
        function showMessage(message, type) {
            const messageDiv = document.getElementById('profileMessage');
            messageDiv.textContent = message;
            messageDiv.style.display = 'block';
            messageDiv.className = 'alert ' + type;
        }

        // Handle form submission with AJAX
        document.getElementById('profileEditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'update');
            
            const saveBtn = document.getElementById('saveProfileBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Saving...</span>';
            saveBtn.disabled = true;
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'user_profile.php', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                    
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                showMessage(response.message || 'Profile updated successfully!', 'success');
                                
                                // Update profile picture if changed
                                if (response.profilePicture) {
                                    // Add cache-busting timestamp if not already present
                                    let pictureUrl = response.profilePicture;
                                    if (pictureUrl.indexOf('?t=') === -1) {
                                        pictureUrl += '?t=' + new Date().getTime();
                                    }
                                    document.getElementById('profilePictureDisplay').src = pictureUrl;
                                    document.getElementById('profilePicturePreview').src = pictureUrl;
                                    
                                    // Force image reload by setting src to empty first
                                    setTimeout(function() {
                                        const img1 = document.getElementById('profilePictureDisplay');
                                        const img2 = document.getElementById('profilePicturePreview');
                                        const currentSrc1 = img1.src;
                                        const currentSrc2 = img2.src;
                                        img1.src = '';
                                        img2.src = '';
                                        setTimeout(function() {
                                            img1.src = currentSrc1;
                                            img2.src = currentSrc2;
                                        }, 10);
                                    }, 100);
                                } else {
                                    // Reload profile to get updated picture
                                    setTimeout(function() {
                                        loadUserProfile();
                                    }, 500);
                                }
                                
                                // Update username in top button
                                if (response.newUsername) {
                                    const usernameSpan = document.querySelector('.user-details-btn span');
                                    if (usernameSpan) {
                                        usernameSpan.textContent = response.newUsername;
                                    }
                                    document.getElementById('profileUsername').textContent = response.newUsername;
                                }
                                
                                // Update email
                                if (response.newEmail) {
                                    document.getElementById('profileEmail').textContent = response.newEmail;
                                }
                                
                                // Clear password fields
                                document.getElementById('currentPassword').value = '';
                                document.getElementById('newPassword').value = '';
                                
                                // Close modal after 2 seconds
                                setTimeout(function() {
                                    closeEditModal();
                                }, 2000);
                            } else {
                                showMessage(response.message || 'Failed to update profile', 'error');
                            }
                        } catch (e) {
                            showMessage('Error processing response', 'error');
                            console.error('Parse error:', e, 'Response:', xhr.responseText);
                        }
                    } else {
                        showMessage('Server error. Please try again.', 'error');
                    }
                }
            };
            
            xhr.send(formData);
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const profileModal = document.getElementById("profileModal");
            const editModal = document.getElementById("editProfileModal");
            if (event.target == profileModal) {
                profileModal.style.display = "none";
            }
            if (event.target == editModal) {
                editModal.style.display = "none";
                document.getElementById('profileMessage').style.display = 'none';
            }
        }

        // Load profile on page load
        window.addEventListener('DOMContentLoaded', function() {
            loadUserProfile();
        });
    </script>
</body>
</html>
