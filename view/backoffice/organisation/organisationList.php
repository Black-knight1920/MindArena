<?php
require_once __DIR__."/../../../Controller/OrganisationController.php";
require_once __DIR__."/../../../Controller/DonController.php";
$orgCtrl = new OrganisationController();
$donCtrl = new DonController();
$organisations = $orgCtrl->listOrganisations();
$totalGeneral = 0;
foreach ($organisations as $org) {
    $totalGeneral += $org['montant_total'] ?? 0;
}
// Récupérer tous les dons pour compter par organisation
$tousLesDons = $donCtrl->listDon();
$donsParOrganisation = [];
// Organiser les dons par organisation
foreach ($tousLesDons as $don) {
    $orgId = $don['organisationId'];
    if (!isset($donsParOrganisation[$orgId])) {
        $donsParOrganisation[$orgId] = [];
    }
    $donsParOrganisation[$orgId][] = $don;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Organisations - Mind Arena</title>
   
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
   
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
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
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
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif;
            background: radial-gradient(circle at top left, rgba(139,92,246,0.16), transparent 55%),
                        radial-gradient(circle at bottom right, rgba(236,72,153,0.14), transparent 55%),
                        var(--bg);
            color: var(--text);
            transition: background .25s ease, color .25s ease;
        }
        a { text-decoration: none; color: inherit; }
        /* ================= LAYOUT ================= */
        .admin-shell { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: 230px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            padding: 18px 16px 18px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
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
        .sidebar-title { display: flex; flex-direction: column; }
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
        .sidebar-nav { margin-top: 10px; list-style: none; padding: 0; }
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
        .sidebar-link i { font-size: 17px; }
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
        .admin-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
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
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .header-left { display: flex; align-items: center; gap: 10px; font-size: 14px; }
        .header-left-title { font-weight: 600; }
        .header-left-sub { font-size: 12px; color: var(--text-muted); }
        .header-search {
            flex: 0 0 280px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            background: rgba(15,23,42,0.4);
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,0.35);
            transition: background-color 0.3s ease, border-color 0.3s ease;
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
        .header-search i { font-size: 16px; color: var(--text-muted); }
        .header-right { display: flex; align-items: center; gap: 16px; }
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
            border: none;
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
        .header-user { display: flex; align-items: center; gap: 10px; font-size: 13px; }
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
        .admin-content {
            flex: 1;
            padding: 24px 24px 26px;
            min-height: calc(100vh - 60px);
            background: var(--bg-soft);
            transition: background-color 0.3s ease;
        }
        .content-inner { max-width: 1320px; margin: 0 auto; }
        .card-shell {
            background: var(--card-bg);
            border-radius: 18px;
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow-soft);
            padding: 22px 22px 20px;
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
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
        .filter-label i { font-size: 1rem; }
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
        .filter-select:hover { border-color: var(--primary); }
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
        .table-container { margin-top: 20px; }
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
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
        }
        .stat-label { font-size: 0.875rem; color: var(--text-muted); }
        .modern-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
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
        .modern-table th.sortable:hover .sort-icon { opacity: 1; }
        .modern-table td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--border-subtle);
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }
        .modern-table tr:last-child td { border-bottom: none; }
        .modern-table tbody tr:hover { background: var(--primary-soft); }
        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .badge-info {
            background: rgba(59, 130, 246, 0.2);
            color: var(--info);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
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
        .btn-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .btn-success:hover {
            background: var(--success);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .btn-danger:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .btn-primary {
            background: var(--primary);
            color: white;
            border: 1px solid var(--primary);
        }
        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }
        .btn-info {
            background: rgba(59, 130, 246, 0.2);
            color: var(--info);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .btn-info:hover {
            background: var(--info);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .progress {
            height: 6px;
            background: var(--border-subtle);
            border-radius: 3px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(135deg, var(--success), #34d399);
            border-radius: 3px;
        }
        .amount {
            font-weight: 700;
            color: var(--success);
        }
        .amount-zero { color: var(--text-muted); }
        .description-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .website-link {
            color: var(--primary);
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .website-link:hover { color: var(--primary-hover); }
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
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .text-muted-small { font-size: 12px; color: var(--text-muted); }
        .mt-3 { margin-top: 1rem; }
        .me-1 { margin-right: 0.25rem; }
        .me-2 { margin-right: 0.5rem; }
        .d-flex { display: flex; }
        .align-items-center { align-items: center; }
        .gap-2 { gap: 0.5rem; }
        .flex-grow-1 { flex-grow: 1; }
        .hidden { display: none; }
        @media (max-width: 960px) {
            .admin-sidebar { display: none; }
            .admin-header { padding-inline: 14px; }
            .header-search { display: none; }
            .admin-content { padding-inline: 14px; }
            .modern-table { display: block; overflow-x: auto; }
            .action-buttons { flex-direction: column; }
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-group { min-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        <!-- Sidebar -->
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
                <a href="../don/donList.php" class="sidebar-link">
                    <i class="bi bi-currency-euro"></i>
                    <span>Liste des Dons</span>
                </a>
                <a href="organisationList.php" class="sidebar-link active">
                    <i class="bi bi-building"></i>
                    <span>Organisations</span>
                </a>
                <a href="addOrganisation.php" class="sidebar-link">
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

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <div>
                        <div class="header-left-title">Organisations</div>
                        <div class="header-left-sub">Gestion des associations partenaires</div>
                    </div>
                </div>
                <div class="header-search">
                    <i class="bi bi-search"></i>
                    <input type="text" id="headerSearch" placeholder="Rechercher une organisation...">
                </div>
                <div class="header-right">
                    <div class="theme-toggle-wrap">
                        <span>Mode</span>
                        <button class="theme-toggle" id="themeToggle">
                            <div class="theme-toggle-thumb">
                                <i class="bi bi-sun"></i>
                            </div>
                        </button>
                    </div>
                    <a href="addOrganisation.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i>
                        Nouvelle
                    </a>
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
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-value"><?= count($organisations) ?></div>
                                <div class="stat-label">Organisations</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?= number_format($totalGeneral, 2) ?> €</div>
                                <div class="stat-label">Total Collecté</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?= count($organisations) > 0 ? number_format($totalGeneral / count($organisations), 2) : '0.00' ?> €</div>
                                <div class="stat-label">Moyenne par Organisation</div>
                            </div>
                        </div>

                        <!-- Filter Bar -->
                        <div class="filter-bar">
                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="bi bi-search"></i>
                                    Recherche
                                </label>
                                <input type="text" id="filterSearch" class="filter-select" placeholder="Par nom, description ou site web...">
                            </div>

                            <div class="filter-group">
                                <label class="filter-label">
                                    <i class="bi bi-filter"></i>
                                    Montant
                                </label>
                                <select id="filterAmount" class="filter-select">
                                    <option value="">Tous les Montants</option>
                                    <option value="high">Élevé (> 1000€)</option>
                                    <option value="medium">Moyen (500€ - 1000€)</option>
                                    <option value="low">Faible (< 500€)</option>
                                    <option value="zero">Zéro don</option>
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
                            <span id="resultCount"><?= count($organisations) ?> organisation(s) trouvée(s)</span>
                        </div>

                        <!-- Table -->
                        <div class="table-container">
                            <table class="modern-table" id="orgsTable">
                                <thead>
                                    <tr>
                                        <th class="sortable" onclick="sortTable(0)">
                                            ID
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th class="sortable" onclick="sortTable(1)">
                                            Nom
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th class="sortable" onclick="sortTable(2)">
                                            Description
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th>Site Web</th>
                                        <th class="sortable" onclick="sortTable(4)">
                                            Montant Total
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th class="sortable" onclick="sortTable(5)">
                                            Pourcentage
                                            <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        </th>
                                        <th>Dons</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php if (empty($organisations)): ?>
                                        <tr>
                                            <td colspan="8" class="empty-state">
                                                <i class="bi bi-building"></i>
                                                <div>Aucune organisation trouvée</div>
                                                <a href="addOrganisation.php" class="btn btn-primary mt-3">
                                                    <i class="bi bi-plus-circle me-2"></i>Créer la première
                                                </a>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($organisations as $org):
                                            $montant = $org['montant_total'] ?? 0;
                                            $pourcentage = $totalGeneral > 0 ? ($montant / $totalGeneral) * 100 : 0;
                                            $nombreDons = 0;
                                            if (isset($donsParOrganisation[$org['id']])) {
                                                $nombreDons = count($donsParOrganisation[$org['id']]);
                                            }
                                        ?>
                                        <tr class="data-row" data-id="<?= $org['id'] ?>" data-name="<?= htmlspecialchars($org['nom']) ?>" data-description="<?= htmlspecialchars($org['description']) ?>" data-website="<?= htmlspecialchars($org['website_url'] ?? '') ?>" data-amount="<?= $montant ?>" data-percentage="<?= $pourcentage ?>">
                                            <td><strong>#<?= $org['id'] ?></strong></td>
                                            <td>
                                                <strong>
                                                    <i class="bi bi-building me-1"></i>
                                                    <?= htmlspecialchars($org['nom']) ?>
                                                </strong>
                                            </td>
                                            <td class="description-cell" title="<?= htmlspecialchars($org['description']) ?>">
                                                <?= htmlspecialchars(substr($org['description'], 0, 80)) ?>...
                                            </td>
                                            <td>
                                                <?php if (!empty($org['website_url'])): ?>
                                                    <a href="<?= htmlspecialchars($org['website_url']) ?>"
                                                       target="_blank"
                                                       class="website-link"
                                                       title="Visiter le site web">
                                                        <i class="bi bi-globe me-1"></i>Visiter
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="amount <?= $montant == 0 ? 'amount-zero' : '' ?>">
                                                <?= number_format($montant, 2) ?> €
                                            </td>
                                            <td>
                                                <?php if ($totalGeneral > 0): ?>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress flex-grow-1">
                                                            <div class="progress-bar" style="width: <?= min($pourcentage, 100) ?>%"></div>
                                                        </div>
                                                        <small class="text-muted"><?= number_format($pourcentage, 1) ?>%</small>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">0%</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="organisationDons.php?id=<?= $org['id'] ?>" class="btn btn-info">
                                                    <i class="bi bi-eye me-1"></i>
                                                    <?= $nombreDons ?> don(s)
                                                </a>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="modifyOrganisation.php?id=<?= $org['id'] ?>" class="btn btn-success">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="deleteOrganisation.php?id=<?= $org['id'] ?>" class="btn btn-danger"
                                                       onclick="return confirm('Êtes-vous sûr de supprimer <?= htmlspecialchars($org['nom']) ?> ?')">
                                                       <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
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
                    body.classList.add('light');
                    thumb.innerHTML = '<i class="bi bi-sun"></i>';
                } else {
                    body.classList.remove('light');
                    thumb.innerHTML = '<i class="bi bi-moon"></i>';
                }
                localStorage.setItem('ma-admin-theme', theme);
            }

            const saved = localStorage.getItem('ma-admin-theme') || 'dark';
            applyTheme(saved);

            if (toggle) {
                toggle.addEventListener('click', function () {
                    const isLight = body.classList.contains('light');
                    const next = isLight ? 'dark' : 'light';
                    applyTheme(next);
                });
            }
        })();

        // ========== ADVANCED FILTERING & SORTING ==========
        const filterSearch = document.getElementById('filterSearch');
        const filterAmount = document.getElementById('filterAmount');
        const headerSearch = document.getElementById('headerSearch');
        const clearBtn = document.getElementById('clearFilters');
        const resultCount = document.getElementById('resultCount');
        const tableBody = document.getElementById('tableBody');

        let currentSort = { column: null, direction: 'asc' };

        function getDataRows() {
            return Array.from(document.querySelectorAll('.data-row'));
        }

        function applyFilters() {
            const searchTerm = filterSearch.value.toLowerCase();
            const amountFilter = filterAmount.value;

            const rows = getDataRows();
            let visibleCount = 0;

            rows.forEach(row => {
                const nameText = row.getAttribute('data-name').toLowerCase();
                const descText = row.getAttribute('data-description').toLowerCase();
                const websiteText = row.getAttribute('data-website').toLowerCase();
                const amountVal = parseFloat(row.getAttribute('data-amount'));

                const matchesSearch =
                    nameText.includes(searchTerm) ||
                    descText.includes(searchTerm) ||
                    websiteText.includes(searchTerm);

                let matchesAmount = true;
                if (amountFilter) {
                    if (amountFilter === 'high') {
                        matchesAmount = amountVal > 1000;
                    } else if (amountFilter === 'medium') {
                        matchesAmount = amountVal >= 500 && amountVal <= 1000;
                    } else if (amountFilter === 'low') {
                        matchesAmount = amountVal > 0 && amountVal < 500;
                    } else if (amountFilter === 'zero') {
                        matchesAmount = amountVal === 0;
                    }
                }

                if (matchesSearch && matchesAmount) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            resultCount.textContent = `${visibleCount} organisation(s) trouvée(s)`;
        }

        function sortTable(columnIndex) {
            const rows = getDataRows();
            const direction = currentSort.column === columnIndex && currentSort.direction === 'asc' ? 'desc' : 'asc';
            currentSort = { column: columnIndex, direction };

            rows.sort((a, b) => {
                let aVal, bVal;

                if (columnIndex === 0) {
                    aVal = parseInt(a.getAttribute('data-id'));
                    bVal = parseInt(b.getAttribute('data-id'));
                } else if (columnIndex === 1) {
                    aVal = a.getAttribute('data-name').toLowerCase();
                    bVal = b.getAttribute('data-name').toLowerCase();
                } else if (columnIndex === 2) {
                    aVal = a.getAttribute('data-description').toLowerCase();
                    bVal = b.getAttribute('data-description').toLowerCase();
                } else if (columnIndex === 4) {
                    aVal = parseFloat(a.getAttribute('data-amount'));
                    bVal = parseFloat(b.getAttribute('data-amount'));
                } else if (columnIndex === 5) {
                    aVal = parseFloat(a.getAttribute('data-percentage'));
                    bVal = parseFloat(b.getAttribute('data-percentage'));
                }

                if (direction === 'asc') {
                    return aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
                } else {
                    return aVal < bVal ? 1 : aVal > bVal ? -1 : 0;
                }
            });

            rows.forEach(row => tableBody.appendChild(row));
        }

        filterSearch.addEventListener('input', applyFilters);
        filterAmount.addEventListener('change', applyFilters);
        headerSearch.addEventListener('input', (e) => {
            filterSearch.value = e.target.value;
            applyFilters();
        });

        clearBtn.addEventListener('click', () => {
            filterSearch.value = '';
            filterAmount.value = '';
            headerSearch.value = '';
            applyFilters();
        });
    </script>
</body>
</html>