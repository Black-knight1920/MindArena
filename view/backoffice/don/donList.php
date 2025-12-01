<?php
require_once __DIR__."/../../../Controller/DonController.php";

$donCtrl = new DonController();
$dons = $donCtrl->listDon();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Dons - Mind Arena</title>
   
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

        /* ================= FILTER BAR ================= */
        .filter-bar {
            display: flex;
            gap: 14px;
            align-items: center;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 250px;
        }

        .filter-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .filter-label i {
            font-size: 1rem;
        }

        .filter-select {
            flex: 1;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--border-subtle);
            background: var(--card-bg);
            color: var(--text);
            font-size: 0.875rem;
            cursor: pointer;
            transition: border-color .2s ease, background .2s ease;
        }

        .filter-select:hover {
            border-color: var(--primary);
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .filter-clear-btn {
            padding: 8px 14px;
            border: none;
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .filter-clear-btn:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-1px);
        }

        .search-counter {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ================= TABLE STYLES ================= */
        .table-container {
            margin-top: 20px;
        }

        .table-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--card-border);
            padding: 16px;
            text-align: center;
            transition: background .25s ease, border-color .25s ease;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: background .25s ease, box-shadow .25s ease;
        }

        .modern-table th {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            color: white;
            padding: 16px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            user-select: none;
            transition: background .2s ease;
        }

        .modern-table th:hover {
            background: linear-gradient(135deg, #7c3aed, #9333ea);
        }

        .modern-table th .sort-icon {
            margin-left: 6px;
            font-size: 0.75rem;
            opacity: 0.6;
            transition: opacity .2s ease;
        }

        .modern-table th.sortable:hover .sort-icon {
            opacity: 1;
        }

        .modern-table td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--border-subtle);
            font-size: 0.9rem;
            transition: border-color .25s ease;
        }

        .modern-table tr:last-child td {
            border-bottom: none;
        }

        .modern-table tbody tr:hover {
            background: var(--primary-soft);
        }

        /* Badge styles */
        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-primary {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        /* Button styles */
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-outline {
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border-subtle);
        }

        .btn-outline:hover {
            background: var(--primary-soft);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Amount styling */
        .amount {
            font-weight: 700;
            color: var(--success);
        }

        .donor-name {
            font-weight: 600;
            color: var(--text);
        }

        .anonymous {
            color: var(--text-muted);
            font-style: italic;
        }

        /* Total row */
        .total-row {
            background: var(--primary-soft) !important;
            font-weight: 700;
        }

        .total-row td {
            border-top: 2px solid var(--primary);
            border-bottom: none !important;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
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

        .hidden {
            display: none;
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
            .modern-table {
                display: block;
                overflow-x: auto;
            }
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-group {
                min-width: 100%;
            }
        }
    </style>
</head>

<body>
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
                <a href="/projet-dons/backoffice.php" class="sidebar-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>

                <div class="sidebar-nav-label">Gestion</div>
                <a href="donList.php" class="sidebar-link active">
                    <i class="bi bi-currency-euro"></i>
                    <span>Liste des Dons</span>
                </a>
                <a href="../organisation/organisationList.php" class="sidebar-link">
                    <i class="bi bi-building"></i>
                    <span>Organisations</span>
                </a>
                <a href="../organisation/addOrganisation.php" class="sidebar-link">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nouvelle Organisation</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <span class="text-muted-small">© 2024 Mind Arena</span>
                <a href="/projet-dons/View/frontoffice/index.php">
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
                        <div class="header-left-title">Liste des Dons</div>
                        <div class="header-left-sub">Gestion des donations</div>
                    </div>
                </div>

                <div class="header-search">
                    <i class="bi bi-search"></i>
                    <input type="text" id="headerSearch" placeholder="Rechercher un don...">
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
                        <span>Admin</span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                <div class="content-inner">
                    <div class="card-shell">
                        <!-- Stats -->
                        <?php
                        $donsData = $dons->fetchAll();
                        $total = 0;
                        $totalDons = count($donsData);
                        foreach ($donsData as $d) {
                            $total += $d['montant'];
                        }
                        ?>

                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-value"><?= $totalDons ?></div>
                                <div class="stat-label">Total des Dons</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?= number_format($total, 2) ?> €</div>
                                <div class="stat-label">Montant Total</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?= $totalDons > 0 ? number_format($total / $totalDons, 2) : '0.00' ?> €</div>
                                <div class="stat-label">Moyenne par Don</div>
                            </div>
                        </div>

                        <!-- Filter Bar -->
                        <div class="filter-bar">
                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="bi bi-search"></i>
                                    Recherche
                                </label>
                                <input type="text" id="filterSearch" class="filter-select" placeholder="Par donateur, organisation ou montant...">
                            </div>

                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="bi bi-tag"></i>
                                    Type
                                </label>
                                <select id="filterType" class="filter-select">
                                    <option value="">Tous les Types</option>
                                    <option value="Monétaire">Monétaire</option>
                                    <option value="Matériel">Matériel</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="bi bi-calendar"></i>
                                    Période
                                </label>
                                <select id="filterPeriod" class="filter-select">
                                    <option value="">Toutes les Dates</option>
                                    <option value="today">Aujourd'hui</option>
                                    <option value="week">Cette semaine</option>
                                    <option value="month">Ce mois</option>
                                    <option value="all">Tous</option>
                                </select>
                            </div>

                            <button id="clearFilters" class="filter-clear-btn">
                                <i class="bi bi-x-circle"></i>
                                Réinitialiser
                            </button>
                        </div>

                        <!-- Search Counter -->
                        <div class="search-counter">
                            <i class="bi bi-info-circle"></i>
                            <span id="resultCount"><?= $totalDons ?> don(s) trouvé(s)</span>
                        </div>

                        <!-- Table -->
                        <div class="table-container">
                            <table class="modern-table" id="donsTable">
                                <thead>
                                    <tr>
                                        <th class="sortable" onclick="sortTable(0)">
                                            ID
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th class="sortable" onclick="sortTable(1)">
                                            Donateur
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th class="sortable" onclick="sortTable(2)">
                                            Montant
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th class="sortable" onclick="sortTable(3)">
                                            Date
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th class="sortable" onclick="sortTable(4)">
                                            Type
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th class="sortable" onclick="sortTable(5)">
                                            Organisation
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php if (empty($donsData)): ?>
                                        <tr>
                                            <td colspan="7" class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <div>Aucun don trouvé</div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($donsData as $d):
                                            $nomComplet = '';
                                            if (!empty($d['prenom_donateur']) || !empty($d['nom_donateur'])) {
                                                $nomComplet = trim(($d['prenom_donateur'] ?? '') . ' ' . ($d['nom_donateur'] ?? ''));
                                            }
                                        ?>
                                        <tr class="data-row" data-id="<?= $d['id'] ?>" data-type="<?= htmlspecialchars($d['typeDon']) ?>" data-date="<?= $d['dateDon'] ?>" data-organisation="<?= htmlspecialchars($d['organisation_nom'] ?? 'N/A') ?>" data-donor="<?= htmlspecialchars($nomComplet ?: 'Anonyme') ?>" data-amount="<?= $d['montant'] ?>">
                                            <td data-label="ID"><strong>#<?= $d['id'] ?></strong></td>
                                            <td data-label="Donateur" class="donor-name">
                                                <?php if (!empty($nomComplet)): ?>
                                                    <i class="bi bi-person"></i><?= htmlspecialchars($nomComplet) ?>
                                                <?php else: ?>
                                                    <span class="anonymous"><i class="bi bi-eye-slash"></i>Anonyme</span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Montant" class="amount"><?= number_format($d['montant'], 2) ?> €</td>
                                            <td data-label="Date"><?= date('d/m/Y', strtotime($d['dateDon'])) ?></td>
                                            <td data-label="Type">
                                                <?php if ($d['typeDon'] === 'Monétaire'): ?>
                                                    <span class="badge badge-success"><?= htmlspecialchars($d['typeDon']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-primary"><?= htmlspecialchars($d['typeDon']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Organisation"><strong><?= htmlspecialchars($d['organisation_nom'] ?? 'N/A') ?></strong></td>
                                            <td data-label="Actions">
                                                <a href="deleteDon.php?id=<?= $d['id'] ?>" class="btn btn-danger"
                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ? Cette action est irréversible.')">
                                                   <i class="bi bi-trash"></i>Supprimer
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                       
                                        <!-- Ligne du total -->
                                        <tr class="total-row" id="totalRow">
                                            <td colspan="2"><strong>Total Général</strong></td>
                                            <td class="amount" id="totalAmount"><?= number_format($total, 2) ?> €</td>
                                            <td colspan="4"></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ========== THEME TOGGLE ==========
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

            const saved = localStorage.getItem('ma-admin-theme') || 'light';
            applyTheme(saved);

            if (toggle) {
                toggle.addEventListener('click', function () {
                    const next = body.classList.contains('dark') ? 'light' : 'dark';
                    applyTheme(next);
                });
            }
        })();

        // ========== ADVANCED FILTERING & SORTING ==========
        const filterSearch = document.getElementById('filterSearch');
        const filterType = document.getElementById('filterType');
        const filterPeriod = document.getElementById('filterPeriod');
        const headerSearch = document.getElementById('headerSearch');
        const clearBtn = document.getElementById('clearFilters');
        const resultCount = document.getElementById('resultCount');
        const tableBody = document.getElementById('tableBody');
        const totalRow = document.getElementById('totalRow');

        let currentSort = { column: null, direction: 'asc' };

        // Get all data rows
        function getDataRows() {
            return Array.from(document.querySelectorAll('.data-row'));
        }

        // Filter and display
        function applyFilters() {
            const searchTerm = filterSearch.value.toLowerCase();
            const typeFilter = filterType.value;
            const periodFilter = filterPeriod.value;

            const rows = getDataRows();
            let visibleCount = 0;
            let visibleTotal = 0;

            rows.forEach(row => {
                const donorText = row.getAttribute('data-donor').toLowerCase();
                const orgText = row.getAttribute('data-organisation').toLowerCase();
                const typeText = row.getAttribute('data-type');
                const dateText = row.getAttribute('data-date');
                const amountText = row.getAttribute('data-amount');

                // Search filter
                const matchesSearch =
                    donorText.includes(searchTerm) ||
                    orgText.includes(searchTerm) ||
                    amountText.includes(searchTerm);

                // Type filter
                const matchesType = !typeFilter || typeText === typeFilter;

                // Period filter
                let matchesPeriod = true;
                if (periodFilter) {
                    const rowDate = new Date(dateText);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (periodFilter === 'today') {
                        const rowDateOnly = new Date(rowDate);
                        rowDateOnly.setHours(0, 0, 0, 0);
                        matchesPeriod = rowDateOnly.getTime() === today.getTime();
                    } else if (periodFilter === 'week') {
                        const weekStart = new Date(today);
                        weekStart.setDate(today.getDate() - today.getDay());
                        matchesPeriod = rowDate >= weekStart && rowDate <= today;
                    } else if (periodFilter === 'month') {
                        matchesPeriod = rowDate.getMonth() === today.getMonth() && rowDate.getFullYear() === today.getFullYear();
                    }
                }

                if (matchesSearch && matchesType && matchesPeriod) {
                    row.style.display = '';
                    visibleCount++;
                    visibleTotal += parseFloat(row.getAttribute('data-amount'));
                } else {
                    row.style.display = 'none';
                }
            });

            // Update result count
            resultCount.textContent = `${visibleCount} don(s) trouvé(s)`;

            // Update total
            if (totalRow) {
                if (visibleCount === 0) {
                    totalRow.style.display = 'none';
                } else {
                    totalRow.style.display = '';
                    const totalCell = totalRow.querySelector('#totalAmount') || totalRow.cells[2];
                    totalCell.textContent = number_format(visibleTotal, 2) + ' €';
                }
            }
        }

        // Number formatting
        function number_format(number, decimals) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            const n = !isFinite(+number) ? 0 : +number;
            const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
            return (Math.round(n * Math.pow(10, prec)) / Math.pow(10, prec)).toFixed(prec);
        }

        // Sorting
        function sortTable(columnIndex) {
            const rows = getDataRows();
            const direction = currentSort.column === columnIndex && currentSort.direction === 'asc' ? 'desc' : 'asc';
            currentSort = { column: columnIndex, direction };

            rows.sort((a, b) => {
                let aVal, bVal;

                if (columnIndex === 0) { // ID
                    aVal = parseInt(a.getAttribute('data-id'));
                    bVal = parseInt(b.getAttribute('data-id'));
                } else if (columnIndex === 1) { // Donateur
                    aVal = a.getAttribute('data-donor').toLowerCase();
                    bVal = b.getAttribute('data-donor').toLowerCase();
                } else if (columnIndex === 2) { // Montant
                    aVal = parseFloat(a.getAttribute('data-amount'));
                    bVal = parseFloat(b.getAttribute('data-amount'));
                } else if (columnIndex === 3) { // Date
                    aVal = new Date(a.getAttribute('data-date'));
                    bVal = new Date(b.getAttribute('data-date'));
                } else if (columnIndex === 4) { // Type
                    aVal = a.getAttribute('data-type').toLowerCase();
                    bVal = b.getAttribute('data-type').toLowerCase();
                } else if (columnIndex === 5) { // Organisation
                    aVal = a.getAttribute('data-organisation').toLowerCase();
                    bVal = b.getAttribute('data-organisation').toLowerCase();
                }

                if (direction === 'asc') {
                    return aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
                } else {
                    return aVal < bVal ? 1 : aVal > bVal ? -1 : 0;
                }
            });

            // Re-insert rows
            rows.forEach(row => tableBody.insertBefore(row, totalRow));
        }

        // Event listeners
        filterSearch.addEventListener('input', applyFilters);
        filterType.addEventListener('change', applyFilters);
        filterPeriod.addEventListener('change', applyFilters);
        headerSearch.addEventListener('input', (e) => {
            filterSearch.value = e.target.value;
            applyFilters();
        });

        // Clear filters
        clearBtn.addEventListener('click', () => {
            filterSearch.value = '';
            filterType.value = '';
            filterPeriod.value = '';
            headerSearch.value = '';
            applyFilters();
        });
    </script>
</body>
</html>