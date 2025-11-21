<?php
require_once __DIR__."/../../../Controller/OrganisationController.php";
require_once __DIR__."/../../../Controller/DonController.php";

$orgCtrl = new OrganisationController();
$donCtrl = new DonController();

// Récupérer les organisations avec leurs montants calculés
$organisations = $orgCtrl->listOrganisations();

// Calculer le total général
$totalGeneral = 0;
foreach ($organisations as $org) {
    $totalGeneral += $org['montant_total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Organisations - Mind Arena</title>
    
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
            --danger: #f5365c;
            --warning: #fb6340;
            --dark: #212529;
            --sidebar-bg: #172b4d;
            --sidebar-color: #fff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fe;
            color: #525f7f;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left-color: var(--primary);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s;
            min-height: 100vh;
        }

        .main-header {
            background: #fff;
            padding: 1.5rem 2rem;
            box-shadow: 0 1px 15px rgba(0, 0, 0, 0.04);
            border-bottom: 1px solid #e9ecef;
        }

        /* Table Styling */
        .table-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .table th {
            background: linear-gradient(87deg, var(--primary) 0, #825ee4 100%);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .btn {
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 6px;
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

        .btn-success {
            background: linear-gradient(87deg, var(--success) 0, #2dcecc 100%);
            border: none;
        }

        .btn-danger {
            background: linear-gradient(87deg, var(--danger) 0, #f56036 100%);
            border: none;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(245, 54, 92, 0.2), 0 3px 6px rgba(0, 0, 0, 0.08);
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

        .montant {
            font-weight: bold;
            color: var(--success);
            font-size: 1.1rem;
        }

        .montant-zero {
            color: var(--secondary);
        }

        /* Progress Bar */
        .progress {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(87deg, var(--success) 0, #2dcecc 100%);
            border-radius: 4px;
        }

        .website-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .website-link:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        .description-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -280px;
            }
            .main-content {
                margin-left: 0;
            }
            .description-cell {
                max-width: 150px;
            }
        }
    </style>
</head>

<body>
    <!-- ======= Sidebar ======= -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h5 class="mb-0 text-white">
                <i class="bi bi-rocket-takeoff me-2"></i>
                Mind Arena
            </h5>
            <small class="text-muted">Backoffice</small>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-link" href="addOrganisation.php">
                <i class="bi bi-plus-circle me-2"></i>
                Nouvelle organisation
            </a>
            <a class="nav-link active" href="organisationList.php">
                <i class="bi bi-building me-2"></i>
                Liste des organisations
            </a>
            <a class="nav-link" href="../don/donList.php">
                <i class="bi bi-currency-euro me-2"></i>
                Dons
            </a>
            <a class="nav-link" href="/projet-dons/backoffice.php">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </nav>
    </div>

    <!-- ======= Main Content ======= -->
    <div class="main-content">
        <div class="main-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-building me-2"></i>
                        Gestion des Organisations
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 mt-2">
                            <li class="breadcrumb-item"><a href="/projet-dons/backoffice.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Organisations</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="addOrganisation.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nouvelle Organisation
                    </a>
                </div>
            </div>
        </div>

        <div class="container-fluid py-4">
            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="bi bi-building fs-1"></i>
                            </div>
                            <h3 class="text-dark"><?= count($organisations) ?></h3>
                            <p class="text-muted mb-0">Organisations</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="bi bi-currency-euro fs-1"></i>
                            </div>
                            <h3 class="text-dark"><?= number_format($totalGeneral, 2) ?> €</h3>
                            <p class="text-muted mb-0">Total collecté</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="bi bi-graph-up fs-1"></i>
                            </div>
                            <h3 class="text-dark"><?= number_format($totalGeneral > 0 ? $totalGeneral / count($organisations) : 0, 2) ?> €</h3>
                            <p class="text-muted mb-0">Moyenne par organisation</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Site Web</th>
                                <th>Montant Total</th>
                                <th>Pourcentage</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($organisations)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-building fs-1 text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-3">Aucune organisation trouvée</p>
                                        <a href="addOrganisation.php" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-2"></i>Créer la première organisation
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($organisations as $org): 
                                    $montant = $org['montant_total'] ?? 0;
                                    $pourcentage = $totalGeneral > 0 ? ($montant / $totalGeneral) * 100 : 0;
                                ?>
                                <tr>
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
                                    <td class="montant <?= $montant == 0 ? 'montant-zero' : '' ?>">
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
                                        <div class="d-flex gap-1">
                                            <a href="modifyOrganisation.php?id=<?= $org['id'] ?>" class="btn btn-success btn-sm">
                                                <i class="bi bi-pencil me-1"></i>Modifier
                                            </a>
                                            <a href="deleteOrganisation.php?id=<?= $org['id'] ?>" class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer <?= htmlspecialchars($org['nom']) ?> ?')">
                                               <i class="bi bi-trash me-1"></i>Supprimer
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>