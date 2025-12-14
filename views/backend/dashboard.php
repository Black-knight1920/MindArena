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
                <li><a href="<?= BASE_URL ?>/admin_index.php" class="sidebar-link active"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="<?= BASE_URL ?>/users.php" class="sidebar-link"><i class="fas fa-users"></i> Users</a></li>
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

    <script>
        // Theme toggle functionality
        document.getElementById('themeToggle').addEventListener('click', function() {
            document.body.classList.toggle('light');
        });
    </script>

</body>
</html>

