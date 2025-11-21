<?php
// Page d'accueil du backoffice avec design Flexy Bootstrap Lite

// Vérifier si les fichiers existent avant de les inclure
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
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #5e72e4;
            --secondary: #8392ab;
            --success: #2dce89;
            --info: #11cdef;
            --warning: #fb6340;
            --danger: #f5365c;
            --light: #f8f9fa;
            --dark: #212529;
            --sidebar-bg: #172b4d;
            --sidebar-color: #fff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fe;
            color: #525f7f;
            line-height: 1.6;
        }

        /* ======= Sidebar ======= */
        .sidebar {
            width: 250px;
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 1rem;
        }

        .nav-item {
            margin: 0.125rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left-color: var(--primary);
        }

        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* ======= Main Content ======= */
        .main-content {
            margin-left: 250px;
            transition: all 0.3s;
            min-height: 100vh;
        }

        /* ======= Header ======= */
        .main-header {
            background: #fff;
            padding: 1rem 2rem;
            box-shadow: 0 1px 15px rgba(0, 0, 0, 0.04);
            border-bottom: 1px solid #e9ecef;
        }

        /* ======= Stats Cards ======= */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 28px rgba(50, 50, 93, 0.15), 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.primary {
            background: linear-gradient(87deg, var(--primary) 0, #825ee4 100%);
            color: white;
        }

        .stat-icon.success {
            background: linear-gradient(87deg, var(--success) 0, #2dcecc 100%);
            color: white;
        }

        .stat-icon.warning {
            background: linear-gradient(87deg, var(--warning) 0, #fbb140 100%);
            color: white;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .stat-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ======= Action Cards ======= */
        .action-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s;
            text-align: center;
            height: 100%;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(50, 50, 93, 0.15), 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .action-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            background: linear-gradient(87deg, var(--primary) 0, #825ee4 100%);
            color: white;
        }

        .action-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .action-description {
            color: var(--secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        /* ======= Alert ======= */
        .alert-custom {
            background: linear-gradient(87deg, var(--info) 0, #1171ef 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        /* ======= Buttons ======= */
        .btn {
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(87deg, var(--primary) 0, #825ee4 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        /* ======= Responsive ======= */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .main-content {
                margin-left: 0;
            }
            .main-header {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- ======= Sidebar ======= -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="#" class="sidebar-brand">
                <i class="bi bi-rocket-takeoff me-2"></i>
                Mind Arena
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-title">Navigation Principale</div>
            <div class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="nav-title">Gestion des Dons</div>
            <div class="nav-item">
                <a href="View/backoffice/don/donList.php" class="nav-link">
                    <i class="bi bi-currency-euro"></i>
                    <span>Liste des Dons</span>
                </a>
            </div>

            <div class="nav-title">Gestion des Organisations</div>
            <div class="nav-item">
                <a href="View/backoffice/organisation/organisationList.php" class="nav-link">
                    <i class="bi bi-building"></i>
                    <span>Organisations</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="View/backoffice/organisation/addOrganisation.php" class="nav-link">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nouvelle Organisation</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- ======= Main Content ======= -->
    <div class="main-content">
        <!-- ======= Header ======= -->
        <header class="main-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-dark">Tableau de Bord</h4>
                        <p class="text-muted mb-0">Bienvenue dans l'administration Mind Arena</p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted">
                            <i class="bi bi-person-circle me-2"></i>
                            Administrateur
                        </span>
                        <a href="View/frontoffice/index.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-2"></i>
                            Voir le site
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- ======= Content ======= -->
        <div class="container-fluid py-4">
            <!-- Alert -->
            <div class="alert-custom">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>
                        <h5 class="mb-1">Information Importante</h5>
                        <p class="mb-0">Les dons ne peuvent pas être modifiés après enregistrement pour garantir l'intégrité des données financières.</p>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="row mb-5">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="bi bi-currency-euro"></i>
                        </div>
                        <h3 class="stat-value"><?= number_format($totalDons, 2) ?> €</h3>
                        <p class="stat-title">Total des Dons</p>
                        <a href="View/backoffice/don/donList.php" class="btn btn-primary mt-3">
                            <i class="bi bi-eye me-2"></i>Voir les dons
                        </a>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3 class="stat-value"><?= $totalOrganisations ?></h3>
                        <p class="stat-title">Organisations</p>
                        <a href="View/backoffice/organisation/organisationList.php" class="btn btn-primary mt-3">
                            <i class="bi bi-gear me-2"></i>Gérer
                        </a>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3 class="stat-value"><?= number_format($moyenneDon, 2) ?> €</h3>
                        <p class="stat-title">Moyenne par Organisation</p>
                        <a href="View/backoffice/organisation/organisationList.php" class="btn btn-primary mt-3">
                            <i class="bi bi-bar-chart me-2"></i>Statistiques
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12 mb-4">
                    <h4 class="fw-bold text-dark mb-4">Actions Rapides</h4>
                </div>
                
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h4 class="action-title">Nouvelle Organisation</h4>
                        <p class="action-description">
                            Ajouter une nouvelle association partenaire à la plateforme de collecte
                        </p>
                        <a href="View/backoffice/organisation/addOrganisation.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Nouvelle Organisation
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="bi bi-eye"></i>
                        </div>
                        <h4 class="action-title">Voir le Site</h4>
                        <p class="action-description">
                            Accéder à la version publique pour tester l'expérience utilisateur
                        </p>
                        <a href="View/frontoffice/index.php" class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Visiter le Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
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