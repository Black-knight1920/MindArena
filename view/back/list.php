<?php
// list.php

require_once "database.php";
// Fetch all users from database
$query = $conn->query("SELECT * FROM user ORDER BY id DESC");
$users = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="assets/css/list.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2 class="logo">Mind Arena</h2>
        <ul class="menu">
            <li><a href="http://127.0.0.1/projet-Copie%20-%20Copie/view/back/back.php">Dashboard</a></li>
            <li class="active">Users</li>
            <li>Settings</li>
            <li>Statistics</li>
        </ul>
        <form action="logout.php" method="post">
            <button class="logout-btn"><a href="http://127.0.0.1/projet-Copie%20-%20Copie/view/front/login.html">Logout</a></button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="header">
            <h1>User Management</h1>
            <a href="create_user.php" class="btn-create">Create New User</a>
        </div>

        <!-- User List -->
        <div class="user-list">
            <?php
            if (!empty($users)) {
                foreach ($users as $user) {
                    ?>
                    <div class="user-card">
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
                            <form action="edit_user.php" method="post">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn-edit">Edit</button>
                            </form>
                            <form action="delete_user.php" method="post" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No users found.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>
