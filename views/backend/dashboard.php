<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice – Dashboard</title>
    <link rel="stylesheet" href="assets/css/back.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="light">

    <div class="admin-shell">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-logo">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="sidebar-title">
                    <span>Mind Arena</span>
                    <span><?php echo $_SESSION["admin"] ?></span>
                </div>
            </div>

            <ul class="sidebar-nav">
                <li class="sidebar-nav-label">Navigation</li>
                <li><a href="http://127.0.0.1/project-MVC%20-%20Copie/admin_index.php" class="sidebar-link active"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="http://127.0.0.1/project-MVC%20-%20Copie/users.php" class="sidebar-link"><i class="fas fa-users"></i> Users</a></li>
                <!--<li><a href="#" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="#" class="sidebar-link"><i class="fas fa-chart-bar"></i> Statistics</a></li>-->
            </ul>

            <div class="sidebar-footer">
                <a href="http://127.0.0.1/project-MVC%20-%20Copie/views/frontend/login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <div class="admin-header">
                <div class="header-left">
                    <div>
                        <div class="header-left-title">Admin Dashboard</div>
                        <div class="header-left-sub">Welcome back, Admin!</div>
                    </div>
                </div>

                <div class="header-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>

                <div class="header-right">
                    <div class="theme-toggle-wrap">
                        <span>Dark</span>
                        <div class="theme-toggle" id="themeToggle">
                            <div class="theme-toggle-thumb">
                                <i class="fas fa-moon"></i>
                            </div>
                        </div>
                        <span>Light</span>
                    </div>

                    <div class="header-user" id="adminProfileTrigger" style="cursor: pointer; transition: opacity 0.2s ease;">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span><?php echo htmlspecialchars($_SESSION["admin"]); ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 6px; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="admin-content">
                <div class="content-inner">
                    <div class="page-header">
                        <h1 class="page-title">Dashboard</h1>
                        <p class="page-subtitle">Welcome back, Admin! Here's what's happening today.</p>
                    </div>

                    <div class="cards">
                        <div class="card card-shell">
                            <h3>New Users (7 days)</h3>
                            <p class="number"><?php echo $data['newUsers']; ?></p>
                        </div>

                        <div class="card card-shell">
                            <h3>Total Users</h3>
                            <p class="number"><?php echo $data['totalUsers']; ?></p>
                        </div>

                        <div class="card card-shell">
                            <h3>Active Sessions</h3>
                            <p class="number"><?php echo $data['activeSessions']; ?></p>
                        </div>
                    </div>

                    <div class="panel card-shell">
                        <h2>Recent Activities</h2>
                        <ul>
                            <?php foreach ($data['recentUsers'] as $user): ?>
                                <li>
                                    <i class="fas fa-user-plus"></i>
                                    <strong><?php echo $user['name'] ?></strong> 
                                    joined on <?php echo $user['date-inscrit'] ?>.
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Profile Edit Modal -->
    <div id="adminProfileModal" class="modal" style="display: none; position: fixed; inset: 0; justify-content: center; align-items: center; background: rgba(0, 0, 0, 0.5); z-index: 1000;">
        <div class="modal-content card-shell" style="background-color: var(--card-bg, #fff); color: var(--text-color, #333); border-radius: 12px; padding: 25px 30px; max-width: 480px; width: 100%; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); display: flex; flex-direction: column; font-family: 'Poppins', sans-serif; animation: fadeIn 0.3s ease;">
            <span class="close" id="adminProfileClose" style="cursor:pointer; font-size: 22px; align-self: flex-end; font-weight: bold; color: #888; transition: color 0.2s ease;">&times;</span>
            <h2 style="margin-top: 0; margin-bottom: 20px; font-weight: 500; font-size: 1.5rem; color: var(--primary-color, #333);">Edit Admin Profile</h2>
            <div id="adminProfileMessage" style="display: none; padding: 10px; margin-bottom: 15px; border-radius: 8px;"></div>
            
            <form id="adminProfileForm">
                <label for="adminUsername" style="font-weight: 500; font-size: 0.9rem; margin-top: 12px; margin-bottom: 5px; display: block;">Username:</label>
                <input type="text" id="adminUsername" name="username" required style="width: 100%; padding: 10px 12px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 0.95rem; font-family: 'Poppins', sans-serif; transition: border-color 0.2s ease; box-sizing: border-box;">

                <label for="adminEmail" style="font-weight: 500; font-size: 0.9rem; margin-top: 12px; margin-bottom: 5px; display: block;">Email (optional - not stored in database):</label>
                <input type="email" id="adminEmail" name="email" placeholder="Email not stored in admin table" style="width: 100%; padding: 10px 12px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 0.95rem; font-family: 'Poppins', sans-serif; transition: border-color 0.2s ease; box-sizing: border-box;">

                <label for="adminPassword" style="font-weight: 500; font-size: 0.9rem; margin-top: 12px; margin-bottom: 5px; display: block;">Password (leave blank to keep current):</label>
                <div style="position: relative;">
                    <input type="password" id="adminPassword" name="password" minlength="6" placeholder="Enter new password or leave blank" style="width: 100%; padding: 10px 40px 10px 12px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 0.95rem; font-family: 'Poppins', sans-serif; transition: border-color 0.2s ease; box-sizing: border-box;">
                    <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 5px;">
                        <i class="fas fa-eye" id="passwordIcon"></i>
                    </button>
                </div>
                <small style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-top: -8px; margin-bottom: 10px;">Minimum 6 characters. Leave blank to keep current password.</small>

                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" class="btn-save" style="flex: 1; background-color: var(--primary-color, #007bff); color: #fff; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-size: 1rem; font-weight: 500; transition: background 0.2s ease;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" id="adminProfileCancel" style="flex: 1; background: var(--text-muted); color: white; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-size: 1rem; font-weight: 500; transition: background 0.2s ease;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .modal-content input:focus {
            border-color: var(--primary-color, #007bff);
            outline: none;
        }
        .btn-save:hover {
            background-color: #0056b3;
        }
        .close:hover {
            color: var(--primary-color, #007bff);
        }
    </style>

    <script>
        // Theme toggle functionality
        document.getElementById('themeToggle').addEventListener('click', function() {
            document.body.classList.toggle('light');
        });

        // Admin Profile Modal functionality
        const adminProfileModal = document.getElementById('adminProfileModal');
        const adminProfileTrigger = document.getElementById('adminProfileTrigger');
        const adminProfileClose = document.getElementById('adminProfileClose');
        const adminProfileCancel = document.getElementById('adminProfileCancel');
        const adminProfileForm = document.getElementById('adminProfileForm');
        const adminProfileMessage = document.getElementById('adminProfileMessage');
        const togglePassword = document.getElementById('togglePassword');
        const adminPassword = document.getElementById('adminPassword');
        const passwordIcon = document.getElementById('passwordIcon');

        // Toggle password visibility
        togglePassword.addEventListener('click', () => {
            const type = adminPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            adminPassword.setAttribute('type', type);
            passwordIcon.classList.toggle('fa-eye');
            passwordIcon.classList.toggle('fa-eye-slash');
        });

        // Open admin profile modal
        adminProfileTrigger.addEventListener('click', () => {
            loadAdminProfile();
            adminProfileModal.style.display = 'flex';
        });

        // Close admin profile modal
        adminProfileClose.addEventListener('click', () => {
            adminProfileModal.style.display = 'none';
            adminProfileMessage.style.display = 'none';
        });

        adminProfileCancel.addEventListener('click', () => {
            adminProfileModal.style.display = 'none';
            adminProfileMessage.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === adminProfileModal) {
                adminProfileModal.style.display = 'none';
                adminProfileMessage.style.display = 'none';
            }
        });

        // Load admin profile data
        function loadAdminProfile() {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'admin_profile.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success && response.data) {
                                document.getElementById('adminUsername').value = response.data.name || '';
                                // Email field is optional (not in database)
                                document.getElementById('adminEmail').value = '';
                                adminPassword.value = '';
                            } else {
                                const errorMsg = response.message || 'Failed to load profile data';
                                showAdminMessage(errorMsg, 'error');
                                console.error('Profile load error:', response);
                            }
                        } catch (e) {
                            showAdminMessage('Error parsing response: ' + e.message, 'error');
                            console.error('Parse error:', e, 'Response:', xhr.responseText);
                        }
                    } else {
                        showAdminMessage('Server error (Status: ' + xhr.status + ')', 'error');
                        console.error('HTTP Error:', xhr.status, xhr.responseText);
                    }
                }
            };
            
            xhr.onerror = function() {
                showAdminMessage('Network error. Please check your connection.', 'error');
            };
            
            xhr.send('action=get');
        }

        // Handle form submission
        adminProfileForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(adminProfileForm);
            formData.append('action', 'update');
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'admin_profile.php', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                showAdminMessage(response.message || 'Profile updated successfully!', 'success');
                                if (response.newUsername) {
                                    const adminNameSpan = adminProfileTrigger.querySelector('span');
                                    if (adminNameSpan) {
                                        adminNameSpan.textContent = response.newUsername;
                                    }
                                }
                                adminPassword.value = '';
                                setTimeout(() => {
                                    adminProfileModal.style.display = 'none';
                                    adminProfileMessage.style.display = 'none';
                                }, 1500);
                            } else {
                                showAdminMessage(response.message || 'Failed to update profile', 'error');
                            }
                        } catch (e) {
                            showAdminMessage('Error processing response', 'error');
                        }
                    } else {
                        showAdminMessage('Server error. Please try again.', 'error');
                    }
                }
            };
            
            xhr.send(formData);
        });

        // Show message in modal
        function showAdminMessage(message, type) {
            adminProfileMessage.textContent = message;
            adminProfileMessage.style.display = 'block';
            if (type === 'success') {
                adminProfileMessage.style.background = '#10b981';
                adminProfileMessage.style.color = 'white';
            } else {
                adminProfileMessage.style.background = '#ef4444';
                adminProfileMessage.style.color = 'white';
            }
        }
    </script>

</body>
</html>