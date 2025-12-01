<?php
require_once __DIR__."/../../../Controller/DonController.php";
require_once __DIR__."/../../../Controller/OrganisationController.php";

$donCtrl = new DonController();
$orgCtrl = new OrganisationController();

$organisationId = $_GET['id'] ?? 0;

// Récupérer les informations de l'organisation
$organisation = $orgCtrl->getOrganisation($organisationId);

if (!$organisation) {
    header("Location: organisationList.php");
    exit;
}

// Récupérer les dons de cette organisation spécifique
$sql = "SELECT d.* 
        FROM don d 
        WHERE d.organisationId = :organisationId 
        ORDER BY d.dateDon DESC, d.id DESC";

$db = config::getConnexion();
$q = $db->prepare($sql);
$q->execute([':organisationId' => $organisationId]);
$dons = $q->fetchAll();

// Calculer le total des dons pour cette organisation
$totalOrganisation = 0;
foreach ($dons as $don) {
    $totalOrganisation += $don['montant'];
}

// NOUVEAU : Définir les objectifs comme dans index.php du frontoffice
$objectifsParOrganisation = [
    1 => 10000, // ID 1 : 10 000€
    2 => 5000,  // ID 2 : 5 000€
    3 => 15000, // ID 3 : 15 000€
    4 => 3000,  // ID 4 : 3 000€
    5 => 8000,  // ID 5 : 8 000€
    6 => 12000, // ID 6 : 12 000€
    7 => 6000,  // ID 7 : 6 000€
    8 => 20000, // ID 8 : 20 000€
    9 => 4000,  // ID 9 : 4 000€
    10 => 7000  // ID 10 : 7 000€
];

// Utiliser l'objectif spécifique à l'organisation ou un défaut
$objectif = $objectifsParOrganisation[$organisationId] ?? 5000; // 5000€ par défaut si non défini
$pourcentage = $objectif > 0 ? min(100, ($totalOrganisation / $objectif) * 100) : 0;
$montantRestant = max(0, $objectif - $totalOrganisation);

// NOUVEAU : Chemin de l'image de l'organisation
$imagePath = "../frontoffice/images/organisations/organisation_" . $organisationId . ".jpg";
$defaultImagePath = "../frontoffice/images/organisations/default_org.jpg";
$imageExists = file_exists($imagePath);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dons de <?= htmlspecialchars($organisation['nom']) ?> - Mind Arena</title>
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Remix Icons pour le dark mode -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
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
            background: var(--bg-soft);
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

        /* ================= STYLES SPÉCIFIQUES ================= */
        .org-header {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .org-main-info {
            display: flex;
            gap: 25px;
            align-items: flex-start;
        }

        @media (max-width: 768px) {
            .org-main-info {
                flex-direction: column;
            }
        }

        .org-image-container {
            flex-shrink: 0;
            width: 250px;
            height: 180px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .org-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .org-image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .org-details {
            flex: 1;
        }

        .org-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .org-subtitle {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .website-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .website-link:hover {
            color: var(--primary-hover);
        }

        /* NOUVEAU : Section de progression améliorée avec mêmes objectifs */
        .progress-section {
            background: rgba(139, 92, 246, 0.1);
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
            border: 1px solid rgba(139, 92, 246, 0.2);
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .progress-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-percentage {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #10b981, #4cff4c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .progress-bar-container {
            width: 100%;
            height: 16px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin: 15px 0;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #4cff4c, #10b981);
            border-radius: 8px;
            transition: width 1s ease-out;
        }

        .progress-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.95rem;
            margin-top: 20px;
        }

        .progress-current {
            color: #4cff4c;
            font-weight: bold;
            font-size: 1.3rem;
        }

        .progress-goal {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .progress-remaining {
            color: var(--warning);
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Niveau de progression identique au frontoffice */
        .progress-level {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.1);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.95rem;
            color: var(--success);
            font-weight: 600;
            margin-top: 15px;
        }

        .progress-level i {
            font-size: 1.1rem;
        }

        /* Statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--card-border);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin: 0 auto 15px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tableau des dons */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            margin-top: 20px;
        }

        .modern-table th {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            color: white;
            padding: 18px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modern-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border-subtle);
            font-size: 0.95rem;
            vertical-align: middle;
        }

        .modern-table tr:last-child td {
            border-bottom: none;
        }

        .modern-table tr:hover {
            background: var(--primary-soft);
        }

        /* Badge styles */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-primary {
            background: rgba(139, 92, 246, 0.2);
            color: var(--primary);
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        /* Button styles */
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-subtle);
        }

        .btn-outline:hover {
            background: var(--primary-soft);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: 1px solid var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        /* Amount styling */
        .amount {
            font-weight: 700;
            color: var(--success);
            font-size: 1.1rem;
        }

        .donor-name {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .anonymous {
            color: var(--text-muted);
            font-style: italic;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3.5rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .total-row {
            background: var(--primary-soft) !important;
            font-weight: 700;
        }

        .total-row td {
            border-top: 2px solid var(--primary);
            border-bottom: none !important;
            padding: 20px 16px;
        }

        .total-amount {
            font-size: 1.3rem;
            color: var(--success);
        }

        /* Actions rapides */
        .quick-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        /* Simple utilities */
        .text-muted-small {
            font-size: 12px;
            color: var(--text-muted);
        }

        .me-1 { margin-right: 0.25rem; }
        .me-2 { margin-right: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }

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
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .quick-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .progress-stats {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
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

        <!-- ======= Main Content ======= -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <div>
                        <div class="header-left-title">Dons de l'organisation</div>
                        <div class="header-left-sub">Détail des donations reçues</div>
                    </div>
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
                    <a href="organisationList.php" class="btn btn-outline">
                        <i class="bi bi-arrow-left"></i>
                        Retour à la liste
                    </a>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                <div class="content-inner">
                    <div class="card-shell">
                        <!-- NOUVEAU : En-tête avec image et informations -->
                        <div class="org-header">
                            <div class="org-main-info">
                                <!-- Image de l'organisation -->
                                <div class="org-image-container">
                                    <?php if ($imageExists): ?>
                                        <img src="<?= $imagePath ?>" 
                                             alt="<?= htmlspecialchars($organisation['nom']) ?>" 
                                             class="org-image">
                                    <?php else: ?>
                                        <div class="org-image-placeholder">
                                            <?= strtoupper(substr($organisation['nom'], 0, 2)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="org-details">
                                    <h1 class="org-title"><?= htmlspecialchars($organisation['nom']) ?></h1>
                                    <p class="org-subtitle"><?= htmlspecialchars($organisation['description']) ?></p>
                                    
                                    <?php if (!empty($organisation['website_url'])): ?>
                                        <div class="mb-3">
                                            <a href="<?= htmlspecialchars($organisation['website_url']) ?>" 
                                               target="_blank" 
                                               class="website-link">
                                                <i class="bi bi-globe"></i>
                                                <?= htmlspecialchars($organisation['website_url']) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Actions rapides -->
                                    <div class="quick-actions">
                                        <a href="../frontoffice/addDon.php?orgId=<?= $organisation['id'] ?>" 
                                           target="_blank" 
                                           class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i>
                                            Nouveau don
                                        </a>
                                        <a href="../frontoffice/index.php#organisations" 
                                           target="_blank" 
                                           class="btn btn-outline">
                                            <i class="bi bi-eye"></i>
                                            Voir sur le site
                                        </a>
                                        <a href="modifyOrganisation.php?id=<?= $organisation['id'] ?>" 
                                           class="btn btn-outline">
                                            <i class="bi bi-pencil"></i>
                                            Modifier
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- NOUVEAU : Section de progression avec mêmes objectifs que le frontoffice -->
                            <div class="progress-section">
                                <div class="progress-header">
                                    <div class="progress-title">
                                        <i class="bi bi-bullseye"></i>
                                        Objectif de collecte
                                    </div>
                                    <div class="progress-percentage"><?= number_format($pourcentage, 1) ?>%</div>
                                </div>
                                
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: <?= $pourcentage ?>%"></div>
                                </div>
                                
                                <div class="progress-stats">
                                    <div>
                                        <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 5px;">
                                            Collecté
                                        </div>
                                        <div class="progress-current"><?= number_format($totalOrganisation, 2) ?> €</div>
                                    </div>
                                    
                                    <div style="text-align: center;">
                                        <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 5px;">
                                            Restant
                                        </div>
                                        <div class="progress-remaining">
                                            <?= number_format($montantRestant, 2) ?> €
                                        </div>
                                    </div>
                                    
                                    <div style="text-align: right;">
                                        <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 5px;">
                                            Objectif
                                        </div>
                                        <div class="progress-goal"><?= number_format($objectif, 2) ?> €</div>
                                    </div>
                                </div>
                                
                                <!-- Niveau de progression identique au frontoffice -->
                                <div style="margin-top: 15px; text-align: center;">
                                    <span class="progress-level">
                                        <?php if ($pourcentage >= 100): ?>
                                            <i class="bi bi-trophy-fill" style="color: gold;"></i>
                                            Objectif atteint !
                                        <?php elseif ($pourcentage >= 75): ?>
                                            <i class="bi bi-star-fill" style="color: gold;"></i>
                                            Niveau Expert
                                        <?php elseif ($pourcentage >= 50): ?>
                                            <i class="bi bi-star-half" style="color: gold;"></i>
                                            Niveau Avancé
                                        <?php elseif ($pourcentage >= 25): ?>
                                            <i class="bi bi-arrow-up-right" style="color: var(--warning);"></i>
                                            Niveau Intermédiaire
                                        <?php else: ?>
                                            <i class="bi bi-arrow-clockwise" style="color: var(--text-muted);"></i>
                                            Niveau Débutant
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques détaillées -->
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                                <div class="stat-value"><?= number_format($totalOrganisation, 2) ?> €</div>
                                <div class="stat-label">Total collecté</div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <div class="stat-value"><?= count($dons) ?></div>
                                <div class="stat-label">Nombre de dons</div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                                <div class="stat-value">
                                    <?= count($dons) > 0 ? number_format($totalOrganisation / count($dons), 2) : '0.00' ?> €
                                </div>
                                <div class="stat-label">Moyenne par don</div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="bi bi-percent"></i>
                                </div>
                                <div class="stat-value"><?= number_format($pourcentage, 1) ?>%</div>
                                <div class="stat-label">Progression</div>
                            </div>
                        </div>

                        <!-- Tableau des dons -->
                        <h3 style="color: var(--text); margin: 30px 0 20px 0; font-size: 1.5rem; font-weight: 700;">
                            <i class="bi bi-list-check me-2"></i>Historique des dons
                        </h3>
                        
                        <div class="table-container">
                            <?php if (empty($dons)): ?>
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <div>Aucun don trouvé pour cette organisation</div>
                                    <p class="text-muted mt-2">
                                        Les dons apparaîtront ici lorsqu'ils seront enregistrés.
                                        <br>
                                        <a href="../frontoffice/addDon.php?orgId=<?= $organisation['id'] ?>" 
                                           target="_blank" 
                                           class="btn btn-primary mt-3">
                                            <i class="bi bi-plus-circle"></i> Faire le premier don
                                        </a>
                                    </p>
                                </div>
                            <?php else: ?>
                                <table class="modern-table">
                                    <thead>
                                        <tr>
                                            <th width="80">ID</th>
                                            <th>Donateur</th>
                                            <th width="150">Montant</th>
                                            <th width="120">Date</th>
                                            <th width="120">Type</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dons as $don): 
                                            $nomComplet = '';
                                            if (!empty($don['prenom_donateur']) || !empty($don['nom_donateur'])) {
                                                $nomComplet = trim(($don['prenom_donateur'] ?? '') . ' ' . ($don['nom_donateur'] ?? ''));
                                            }
                                        ?>
                                        <tr>
                                            <td><strong>#<?= $don['id'] ?></strong></td>
                                            <td class="donor-name">
                                                <?php if (!empty($nomComplet)): ?>
                                                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($nomComplet) ?>
                                                <?php else: ?>
                                                    <span class="anonymous"><i class="bi bi-eye-slash me-1"></i>Anonyme</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="amount"><?= number_format($don['montant'], 2) ?> €</td>
                                            <td><?= date('d/m/Y', strtotime($don['dateDon'])) ?></td>
                                            <td>
                                                <?php if ($don['typeDon'] === 'Monétaire'): ?>
                                                    <span class="badge badge-success"><?= htmlspecialchars($don['typeDon']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-primary"><?= htmlspecialchars($don['typeDon']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="../don/deleteDon.php?id=<?= $don['id'] ?>" 
                                                   class="btn btn-danger" 
                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ? Cette action est irréversible.')">
                                                   <i class="bi bi-trash"></i>Supprimer
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        
                                        <!-- Ligne du total -->
                                        <tr class="total-row">
                                            <td colspan="2">
                                                <strong>Total pour <?= htmlspecialchars($organisation['nom']) ?></strong>
                                            </td>
                                            <td class="total-amount"><?= number_format($totalOrganisation, 2) ?> €</td>
                                            <td colspan="3"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <div style="text-align: center; margin-top: 20px; color: var(--text-muted); font-size: 0.9rem;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <?= count($dons) ?> don(s) au total pour cette organisation
                                </div>
                            <?php endif; ?>
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

        // Animation de la barre de progression
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.progress-bar-fill');
            if (progressBar) {
                const currentWidth = progressBar.style.width;
                progressBar.style.width = '0';
                
                setTimeout(() => {
                    progressBar.style.transition = 'width 1.5s cubic-bezier(0.4, 0, 0.2, 1)';
                    progressBar.style.width = currentWidth;
                }, 300);
            }
        });
    </script>
</body>
</html>