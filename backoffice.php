<?php
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

    // Gestion des notifications non lues (basé sur le dernier don vu)
    $latestDonationId = !empty($dons) ? intval($dons[0]['id'] ?? 0) : 0; // $dons est déjà trié par id DESC
    $lastSeenDonationId = isset($_COOKIE['last_seen_donation']) ? intval($_COOKIE['last_seen_donation']) : 0;
    $unreadNotifications = 0;

    if ($latestDonationId > 0) {
        foreach ($dons as $don) {
            $donId = intval($don['id'] ?? 0);
            if ($donId > $lastSeenDonationId) {
                $unreadNotifications++;
            } else {
                // Les dons sont ordonnés par date décroissante, on peut s'arrêter dès que l'on rencontre un don déjà vu
                break;
            }
        }
    }

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
    $latestDonationId = 0;
    $lastSeenDonationId = 0;
    $unreadNotifications = 0;
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

        /* ================= NOTIFICATIONS STYLES ================= */
        .notifications-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .empty-notification {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .notification-row {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px;
            border-radius: 12px;
            background: var(--bg-soft);
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }

        .notification-row:hover {
            background: var(--card-bg);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateX(4px);
        }

        .notification-row.monetary {
            border-left-color: #10b981;
        }

        .notification-row.material {
            border-left-color: #f59e0b;
        }

        .notification-icon {
            font-size: 24px;
            min-width: 24px;
            text-align: center;
        }

        .notification-info {
            flex: 1;
            min-width: 0;
        }

        .notification-donor {
            font-size: 0.95rem;
            color: var(--text);
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .notification-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .org-name {
            color: var(--primary);
            font-weight: 600;
        }

        .donation-type-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .donation-type-badge.monetary {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }

        .donation-type-badge.material {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .time {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        /* ================= NOTIFICATION BADGE STYLES ================= */
        .notification-bell {
            position: relative;
            cursor: pointer;
            font-size: 20px;
            color: var(--text-muted);
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
        }

        .notification-bell:hover {
            color: var(--primary);
        }

        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 20px;
            height: 20px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
            }
            50% {
                box-shadow: 0 4px 20px rgba(239, 68, 68, 0.8);
            }
        }

        .notification-badge.empty {
            display: none;
        }

        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            top: 60px;
            right: 0;
            width: 400px;
            max-height: 500px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            z-index: 1000;
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-dropdown.active {
            display: flex;
        }

        .notification-dropdown-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-dropdown-header h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
        }

        .notification-dropdown-header .close-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--text-muted);
            transition: color 0.3s ease;
        }

        .notification-dropdown-header .close-btn:hover {
            color: var(--text);
        }

        .notification-dropdown-list {
            flex: 1;
            overflow-y: auto;
            max-height: 400px;
        }

        .notification-dropdown-list::-webkit-scrollbar {
            width: 6px;
        }

        .notification-dropdown-list::-webkit-scrollbar-track {
            background: var(--bg-soft);
        }

        .notification-dropdown-list::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        .notification-dropdown-item {
            padding: 16px 20px;
            border-bottom: 1px solid var(--bg-soft);
            display: flex;
            gap: 12px;
            transition: background 0.3s ease;
            cursor: pointer;
        }

        .notification-dropdown-item:hover {
            background: var(--bg-soft);
        }

        .notification-dropdown-item:last-child {
            border-bottom: none;
        }

        .notification-item-icon {
            font-size: 24px;
            min-width: 24px;
            text-align: center;
        }

        .notification-item-content {
            flex: 1;
            min-width: 0;
        }

        .notification-item-donor {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
        }

        .notification-item-details {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .notification-dropdown-footer {
            padding: 12px 20px;
            border-top: 1px solid var(--card-border);
            text-align: center;
        }

        .notification-dropdown-footer a {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .notification-dropdown-footer a:hover {
            color: var(--primary-hover);
        }

        /* ================= MODAL STYLES ================= */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow-soft);
            max-width: 600px;
            width: 90%;
            max-height: 85vh;
            overflow: hidden;
            position: relative;
            animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), #a855f7, var(--primary));
            background-size: 200% 100%;
            animation: shimmer 3s infinite linear;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            padding: 28px 35px;
            background: linear-gradient(135deg, rgba(139,92,246,0.1), rgba(168,85,247,0.05));
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .modal-header h3 i {
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .modal-close {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--primary-soft);
            border: 1px solid var(--primary);
            color: var(--primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal-close:hover {
            background: var(--primary);
            color: white;
            transform: rotate(90deg) scale(1.1);
            box-shadow: 0 0 20px rgba(139,92,246,0.5);
        }

        .modal-body {
            padding: 35px;
            max-height: calc(85vh - 100px);
            overflow-y: auto;
        }

        .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: var(--bg-soft);
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        .detail-group {
            margin-bottom: 24px;
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .detail-group:nth-child(1) { animation-delay: 0.1s; }
        .detail-group:nth-child(2) { animation-delay: 0.15s; }
        .detail-group:nth-child(3) { animation-delay: 0.2s; }
        .detail-group:nth-child(4) { animation-delay: 0.25s; }
        .detail-group:nth-child(5) { animation-delay: 0.3s; }
        .detail-group:nth-child(6) { animation-delay: 0.35s; }
        .detail-group:nth-child(7) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .detail-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-label i {
            font-size: 1rem;
            opacity: 0.8;
        }

        .detail-value {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text);
            padding: 16px 20px;
            background: linear-gradient(135deg, rgba(139,92,246,0.15), rgba(168,85,247,0.1));
            border-radius: 12px;
            border: 1px solid var(--border-subtle);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .detail-value::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--primary), #a855f7);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .detail-value:hover {
            border-color: var(--primary);
            transform: translateX(3px);
        }

        .detail-value:hover::before {
            opacity: 1;
        }

        .detail-value.highlight {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05));
            border: 2px solid var(--success);
            color: var(--success);
            text-align: center;
            box-shadow: 0 0 20px rgba(16,185,129,0.2);
            animation: pulseAmount 2s infinite;
        }

        .detail-value.highlight::before {
            width: 100%;
            background: linear-gradient(90deg, transparent, rgba(16,185,129,0.3), transparent);
        }

        @keyframes pulseAmount {
            0%, 100% {
                box-shadow: 0 0 20px rgba(16,185,129,0.2);
            }
            50% {
                box-shadow: 0 0 30px rgba(16,185,129,0.4);
            }
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
                    <!-- Notification Bell -->
                    <div style="position: relative;">
                        <div class="notification-bell" id="notificationBell" data-latest-id="<?php echo $latestDonationId; ?>">
                            <i class="bi bi-bell"></i>
                            <span class="notification-badge<?php echo ($unreadNotifications <= 0 ? ' empty' : ''); ?>" id="notificationBadge"><?php echo $unreadNotifications; ?></span>
                        </div>

                        <!-- Notification Dropdown -->
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-dropdown-header">
                                <h3>📬 Notifications</h3>
                                <button class="close-btn" id="closeNotificationBtn">×</button>
                            </div>

                            <div class="notification-dropdown-list" id="notificationList">
                                <?php
                                if (empty($dons)) {
                                    echo '<div style="padding: 40px 20px; text-align: center; color: var(--text-muted);">Aucune notification</div>';
                                } else {
                                    $recentDons = array_slice($dons, 0, 10); // Afficher les 10 dernières notifications
                                    foreach ($recentDons as $don) {
                                        $nomDonateur = trim(($don['prenom_donateur'] ?? '') . ' ' . ($don['nom_donateur'] ?? ''));
                                        $nomDonateur = empty($nomDonateur) ? 'Donateur Anonyme' : $nomDonateur;
                                        $montant = number_format($don['montant'], 2, ',', ' ');
                                        $typeDon = $don['typeDon'];
                                        $organisationNom = htmlspecialchars($don['organisation_nom'] ?? 'Organisation');
                                        $icon = $typeDon === 'Monétaire' ? '💵' : '📦';
                                ?>
                                    <div class="notification-dropdown-item" 
                                         data-don-id="<?php echo htmlspecialchars($don['id']); ?>"
                                         data-donor="<?php echo htmlspecialchars($nomDonateur); ?>"
                                         data-email="<?php echo htmlspecialchars($don['email_donateur'] ?? ''); ?>"
                                         data-amount="<?php echo htmlspecialchars($montant); ?>"
                                         data-date="<?php echo htmlspecialchars($don['dateDon']); ?>"
                                         data-type="<?php echo htmlspecialchars($typeDon); ?>"
                                         data-org="<?php echo htmlspecialchars($organisationNom); ?>"
                                         style="cursor: pointer;">
                                        <div class="notification-item-icon"><?php echo $icon; ?></div>
                                        <div class="notification-item-content">
                                            <div class="notification-item-donor">
                                                <?php echo htmlspecialchars($nomDonateur); ?>
                                            </div>
                                            <div class="notification-item-details">
                                                <strong><?php echo $montant; ?> €</strong> pour <strong><?php echo $organisationNom; ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>

                            <div class="notification-dropdown-footer">
                                <a href="View/backoffice/don/notifications.php">
                                    Voir toutes les notifications →
                                </a>
                            </div>
                        </div>
                    </div>

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
                    </div>

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

        // Notification Bell Toggle
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const closeNotificationBtn = document.getElementById('closeNotificationBtn');
        const notificationBadge = document.getElementById('notificationBadge');
        const latestDonationId = notificationBell ? parseInt(notificationBell.dataset.latestId || '0', 10) : 0;

        function setBadge(count) {
            if (!notificationBadge) return;
            const safeCount = Math.max(0, parseInt(count, 10) || 0);
            notificationBadge.textContent = safeCount;
            if (safeCount <= 0) {
                notificationBadge.classList.add('empty');
            } else {
                notificationBadge.classList.remove('empty');
            }
        }

        // Afficher/Masquer le dropdown
        if (notificationBell) {
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('active');

                // Lorsque l'utilisateur ouvre le panneau, on marque comme vu
                if (notificationDropdown.classList.contains('active')) {
                    setBadge(0);
                    if (latestDonationId > 0) {
                        document.cookie = 'last_seen_donation=' + latestDonationId + ';path=/';
                    }
                }
            });
        }

        // Fermer le dropdown avec le bouton X
        if (closeNotificationBtn) {
            closeNotificationBtn.addEventListener('click', function() {
                notificationDropdown.classList.remove('active');
            });
        }

        // Fermer le dropdown en cliquant ailleurs
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.contains(e.target) && !notificationBell.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });

        // Masquer le badge si aucune notification
        setBadge(notificationBadge ? notificationBadge.textContent : 0);

        // Auto-refresh des notifications (rafraîchit la page pour récupérer les nouveaux dons)
        setInterval(function() {
            location.reload();
        }, 15000);

        // ========== MODAL NOTIFICATION DETAILS ==========
        // Add click event to notification items
        document.querySelectorAll('.notification-dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const id = this.getAttribute('data-don-id');
                const donor = this.getAttribute('data-donor');
                const email = this.getAttribute('data-email');
                const amount = this.getAttribute('data-amount');
                const date = this.getAttribute('data-date');
                const type = this.getAttribute('data-type');
                const org = this.getAttribute('data-org');
                
                showDonDetails(id, donor, amount, date, type, org, email);
                notificationDropdown.classList.remove('active');
            });
        });

        function showDonDetails(id, donor, amount, date, type, organisation, email) {
            document.getElementById('modalDonId').textContent = '#' + id;
            document.getElementById('modalDonor').textContent = donor;
            document.getElementById('modalEmail').textContent = email || 'Non renseigné';
            document.getElementById('modalAmount').textContent = amount + ' €';
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalType').textContent = type;
            document.getElementById('modalOrganisation').textContent = organisation;
            document.getElementById('donModal').classList.add('show');
        }

        function closeDonModal() {
            document.getElementById('donModal').classList.remove('show');
        }

        // Close modal on overlay click
        document.getElementById('donModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDonModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDonModal();
            }
        });
    </script>

    <!-- Donation Detail Modal -->
    <div class="modal-overlay" id="donModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-info-circle"></i> Détails du Don</h3>
                <button class="modal-close" onclick="closeDonModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="detail-group">
                    <div class="detail-label"><i class="bi bi-hash"></i> Identifiant</div>
                    <div class="detail-value" id="modalDonId"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label"><i class="bi bi-person"></i> Donateur</div>
                    <div class="detail-value" id="modalDonor"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label"><i class="bi bi-envelope"></i> Email</div>
                    <div class="detail-value" id="modalEmail"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label"><i class="bi bi-currency-euro"></i> Montant</div>
                    <div class="detail-value highlight" id="modalAmount"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label"><i class="bi bi-calendar-event"></i> Date du Don</div>
                    <div class="detail-value" id="modalDate"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label"><i class="bi bi-tag"></i> Type de Don</div>
                    <div class="detail-value" id="modalType"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label"><i class="bi bi-building"></i> Organisation</div>
                    <div class="detail-value" id="modalOrganisation"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>