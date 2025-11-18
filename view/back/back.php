<?php
require_once " D:\Program Files (x86)\EasyPHP-5.3.8.0\www\projet-Copie - Copie\database.php";

// TOTAL USERS
$totalUsersQuery = $conn->query("SELECT COUNT(*) FROM user");
$totalUsers = $totalUsersQuery->fetchColumn();

// NEW USERS (registered last 7 days)
$newUsersQuery = $conn->query("
    SELECT COUNT(*) 
    FROM user 
    WHERE `date-inscrit` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
");
$newUsers = $newUsersQuery->fetchColumn();

// ACTIVE SESSIONS FAKE
$activeSessionsQuery = $conn->query("
    SELECT COUNT(*) 
    FROM user 
    WHERE `donation` >= 1
"); 
$activeSessions = $activeSessionsQuery->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice – Dashboard</title>
    <link rel="stylesheet" href="assets/css/back.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2 class="logo">Mind Arena</h2>

        <ul class="menu">
            <li class="active">Dashboard</li>
            <li><a href="list.php">Users</a></li>
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
            <h1>Admin Dashboard</h1>
            <p>Welcome back, Admin!</p>
        </div>

        <div class="cards">
            <div class="card">
                <h3>New Users (7 days)</h3>
                <p class="number"><?php echo $newUsers;?></p>
            </div>

            <div class="card">
                <h3>Total Users</h3>
                <p class="number"><?php echo $totalUsers ?></p>
            </div>

            <div class="card">
                <h3>Active Sessions</h3>
                <p class="number"><?php echo $activeSessions ?></p>
            </div>
        </div>

        <div class="panel">
            <h2>Recent Activities</h2>
            <ul>
                <?php
                $recent = $conn->query("SELECT name, `date-inscrit` FROM user ORDER BY id DESC LIMIT 5");

                while ($row = $recent->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li><strong>{$row['name']}</strong> joined on {$row['date-inscrit']}.</li>";
                }
                ?>
            </ul>
        </div>

    </div>

</body>
</html>
