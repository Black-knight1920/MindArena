<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Management</title>
    <link rel="stylesheet" href="assets/css/list.css" >
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modal styling same as your original code */
        /* Edit User Modal Styling */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: var(--card-bg, #fff); /* matches card-shell background */
            color: var(--text-color, #333);
            border-radius: 12px;
            padding: 25px 30px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
            animation: fadeIn 0.3s ease;
        }

        .modal-content h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 1.5rem;
            color: var(--primary-color, #333);
        }

        .modal-content label {
            font-weight: 500;
            font-size: 0.9rem;
            margin-top: 12px;
            margin-bottom: 5px;
            display: block;
        }

        .modal-content input {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.2s ease;
        }

        .modal-content input:focus {
            border-color: var(--primary-color, #007bff);
            outline: none;
        }

        .btn-save {
            background-color: var(--primary-color, #007bff);
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background 0.2s ease;
        }

        .btn-save:hover {
            background-color: #0056b3;
        }

        .close {
            align-self: flex-end;
            font-size: 22px;
            font-weight: bold;
            color: #888;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .close:hover {
            color: var(--primary-color, #007bff);
        }

        /* Fade in animation */
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

    </style>
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
                <li><a href="<?= BASE_URL ?>/admin_index.php" class="sidebar-link"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="<?= BASE_URL ?>/users.php" class="sidebar-link active"><i class="fas fa-users"></i> Users</a></li>
                <!--<li><a href="#" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="#" class="sidebar-link"><i class="fas fa-chart-bar"></i> Statistics</a></li>-->
            </ul>

            <div class="sidebar-footer">
                <a href="<?= BASE_URL ?>/index.php?action=login"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <div class="admin-header">
                <div class="header-left">
                    <div>
                        <div class="header-left-title">User Management</div>
                        <div class="header-left-sub">Manage system users</div>
                    </div>
                </div>

                <div class="header-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search users...">
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

                    <div class="header-user">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span><?php echo $_SESSION["admin"] ?></span>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="admin-content">
                <div class="content-inner">
                    <div class="page-header" style="display:flex; justify-content: space-between; align-items:center;">
                        <h1 class="page-title">User Management</h1>
                        <button id="openCreateUserModal" class="btn-create" style="cursor:pointer;">
                            <i class="fas fa-plus"></i> Create New User
                        </button>
                    </div>

                    <!-- User List -->
                    <div class="user-list">
                        <?php if (!empty($data['users'])): ?>
                            <?php foreach ($data['users'] as $user): ?>
                                <div class="user-card card-shell">
                                    <div class="user-info">
                                        <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                        <p><strong>Password:</strong> <?php echo htmlspecialchars($user['mdp']); ?></p>
                                        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['date-naissance']); ?></p>
                                        <p><strong>Date Joined:</strong> <?php echo htmlspecialchars($user['date-inscrit']); ?></p>
                                        <p><strong>Donation:</strong> <?php echo htmlspecialchars($user['donation']); ?></p>
                                    </div>
                                    <div class="user-actions">
                                        <!-- Edit Button -->
                                        <button type="button" class="btn-edit" 
                                            data-user-id="<?php echo $user['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                            data-dob="<?php echo htmlspecialchars($user['date-naissance']); ?>"
                                            data-donation="<?php echo htmlspecialchars($user['donation']); ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <!-- Delete Form -->
                                        <form action="users.php" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn-delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-data card-shell">
                                <p>No users found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content card-shell">
            <span class="close" id="editModalClose" style="cursor:pointer; font-size: 22px;">&times;</span>
            <h2>Edit User</h2>

            <form id="editUserForm" method="post" action="users.php">
                <input type="hidden" name="user_id" id="editUserId" value="">

                <label for="editName">Name:</label>
                <input type="text" id="editName" name="name" required>

                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="email" required>

                <label for="editDob">Date of Birth:</label>
                <input type="date" id="editDob" name="date_naissance" required>

                <label for="editDonation">Donation:</label>
                <input type="number" id="editDonation" name="donation" min="0" step="0.01" required>

                <button type="submit" name="edit_user" class="btn-save" style="margin-top: 10px;">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Create User Modal -->
    <div id="createUserModal" class="modal">
        <div class="modal-content card-shell">
            <span class="close" id="createModalClose" style="cursor:pointer; font-size: 22px;">&times;</span>
            <h2>Create New User</h2>

            <form id="createUserForm" method="post" action="users.php">
    <label for="createName">Name:</label>
    <input type="text" id="createName" name="name" required>

    <label for="createEmail">Email:</label>
    <input type="email" id="createEmail" name="email" required>

    <label for="createPassword">Password:</label>
    <input type="password" id="createPassword" name="mdp" required minlength="6">

    <label for="createDob">Date of Birth:</label>
    <input type="date" id="createDob" name="date_naissance" required>

    <label for="createDonation">Donation:</label>
    <input type="number" id="createDonation" name="donation" min="0" step="0.01" required>

    <!-- Hidden input to handle create action -->
    <input type="hidden" name="create_user" value="1">

    <button type="submit" class="btn-save" style="margin-top: 10px;">Create User</button>
</form>

        </div>
    </div>

    <script>
        // Modal input validators
        const modalValidators = {
            editName: value => /^[a-zA-Z0-9_ ]{3,}$/.test(value),
            editEmail: value => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value),
            editDob: value => {
                const birth = new Date(value);
                const age = new Date().getFullYear() - birth.getFullYear();
                return age >= 18;
            },
            editDonation: value => !isNaN(value) && Number(value) >= 0
        };

        // Show + Clear error
        const showError = (input, message) => {
            clearError(input);
            const e = document.createElement("span");
            e.classList.add("error-message");
            e.textContent = message;
            input.classList.add("error");
            input.parentElement.appendChild(e);
        };

        const clearError = (input) => {
            input.classList.remove("error");
            const e = input.parentElement.querySelector(".error-message");
            if (e) e.remove();
        };

        // Real-time validation for Edit User Form
        document.querySelectorAll("#editUserForm input").forEach(input => {
            input.addEventListener("input", () => {
                const id = input.id;
                const value = input.value.trim();
                if (value === "") { clearError(input); return; }

                if (!modalValidators[id](value)) {
                    let msg = {
                        editName: "Name must be at least 3 characters.",
                        editEmail: "Enter a valid email.",
                        editDob: "User must be at least 18 years old.",
                        editDonation: "Donation must be 0 or higher."
                    };
                    showError(input, msg[id]);
                } else {
                    clearError(input);
                }
            });
        });

        // Validate Edit User Form on submit
        document.getElementById("editUserForm").addEventListener("submit", (e) => {
            let valid = true;
            document.querySelectorAll("#editUserForm input").forEach(input => {
                const id = input.id;
                if (!modalValidators[id](input.value.trim())) {
                    let msg = {
                        editName: "Invalid name.",
                        editEmail: "Invalid email.",
                        editDob: "Invalid birth date.",
                        editDonation: "Invalid donation amount."
                    };
                    showError(input, msg[id]);
                    valid = false;
                }
            });
            if (!valid) e.preventDefault();
        });

        document.addEventListener('DOMContentLoaded', () => {
            // Edit User Modal elements
            const editUserModal = document.getElementById('editUserModal');
            const editModalClose = document.getElementById('editModalClose');

            function openEditModal(userData) {
                document.getElementById('editUserId').value = userData.id;
                document.getElementById('editName').value = userData.name;
                document.getElementById('editEmail').value = userData.email;
                document.getElementById('editDob').value = userData.dob;
                document.getElementById('editDonation').value = userData.donation;

                editUserModal.style.display = 'flex';
            }

            // Close Edit Modal
            editModalClose.addEventListener('click', () => {
                editUserModal.style.display = 'none';
            });

            window.addEventListener('click', (e) => {
                if (e.target === editUserModal) {
                    editUserModal.style.display = 'none';
                }
            });

            // Attach event listeners to edit buttons
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const userData = {
                        id: button.dataset.userId,
                        name: button.dataset.name,
                        email: button.dataset.email,
                        dob: button.dataset.dob,
                        donation: button.dataset.donation
                    };

                    openEditModal(userData);
                });
            });

            // Create User Modal elements
            const createUserModal = document.getElementById('createUserModal');
            const createModalClose = document.getElementById('createModalClose');
            const openCreateUserModalBtn = document.getElementById('openCreateUserModal');

            // Create modal validators
            const createValidators = {
                createName: value => /^[a-zA-Z0-9_ ]{3,}$/.test(value),
                createEmail: value => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value),
                createPassword: value => value.length >= 6,
                createDob: value => {
                    const birth = new Date(value);
                    const age = new Date().getFullYear() - birth.getFullYear();
                    return age >= 18;
                },
                createDonation: value => !isNaN(value) && Number(value) >= 0
            };

            // Real-time validation for Create User Form
            document.querySelectorAll("#createUserForm input").forEach(input => {
                input.addEventListener("input", () => {
                    const id = input.id;
                    const value = input.value.trim();
                    if (value === "") { clearError(input); return; }

                    if (!createValidators[id](value)) {
                        let msg = {
                            createName: "Name must be at least 3 characters.",
                            createEmail: "Enter a valid email.",
                            createPassword: "Password must be at least 6 characters.",
                            createDob: "User must be at least 18 years old.",
                            createDonation: "Donation must be 0 or higher."
                        };
                        showError(input, msg[id]);
                    } else {
                        clearError(input);
                    }
                });
            });

            // Validate Create User Form on submit
            document.getElementById("createUserForm").addEventListener("submit", (e) => {
                let valid = true;
                document.querySelectorAll("#createUserForm input").forEach(input => {
                    const id = input.id;
                    if (!createValidators[id](input.value.trim())) {
                        let msg = {
                            createName: "Invalid name.",
                            createEmail: "Invalid email.",
                            createPassword: "Invalid password.",
                            createDob: "Invalid birth date.",
                            createDonation: "Invalid donation amount."
                        };
                        showError(input, msg[id]);
                        valid = false;
                    }
                });
                if (!valid) e.preventDefault();
            });

            // Open Create User Modal
            openCreateUserModalBtn.addEventListener('click', () => {
                // Clear previous inputs and errors
                document.getElementById('createUserForm').reset();
                document.querySelectorAll("#createUserForm input").forEach(input => clearError(input));
                createUserModal.style.display = 'flex';
            });

            // Close Create User Modal
            createModalClose.addEventListener('click', () => {
                createUserModal.style.display = 'none';
            });

            window.addEventListener('click', (e) => {
                if (e.target === createUserModal) {
                    createUserModal.style.display = 'none';
                }
            });

            // Theme toggle
            document.getElementById('themeToggle').addEventListener('click', () => {
                document.body.classList.toggle('light');
            });
        });
    </script>
</body>
</html>

