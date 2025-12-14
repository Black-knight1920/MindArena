<?php
require_once __DIR__."/../../../Controller/DonController.php";

$donCtrl = new DonController();
$dons = $donCtrl->listDon()->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications de Dons - Mind Arena</title>
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Remix Icons -->
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif;
            background: radial-gradient(circle at top left, rgba(139,92,246,0.12), transparent 50%),
                        radial-gradient(circle at bottom right, rgba(236,72,153,0.10), transparent 50%),
                        var(--bg);
            color: var(--text);
            transition: background .25s ease, color .25s ease;
        }

        a {
            text-decoration: none;
            color: inherit;
        }
        .admin-shell {
            display: flex;
            min-height: 100vh;
        }
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

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 16px;
            cursor: pointer;
            border: none;
            transition: background .18s ease, color .18s ease;
        }

        .btn-icon:hover {
            background: var(--primary);
            color: white;
        }

        /* Theme toggle (aligné backoffice) */
        .theme-toggle {
            width: 46px;
            height: 22px;
            border-radius: 999px;
            background: rgba(148,163,184,0.6);
            position: relative;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            transition: background .25s ease;
        }

        body.dark .theme-toggle {
            background: rgba(15,23,42,0.75);
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

        body.dark .theme-toggle-thumb {
            transform: translateX(22px);
        }

        /* ---------- CONTENT AREA ---------- */
        .admin-content {
            flex: 1;
            overflow-y: auto;
            padding: 30px 22px;
        }

        .content-wrapper {
            max-width: 1400px;
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

        /* ================= NOTIFICATIONS STYLES ================= */
        .notifications-header {
            margin-bottom: 30px;
        }

        .notifications-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .notifications-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .notifications-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .notification-item {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--card-border);
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            transition: all 0.3s ease;
            animation: slideInDown 0.5s ease forwards;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-item:hover {
            box-shadow: var(--shadow-soft);
            transform: translateY(-2px);
        }

        .notification-item.monetary {
            border-left: 4px solid var(--success);
        }

        .notification-item.material {
            border-left: 4px solid var(--warning);
        }

        .notification-badge {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .notification-badge.monetary {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .notification-badge.material {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-donor {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        .notification-details {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .detail-item i {
            font-size: 14px;
            color: var(--primary);
        }

        .notification-amount {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            white-space: nowrap;
        }

        .donation-type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .donation-type-badge.monetary {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }

        .donation-type-badge.material {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning);
        }

        .empty-state {
            text-align: center;
            padding: 60px 30px;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--text-muted);
        }

        .empty-state h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--text);
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .btn-primary {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background .18s ease;
            display: inline-block;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        /* Dark mode toggle */
        .theme-toggle {
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 70px;
            }

            .sidebar-title span:last-child,
            .sidebar-nav-label {
                display: none;
            }

            .sidebar-link {
                justify-content: center;
                padding: 9px;
            }

            .notification-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .notification-amount {
                align-self: flex-end;
            }

            .notification-details {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body class="light">
    <div class="admin-shell">
        <!-- MAIN CONTENT -->
        <div class="admin-main">
            <!-- HEADER -->
            <header class="admin-header">
                <div class="header-left">
                    <a href="../../../backoffice.php" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                        <i class="bi bi-arrow-left" style="font-size: 20px; color: var(--primary);"></i>
                        <div>
                            <span class="header-left-title">Notifications de Dons</span>
                            <span class="header-left-sub">Retour au backoffice</span>
                        </div>
                    </a>
                </div>
                <div class="header-actions">
                    <button class="btn-icon" id="refreshBtn" title="Actualiser">
                        <i class="ri-refresh-line"></i>
                    </button>
                    <div class="theme-toggle" id="themeToggle" title="Mode sombre">
                        <div class="theme-toggle-thumb"><i class="ri-sun-fill"></i></div>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <div class="admin-content">
                <div class="content-wrapper">
                    <div class="notifications-header">
                        <h2>📬 Notifications de Dons</h2>
                        <p>Suivez toutes les contributions reçues en temps réel</p>
                    </div>

                    <div class="card-shell">
                        <div class="notifications-container">
                            <?php
                            if (empty($dons)) {
                            ?>
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <h3>Aucun don pour le moment</h3>
                                    <p>Les dons apparaîtront ici dès qu'ils seront reçus</p>
                                    <a href="index.php" class="btn-primary">
                                        <i class="bi bi-arrow-left"></i> Retour à l'accueil
                                    </a>
                                </div>
                            <?php
                            } else {
                                foreach ($dons as $don) {
                                    $nomDonateur = trim(($don['prenom_donateur'] ?? '') . ' ' . ($don['nom_donateur'] ?? ''));
                                    $nomDonateur = empty($nomDonateur) ? 'Donateur Anonyme' : $nomDonateur;
                                    $montant = number_format($don['montant'], 2, ',', ' ');
                                    $dateDon = date('d/m/Y à H:i', strtotime($don['dateDon']));
                                    $typeDon = $don['typeDon'];
                                    $organisationNom = htmlspecialchars($don['organisation_nom'] ?? 'Organisation inconnue');
                                    
                                    $typeClass = $typeDon === 'Monétaire' ? 'monetary' : 'material';
                                    $typeLabel = $typeDon === 'Monétaire' ? '💰 Monétaire' : '📦 Matériel';
                                    $icon = $typeDon === 'Monétaire' ? '💵' : '📦';
                            ?>
                                <div class="notification-item <?php echo $typeClass; ?>">
                                    <div class="notification-badge <?php echo $typeClass; ?>">
                                        <?php echo $icon; ?>
                                    </div>

                                    <div class="notification-content">
                                        <div class="notification-donor">
                                            <?php echo htmlspecialchars($nomDonateur); ?> a fait un don
                                        </div>
                                        <div class="notification-details">
                                            <div class="detail-item">
                                                <i class="ri-building-line"></i>
                                                <span><?php echo $organisationNom; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="ri-calendar-line"></i>
                                                <span><?php echo $dateDon; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="donation-type-badge <?php echo $typeClass; ?>">
                                                    <?php echo $typeLabel; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="notification-amount">
                                        <?php echo $montant; ?> €
                                    </div>
                                </div>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dark/Light mode toggle 
        (function () {
            const toggle = document.getElementById('themeToggle');
            const thumb = toggle ? toggle.querySelector('.theme-toggle-thumb') : null;
            const body = document.body;
            const storageKey = 'ma-admin-theme';

            const applyTheme = (theme) => {
                if (theme === 'dark') {
                    body.classList.add('dark');
                    body.classList.remove('light');
                    if (thumb) thumb.innerHTML = '<i class="ri-moon-fill"></i>';
                } else {
                    body.classList.remove('dark');
                    body.classList.add('light');
                    if (thumb) thumb.innerHTML = '<i class="ri-sun-fill"></i>';
                }
                localStorage.setItem(storageKey, theme);
            };

            const saved = localStorage.getItem(storageKey) || 'light';
            applyTheme(saved);

            if (toggle) {
                toggle.addEventListener('click', () => {
                    const next = body.classList.contains('dark') ? 'light' : 'dark';
                    applyTheme(next);
                });
            }
        })();

        // Bouton actualiser
        document.getElementById('refreshBtn').addEventListener('click', () => {
            location.reload();
        });

        // Auto-refresh toutes les 30 secondes
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
