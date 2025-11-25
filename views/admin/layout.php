<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> – Admin Panel</title>

    <!-- Bootstrap (optionnel, pour la grille & quelques utilitaires) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

    <style>
        /* ================= THEME VARIABLES ================= */
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
        }

        body.light {
            --bg: #f4f5fb;
            --bg-soft: #ffffff;
            --sidebar-bg: #ffffff;
            --sidebar-border: rgba(15,23,42,0.06);
            --sidebar-text: #111827;
            --sidebar-muted: #6b7280;

            --header-bg: rgba(255,255,255,0.96);

            --card-bg: #ffffff;
            --card-border: rgba(15,23,42,0.07);

            --primary: #7c3aed;
            --primary-soft: rgba(124,58,237,0.09);
            --primary-hover: #6d28d9;

            --text: #111827;
            --text-muted: #6b7280;
            --border-subtle: rgba(148,163,184,0.35);

            --shadow-soft: 0 18px 40px rgba(15,23,42,0.12);
        }

        /* ================= GLOBAL ================= */
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif;
            background: radial-gradient(circle at top left, rgba(139,92,246,0.16), transparent 55%),
                        radial-gradient(circle at bottom right, rgba(236,72,153,0.14), transparent 55%),
                        var(--bg);
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
            box-shadow: 0 10px 25px rgba(79,70,229,0.6);
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

        .sidebar-nav-label {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--sidebar-muted);
            letter-spacing: .12em;
            margin: 10px 6px 6px;
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
            box-shadow: 0 0 0 1px rgba(255,255,255,0.08), 0 12px 25px rgba(88,28,135,0.6);
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
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .header-left-title {
            font-weight: 600;
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
            background: rgba(15,23,42,0.4);
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.35);
        }

        body.light .header-search {
            background: rgba(255,255,255,0.7);
            border-color: rgba(148,163,184,0.5);
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
            background: rgba(15,23,42,0.75);
            position: relative;
            cursor: pointer;
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
            box-shadow: 0 10px 20px rgba(88,28,135,0.7);
        }

        /* CONTENT WRAP */
        .admin-content {
            flex: 1;
            padding: 24px 24px 26px;
            min-height: calc(100vh - 60px);
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
        }

        /* Simple utilities */
        .text-muted-small {
            font-size: 12px;
            color: var(--text-muted);
        }

        @media (max-width: 960px) {
            .admin-sidebar {
                display: none; /* version simple desktop only */
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
        }
    </style>
</head>

<body>

<div class="admin-shell">

    <!-- ============ SIDEBAR SIMPLE ============ -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo">
                <i class="ri-shield-user-line"></i>
            </div>
            <div class="sidebar-title">
                <span>MindArena</span>
                <span>Admin panel</span>
            </div>
        </div>

        <div class="sidebar-nav-label">Navigation</div>
        <ul class="sidebar-nav">
            <li>
                <a href="admin.php?action=dashboard"
                   class="sidebar-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="admin.php?action=forums"
                   class="sidebar-link <?= ($activeMenu ?? '') === 'forums' ? 'active' : '' ?>">
                    <i class="ri-layout-grid-line"></i>
                    <span>Forums</span>
                </a>
            </li>
            <li>
                <a href="admin.php?action=reports"
                   class="sidebar-link <?= ($activeMenu ?? '') === 'reports' ? 'active' : '' ?>">
                    <i class="ri-flag-2-line"></i>
                    <span>Signalements</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div>Connecté en tant que <strong>Admin</strong></div>
            <a href="index.php">
                <i class="ri-arrow-left-line"></i> Retour au site
            </a>
        </div>
    </aside>

    <!-- ============ MAIN AREA ============ -->
    <div class="admin-main">

        <!-- TOP BAR -->
        <header class="admin-header">
            <div class="header-left">
                <div class="header-left-title"><?= htmlspecialchars($pageTitle ?? '') ?></div>
                <?php if (!empty($pageSubtitle)): ?>
                    <div class="header-left-sub"><?= htmlspecialchars($pageSubtitle) ?></div>
                <?php endif; ?>
            </div>

            <div class="header-right">
                <div class="header-search">
                    <i class="ri-search-line"></i>
                    <input type="text" placeholder="Rechercher…">
                </div>

                <div class="theme-toggle-wrap">
                    <span>Mode</span>
                    <div id="themeToggle" class="theme-toggle">
                        <div class="theme-toggle-thumb">
                            <i class="ri-moon-fill"></i>
                        </div>
                    </div>
                </div>

                <div class="header-user">
                    <span class="text-muted-small">Admin</span>
                    <div class="user-avatar">
                        <i class="ri-user-3-line"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="admin-content">
            <div class="content-inner">
                <?php
                if (!empty($viewFile) && is_file($viewFile)) {
                    include $viewFile;
                } else {
                    echo '<div class="card-shell"><p class="text-muted-small">Vue introuvable.</p></div>';
                }
                ?>
            </div>
        </main>

    </div>
</div>

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

        // Initial
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
