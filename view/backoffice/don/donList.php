<?php
require_once __DIR__."/../../../Controller/DonController.php";

$donCtrl = new DonController();
$donsResult = $donCtrl->listDon();
// Convert to array if it's a PDOStatement
$dons = is_array($donsResult) ? $donsResult : $donsResult->fetchAll(PDO::FETCH_ASSOC);
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
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        /* ================= PAGINATION STYLES ================= */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
            padding: 20px;
            flex-wrap: wrap;
        }

        .pagination-info {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-right: 20px;
        }

        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid var(--border-subtle);
            background: var(--bg-soft);
            color: var(--text);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .pagination-btn:hover:not(:disabled) {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139,92,246,0.3);
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(139,92,246,0.3);
        }

        .items-per-page {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .items-per-page select {
            padding: 8px 12px;
            border: 1px solid var(--border-subtle);
            background: var(--bg-soft);
            color: var(--text);
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
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

        .modern-table thead {
            background: linear-gradient(135deg, rgba(139,92,246,0.2), rgba(168,85,247,0.15));
            border: 1px solid var(--primary);
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
            transition: border-color 0.3s ease;
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

        .btn-info {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .btn-info:hover {
            background: #3b82f6;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
        }

        /* Modal styles */
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
            border-radius: 24px;
            border: 1px solid var(--primary);
            box-shadow: 
                0 0 0 1px rgba(139,92,246,0.2),
                0 30px 60px rgba(0,0,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.1);
            max-width: 650px;
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
            animation: pulse 2s infinite;
        }

        .detail-value.highlight::before {
            width: 100%;
            background: linear-gradient(90deg, transparent, rgba(16,185,129,0.3), transparent);
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 20px rgba(16,185,129,0.2);
            }
            50% {
                box-shadow: 0 0 30px rgba(16,185,129,0.4);
            }
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
                <a href="../../../backoffice.php" class="sidebar-link">
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
            </nav>

            <div class="sidebar-footer">
                <span class="text-muted-small">© 2024 Mind Arena</span>
                <a href="../../frontoffice/index.php">
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
                        $donsData = $dons;
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
                                                <button onclick="showDonDetails(<?= $d['id'] ?>, '<?= htmlspecialchars(addslashes($nomComplet ?: 'Anonyme'), ENT_QUOTES) ?>', '<?= number_format($d['montant'], 2) ?>', '<?= date('d/m/Y à H:i', strtotime($d['dateDon'])) ?>', '<?= htmlspecialchars(addslashes($d['typeDon']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($d['organisation_nom'] ?? 'N/A'), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($d['email_donateur'] ?? ''), ENT_QUOTES) ?>')" class="btn btn-info" title="Détails">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <a href="deleteDon.php?id=<?= $d['id'] ?>" class="btn btn-danger" title="Supprimer"
                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ? Cette action est irréversible.')">
                                                   <i class="bi bi-trash"></i>
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

                        <!-- Pagination -->
                        <div class="pagination-container">
                            <div class="pagination-info">
                                Affichage <span id="pageStart">1</span>-<span id="pageEnd">10</span> sur <span id="totalItems"><?= count($dons) ?></span> don(s)
                            </div>
                            <div style="flex: 1;"></div>
                            <button class="pagination-btn" id="prevBtn" onclick="previousPage()">← Précédent</button>
                            <div id="pageNumbers" style="display: flex; gap: 4px;"></div>
                            <button class="pagination-btn" id="nextBtn" onclick="nextPage()">Suivant →</button>
                            <div class="items-per-page">
                                <label for="itemsPerPage">Afficher par page:</label>
                                <select id="itemsPerPage" onchange="changeItemsPerPage()">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Détails Don -->
    <div class="modal-overlay" id="donModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-info-circle"></i> Détails du Don</h3>
                <button class="modal-close" onclick="closeDonModal()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-group">
                    <div class="detail-label">
                        <i class="bi bi-hash"></i> Numéro de Don
                    </div>
                    <div class="detail-value" id="modalDonId"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">
                        <i class="bi bi-person"></i> Donateur
                    </div>
                    <div class="detail-value" id="modalDonor"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">
                        <i class="bi bi-envelope"></i> Email
                    </div>
                    <div class="detail-value" id="modalEmail"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">
                        <i class="bi bi-currency-euro"></i> Montant
                    </div>
                    <div class="detail-value highlight" id="modalAmount"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">
                        <i class="bi bi-calendar"></i> Date du Don
                    </div>
                    <div class="detail-value" id="modalDate"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">
                        <i class="bi bi-tag"></i> Type de Don
                    </div>
                    <div class="detail-value" id="modalType"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">
                        <i class="bi bi-building"></i> Organisation
                    </div>
                    <div class="detail-value" id="modalOrganisation"></div>
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

            // Reset pagination to page 1 when filtering
            currentPage = 1;
            updatePagination();
        }

        // ========== PAGINATION CLASSIQUE ==========
        let currentPage = 1;
        let itemsPerPage = 10;

        function getAllDataRows() {
            // On ne veut paginer QUE les vraies lignes de dons, pas la ligne du total !
            return Array.from(document.querySelectorAll('tbody tr.data-row'));
        }

        function updatePagination() {
            const rows = getAllDataRows();
            const totalPages = Math.ceil(rows.length / itemsPerPage);

            // Masquer toutes les lignes
            rows.forEach(row => row.style.display = 'none');

            // Afficher uniquement les lignes de la page courante
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            rows.slice(startIndex, endIndex).forEach(row => row.style.display = '');

            // Mettre à jour l'info pagination
            document.getElementById('pageStart').textContent = rows.length > 0 ? startIndex + 1 : 0;
            document.getElementById('pageEnd').textContent = Math.min(endIndex, rows.length);
            document.getElementById('totalItems').textContent = rows.length;

            // Activer/désactiver les boutons
            document.getElementById('prevBtn').disabled = currentPage === 1;
            document.getElementById('nextBtn').disabled = currentPage === totalPages || totalPages === 0;

            // Générer les numéros de page
            const pageNumbersDiv = document.getElementById('pageNumbers');
            pageNumbersDiv.innerHTML = '';
            if (totalPages > 0) {
                for (let i = 1; i <= Math.min(totalPages, 5); i++) {
                    const btn = document.createElement('button');
                    btn.className = 'pagination-btn' + (i === currentPage ? ' active' : '');
                    btn.textContent = i;
                    btn.onclick = () => goToPage(i);
                    pageNumbersDiv.appendChild(btn);
                }
                if (totalPages > 5) {
                    const dots = document.createElement('span');
                    dots.textContent = '...';
                    dots.style.color = 'var(--text-muted)';
                    pageNumbersDiv.appendChild(dots);
                    const lastBtn = document.createElement('button');
                    lastBtn.className = 'pagination-btn';
                    lastBtn.textContent = totalPages;
                    lastBtn.onclick = () => goToPage(totalPages);
                    pageNumbersDiv.appendChild(lastBtn);
                }
            }
        }

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                updatePagination();
            }
        }

        function nextPage() {
            const rows = getAllDataRows();
            const totalPages = Math.ceil(rows.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updatePagination();
            }
        }

        function goToPage(page) {
            currentPage = page;
            updatePagination();
        }

        function changeItemsPerPage() {
            itemsPerPage = parseInt(document.getElementById('itemsPerPage').value, 10);
            currentPage = 1;
            updatePagination();
        }

        // Initialisation au chargement
        window.addEventListener('DOMContentLoaded', () => {
            updatePagination();
        });

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

        // Initialize pagination on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePagination();
        });

        // ========== MODAL FUNCTIONS ==========
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
</body>
</html>