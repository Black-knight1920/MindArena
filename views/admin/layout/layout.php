<?php
// Ensure BASE_URL is defined
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../../config/constants.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= isset($pageTitle) ? $pageTitle : 'Admin Panel' ?> - Admin Panel</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ================= THEME VARIABLES ================= */
        /* DARK MODE (default) */
        :root {
            --bg: #0f1018;
            --bg-soft: #151626;
            --sidebar-bg: #11121b;
            --sidebar-border: rgba(255,255,255,0.05);
            --sidebar-text: #e5e7eb;
            --sidebar-muted: #9ca3af;

            --header-bg: rgba(15,16,24,0.96);

            --card-bg: #181927;
            --card-border: rgba(148,163,184,0.25);

            --primary: #8b5cf6;
            --primary-soft: rgba(139,92,246,0.13);
            --primary-hover: #a855f7;

            --text: #f9fafb;
            --text-muted: #9ca3af;
            --border-subtle: rgba(148,163,184,0.25);

            --shadow-soft: 0 18px 45px rgba(15,23,42,0.55);

            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        /* LIGHT MODE */
        body.light {
            --bg: #ffffff;
            --bg-soft: #f8fafc;
            --sidebar-bg: #ffffff;
            --sidebar-border: rgba(15,23,42,0.06);
            --sidebar-text: #111827;
            --sidebar-muted: #6b7280;

            --header-bg: rgba(255,255,255,0.96);

            --card-bg: #ffffff;
            --card-border: rgba(15,23,42,0.07);

            --primary: #8b5cf6;
            --primary-soft: rgba(139,92,246,0.09);
            --primary-hover: #7c3aed;

            --text: #111827;
            --text-muted: #6b7280;
            --border-subtle: rgba(148,163,184,0.35);

            --shadow-soft: 0 18px 40px rgba(15,23,42,0.12);
        }

        /* ================= GLOBAL ================= */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
            transition: background .25s ease, color .25s ease;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ================= LAYOUT ================= */
        .admin-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ---------- SIMPLE SIDEBAR ---------- */
        .admin-sidebar {
            width: 230px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            padding: 18px 16px 18px;
            transition: background .25s ease, border-color .25s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 4px 18px;
            border-bottom: 1px solid var(--sidebar-border);
            margin-bottom: 16px;
        }

        .sidebar-logo {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            box-shadow: 0 10px 25px rgba(139,92,246,0.6);
        }

        .sidebar-title {
            display: flex;
            flex-direction: column;
        }
        .sidebar-title span:first-child {
            font-size: 14px;
            font-weight: 700;
            color: var(--sidebar-text);
        }
        .sidebar-title span:last-child {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .18em;
            color: var(--sidebar-muted);
        }

        .sidebar-nav {
            margin-top: 10px;
            list-style: none;
            padding: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 9px;
            font-size: 14px;
            color: var(--sidebar-text);
            transition: background .18s ease, color .18s ease, transform .12s ease;
            margin-bottom: 4px;
        }

        .sidebar-link i {
            font-size: 17px;
        }

        .sidebar-link:hover {
            background: var(--primary-soft);
            color: var(--primary);
            transform: translateX(2px);
        }

        .sidebar-link.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 0 0 1px rgba(255,255,255,0.08), 0 12px 25px rgba(139,92,246,0.6);
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px dashed var(--sidebar-border);
            font-size: 12px;
            color: var(--sidebar-muted);
        }

        .sidebar-footer a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            margin-top: 6px;
            padding: 6px 8px;
            border-radius: 999px;
            transition: background .18s ease, color .18s ease;
        }

        .sidebar-footer a:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }

        /* ---------- MAIN AREA ---------- */
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* TOP BAR */
        .admin-header {
            position: sticky;
            top: 0;
            z-index: 20;
            height: 60px;
            background: var(--header-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 22px;
            transition: background .25s ease, border-color .25s ease;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .header-left-title {
            font-weight: 600;
            color: var(--text);
        }

        .header-left-sub {
            font-size: 12px;
            color: var(--text-muted);
        }

        .header-search {
            flex: 0 0 280px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            background: var(--card-bg);
            border-radius: 999px;
            border: 1px solid var(--border-subtle);
            transition: background .25s ease, border-color .25s ease;
        }

        .header-search input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 13px;
            background: transparent;
            color: var(--text);
        }

        .header-search i {
            font-size: 16px;
            color: var(--text-muted);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Toggle mode */
        .theme-toggle-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--text-muted);
        }

        .theme-toggle {
            width: 46px;
            height: 22px;
            border-radius: 999px;
            background: #3b3b54;
            position: relative;
            cursor: pointer;
            transition: background .25s ease;
        }

        body.light .theme-toggle {
            background: rgba(148,163,184,0.6);
        }

        .theme-toggle-thumb {
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: #f9fafb;
            position: absolute;
            top: 2px;
            left: 3px;
            transition: transform .22s ease;
            box-shadow: 0 6px 16px rgba(15,23,42,0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #0f172a;
        }

        body.light .theme-toggle-thumb {
            transform: translateX(22px);
            background: #ffffff;
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
            box-shadow: 0 10px 20px rgba(139,92,246,0.7);
        }

        /* CONTENT WRAP */
        .admin-content {
            flex: 1;
            padding: 24px 24px 26px;
            min-height: calc(100vh - 60px);
            background: var(--bg-soft);
            transition: background .25s ease;
        }

        .content-inner {
            max-width: 1320px;
            margin: 0 auto;
        }

        .card-shell {
            background: var(--card-bg);
            border-radius: 18px;
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow-soft);
            padding: 22px 22px 20px;
            transition: background .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        /* ================= DASHBOARD STYLES ================= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--card-border);
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--primary), #a855f7);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            color: white;
            box-shadow: 0 10px 20px rgba(139,92,246,0.3);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 8px;
        }

        .stat-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        .stat-action {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* Action Cards */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .action-card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--card-border);
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .action-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            color: white;
            box-shadow: 0 12px 25px rgba(139,92,246,0.4);
        }

        .action-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 12px;
        }

        .action-description {
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Alert */
        .alert-custom {
            background: linear-gradient(135deg, var(--info), #3b82f6);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 20px 24px;
            margin: 0 0 30px 0;
            box-shadow: 0 10px 25px rgba(59,130,246,0.3);
        }

        .alert-custom .d-flex {
            align-items: flex-start;
        }

        .alert-custom i {
            font-size: 24px;
            margin-right: 16px;
            margin-top: 2px;
        }

        .alert-custom h5 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .alert-custom p {
            margin-bottom: 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        /* Button styles */
        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(139,92,246,0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139,92,246,0.4);
            background: linear-gradient(135deg, var(--primary-hover), #9333ea);
        }

        .btn-outline-primary {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139,92,246,0.3);
        }

        /* Section titles */
        .section-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 20px;
        }

        /* Tables */
        .table-glass {
            width: 100%;
            background: var(--card-bg);
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border-subtle);
            backdrop-filter: blur(8px);
            box-shadow: var(--shadow-soft);
        }

        .table-glass thead {
            background: linear-gradient(135deg, var(--primary), #a855f7);
        }

        .table-glass th {
            font-size: 14px;
            padding: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
        }

        .table-glass td {
            padding: 14px;
            color: var(--text);
            border-bottom: 1px solid var(--border-subtle);
        }

        .table-glass tr:hover {
            background: var(--primary-soft);
            transition: .22s;
        }

        /* Form styles */
        .form-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 16px;
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-soft);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-control {
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            color: var(--text);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        /* Simple utilities */
        .text-muted-small {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Notification dropdown */
        .notification-dropdown .dropdown-menu {
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            box-shadow: var(--shadow-soft);
        }

        .notification-dropdown .dropdown-header {
            color: var(--text);
            font-weight: 600;
            border-bottom: 1px solid var(--border-subtle);
        }

        .notification-dropdown .dropdown-item {
            color: var(--text);
            padding: 10px 15px;
            border-bottom: 1px solid var(--border-subtle);
            transition: background 0.2s;
        }

        .notification-dropdown .dropdown-item:hover {
            background: var(--primary-soft);
        }

        .notification-badge {
            font-size: 10px;
            padding: 4px 6px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-weight: 700;
            background: var(--danger) !important;
            border: 2px solid var(--card-bg);
            box-shadow: 0 2px 4px rgba(239,68,68,0.4);
        }

        .btn-notification {
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            color: var(--text);
            border-radius: 999px;
            padding: 8px;
            transition: all 0.3s;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .btn-notification:hover {
            background: var(--primary-soft);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        /* Make sure all text elements use the correct color */
        h1, h2, h3, h4, h5, h6 {
            color: var(--text);
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        @media (max-width: 960px) {
            .admin-sidebar {
                display: none;
            }
            .admin-header {
                padding-inline: 14px;
            }
            .header-search {
                display: none;
            }
            .admin-content {
                padding-inline: 14px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .actions-grid {
                grid-template-columns: 1fr;
            }
            .stat-action {
                flex-direction: column;
            }
        }
    </style>
</head>

<body class="admin-shell">

    <!-- SIDEBAR -->
    <div class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo">
                <i class="ri-shield-user-fill"></i>
            </div>
            <div class="sidebar-title">
                <span>MindArena</span>
                <span>ADMIN PANEL</span>
            </div>
        </div>

        <ul class="sidebar-nav">
            <li>
                <a href="<?= BASE_URL ?>/admin.php?action=dashboard"
                   class="sidebar-link <?= (isset($active) && $active=='dashboard') ? 'active' : '' ?>">
                    <i class="ri-dashboard-line"></i> Dashboard
                </a>
            </li>

            <li>
                <a href="<?= BASE_URL ?>/admin.php?action=forums"
                   class="sidebar-link <?= (isset($active) && $active=='forums') ? 'active' : '' ?>">
                    <i class="ri-folder-3-line"></i> Forums
                </a>
            </li>

            <li>
                <a href="<?= BASE_URL ?>/admin.php?action=user-stats"
                   class="sidebar-link <?= (isset($active) && $active=='user-stats') ? 'active' : '' ?>">
                    <i class="ri-user-line"></i> Utilisateurs
                </a>
            </li>

            <li>
                <a href="<?= BASE_URL ?>/admin.php?action=reports"
                   class="sidebar-link <?= (isset($active) && $active=='reports') ? 'active' : '' ?>">
                    <i class="ri-flag-2-line"></i> Signalements
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="text-muted-small">
                Connecté en tant que <strong>Admin</strong>
            </div>
            <a href="<?= BASE_URL ?>/index.php">
                <i class="ri-arrow-left-line"></i> Retour au site
            </a>
        </div>
    </div>

    <!-- MAIN AREA -->
    <div class="admin-main">
        <!-- HEADER -->
        <div class="admin-header">
            <div class="header-left">
                <span class="header-left-title"><?= isset($pageTitle) ? $pageTitle : 'Tableau de bord' ?></span>
                <?php if (isset($pageSubtitle)): ?>
                    <span class="header-left-sub"><?= $pageSubtitle ?></span>
                <?php endif; ?>
            </div>

            <div class="header-search">
                <i class="ri-search-line"></i>
                <input type="text" placeholder="Rechercher...">
            </div>

            <div class="header-right">
                <!-- Theme Toggle -->
                <div class="theme-toggle-wrap">
                    <span>MODE</span>
                    <div class="theme-toggle" id="themeToggle">
                        <div class="theme-toggle-thumb">
                            <i class="ri-moon-fill"></i>
                        </div>
                    </div>
                </div>

                <!-- Notification Dropdown -->
                <div class="dropdown notification-dropdown">
                    <button class="btn btn-notification position-relative" data-bs-toggle="dropdown">
                        <i class="ri-notification-3-line"></i>
                        <?php if (isset($unread) && $unread > 0): ?>
                        <span class="badge notification-badge position-absolute top-0 start-100 translate-middle"><?= $unread ?></span>
                        <?php endif; ?>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end p-2" style="min-width:260px;max-height:300px;overflow-y:auto;">
                        <h6 class="dropdown-header">Notifications</h6>

                        <?php if (empty($notifications)): ?>
                            <div class="text-muted small px-2 py-1">Aucune notification</div>
                        <?php else: ?>
                            <?php foreach ($notifications as $n): ?>
                                <div class="dropdown-item border-bottom small">
                                    <strong><?= htmlspecialchars($n['title']) ?></strong><br>
                                    <span class="text-muted"><?= htmlspecialchars($n['message']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- User -->
                <div class="header-user">
                    <span style="color: var(--text);">Admin</span>
                    <div class="user-avatar">
                        <i class="ri-user-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="admin-content">
            <div class="content-inner">
                <?php 
                // Session already started in admin.php, just display flashes
                if (!empty($_SESSION['_flash'])) {
                    foreach ($_SESSION['_flash'] as $f) {
                        $type = $f['type'] ?? 'info';
                        $msg = htmlspecialchars($f['message'] ?? '');
                        $cls = $type === 'success' ? 'alert-success' : ($type === 'error' ? 'alert-danger' : 'alert-info');
                        echo "<div class=\"alert {$cls}\" role=\"alert\" style=\"border-radius:12px;margin-bottom:16px;\">{$msg}</div>";
                    }
                    unset($_SESSION['_flash']);
                }

                if (isset($viewFile) && file_exists($viewFile)) {
                    include $viewFile; 
                } else {
                    echo '<div class="card-shell"><h3>Erreur</h3><p>La vue demandée est introuvable.</p></div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Switcher Script -->
    <script>
    // Thème dark / light synchronisé avec localStorage
    (function () {
        const body = document.body;
        const toggle = document.getElementById('themeToggle');
        const thumb = document.querySelector('.theme-toggle-thumb');

        function applyTheme(theme) {
            if (theme === 'light') {
                body.classList.add('light');
                if (thumb) thumb.innerHTML = '<i class="ri-sun-fill"></i>';
            } else {
                body.classList.remove('light');
                if (thumb) thumb.innerHTML = '<i class="ri-moon-fill"></i>';
            }
            localStorage.setItem('ma-admin-theme', theme);
        }

        // Initial - default to dark mode
        const saved = localStorage.getItem('ma-admin-theme') || 'dark';
        applyTheme(saved);

        if (toggle) {
            toggle.addEventListener('click', function () {
                const next = body.classList.contains('light') ? 'dark' : 'light';
                applyTheme(next);
            });
        }
    })();
    </script>
</body>
</html>
