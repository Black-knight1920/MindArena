<?php
// Page d'accueil du backoffice avec design moderne

// Vérifier si les fichiers existant avant de les inclure
$donControllerPath = __DIR__ . "/Controller/DonController.php";
$orgControllerPath = __DIR__ . "/Controller/OrganisationController.php";

if (!file_exists($donControllerPath)) {
    die("Erreur: Fichier DonController.php non trouvé à: " . $donControllerPath);
}

if (!file_exists($orgControllerPath)) {
    die("Erreur: Fichier OrganisationController.php non trouvé à: " . $orgControllerPath);
}

require_once $donControllerPath;
require_once $orgControllerPath;

$donCtrl = new DonController();
$orgCtrl = new OrganisationController();

// Récupérer quelques statistiques pour le dashboard
try {
    $dons = $donCtrl->listDon()->fetchAll();
    $organisations = $orgCtrl->listOrganisations();

    $totalDons = 0;
    foreach ($dons as $don) {
        $totalDons += $don['montant'];
    }

    $totalOrganisations = count($organisations);
    $moyenneDon = $totalOrganisations > 0 ? $totalDons / $totalOrganisations : 0;
    
} catch (Exception $e) {
    // En cas d'erreur, initialiser avec des valeurs par défaut
    $totalDons = 0;
    $totalOrganisations = 0;
    $moyenneDon = 0;
    $dons = [];
    $organisations = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mind Arena</title>
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Remix Icons pour le dark mode -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        /* ================= THEME VARIABLES ================= */
        :root {
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

            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        body.dark {
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
            background: rgba(15,23,42,0.75);
            position: relative;
            cursor: pointer;
            transition: background .25s ease;
        }

        body:not(.dark) .theme-toggle {
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

        body:not(.dark) .theme-toggle-thumb {
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
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        /* Simple utilities */
        .text-muted-small {
            font-size: 12px;
            color: var(--text-muted);
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

<body class="light">
    <div class="admin-shell">
        <!-- ======= Sidebar ======= -->
        <div class="admin-sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-logo">
                    <i class="bi bi-rocket-takeoff"></i>
                </div>
                <div class="sidebar-title">
                    <span>Mind Arena</span>
                    <span>Backoffice</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-nav-label">Navigation</div>
                <a href="#" class="sidebar-link active">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>

                <div class="sidebar-nav-label">Gestion</div>
                <a href="View/backoffice/don/donList.php" class="sidebar-link">
                    <i class="bi bi-currency-euro"></i>
                    <span>Liste des Dons</span>
                </a>
                <a href="View/backoffice/organisation/organisationList.php" class="sidebar-link">
                    <i class="bi bi-building"></i>
                    <span>Organisations</span>
                </a>

                <div class="sidebar-nav-label">Outils</div>
                <a href="View/frontoffice/stats-live.php" target="_blank" class="sidebar-link">
                    <i class="bi bi-graph-up"></i>
                    <span>Stats Live</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <span class="text-muted-small">© 2024 Mind Arena</span>
                <a href="View/frontoffice/index.php">
                    <i class="bi bi-eye"></i>
                    Voir le site
                </a>
            </div>
        </div>

        <!-- ======= Main Content ======= -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <div>
                        <div class="header-left-title">Tableau de Bord</div>
                        <div class="header-left-sub">Bienvenue dans l'administration Mind Arena</div>
                    </div>
                </div>

                <div class="header-search">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>

                <div class="header-right">
                    <div class="theme-toggle-wrap">
                        <span>Mode</span>
                        <div class="theme-toggle" id="themeToggle">
                            <div class="theme-toggle-thumb">
                                <i class="ri-sun-fill"></i>
                            </div>
                        </div>
                    </div>

                    <div class="header-user">
                        <div class="user-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <span>Administrateur</span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                <div class="content-inner">
                    <!-- Alert -->
                    <div class="alert-custom">
                        <div class="d-flex">
                            <i class="bi bi-info-circle-fill"></i>
                            <div>
                                <h5>Information Importante</h5>
                                <p>Les dons ne peuvent pas être modifiés après enregistrement pour garantir l'intégrité des données financières.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Section -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-currency-euro"></i>
                            </div>
                            <div class="stat-value"><?= number_format($totalDons, 2) ?> €</div>
                            <div class="stat-title">Total des Dons</div>
                            <div class="stat-action">
                                <a href="View/backoffice/don/donList.php" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>Voir les dons
                                </a>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="stat-value"><?= $totalOrganisations ?></div>
                            <div class="stat-title">Organisations</div>
                            <div class="stat-action">
                                <a href="View/backoffice/organisation/organisationList.php" class="btn btn-outline-primary">
                                    <i class="bi bi-gear"></i>Gérer
                                </a>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="stat-value"><?= count($dons) ?></div>
                            <div class="stat-title">Nombre de Dons</div>
                            <div class="stat-action">
                                <a href="View/backoffice/don/donList.php" class="btn btn-outline-primary">
                                    <i class="bi bi-list"></i>Voir la liste
                                </a>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-broadcast"></i>
                            </div>
                            <div class="stat-value">Live Stats</div>
                            <div class="stat-title">En direct</div>
                            <div class="stat-action">
                                <a href="View/frontoffice/stats-live.php" target="_blank" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>Voir en direct
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <h2 class="section-title">Actions Rapides</h2>
                    <div class="actions-grid">
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="bi bi-building"></i>
                            </div>
                            <h3 class="action-title">Nouvelle Organisation</h3>
                            <p class="action-description">
                                Ajouter une nouvelle association partenaire à la plateforme de collecte
                            </p>
                            <a href="View/backoffice/organisation/addOrganisation.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i>Nouvelle Organisation
                            </a>
                        </div>

                        <div class="action-card">
                            <div class="action-icon">
                                <i class="bi bi-eye"></i>
                            </div>
                            <h3 class="action-title">Voir le Site</h3>
                            <p class="action-description">
                                Accéder à la version publique pour tester l'expérience utilisateur
                            </p>
                            <a href="View/frontoffice/index.php" class="btn btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right"></i>Visiter le Site
                            </a>
                        </div>

                        <div class="action-card">
                            <div class="action-icon">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <h3 class="action-title">Stats Live</h3>
                            <p class="action-description">
                                Visualiser et partager les statistiques en temps réel
                            </p>
                            <a href="View/frontoffice/stats-live.php" target="_blank" class="btn btn-primary">
                                <i class="bi bi-broadcast"></i>Voir Stats Live
                            </a>
                        </div>
                    </div>
                </div>
            </div>
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
                    body.classList.remove('dark');
                    if (thumb) thumb.innerHTML = '<i class="ri-sun-fill"></i>';
                } else {
                    body.classList.add('dark');
                    if (thumb) thumb.innerHTML = '<i class="ri-moon-fill"></i>';
                }
                localStorage.setItem('ma-admin-theme', theme);
            }

            // Initial
            const saved = localStorage.getItem('ma-admin-theme') || 'light';
            applyTheme(saved);

            if (toggle) {
                toggle.addEventListener('click', function () {
                    const next = body.classList.contains('dark') ? 'light' : 'dark';
                    applyTheme(next);
                });
            }
        })();

        // Animation des cartes
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .action-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>