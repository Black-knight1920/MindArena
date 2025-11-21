<?php
require_once __DIR__."/../../../Controller/DonController.php";

$donCtrl = new DonController();
$dons = $donCtrl->listDon();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Dons - Mind Arena</title>
    
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

        .donateur {
            color: var(--primary);
            font-weight: 600;
        }

        .anonyme {
            color: var(--secondary);
            font-style: italic;
        }

        .type-monetaire {
            background: rgba(46, 206, 137, 0.1);
        }

        .type-materiel {
            background: rgba(94, 114, 228, 0.1);
        }

        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .total-label {
            text-align: right;
            padding-right: 20px;
            color: var(--dark);
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -280px;
            }
            .main-content {
                margin-left: 0;
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
            <a class="nav-link active" href="donList.php">
                <i class="bi bi-currency-euro me-2"></i>
                Liste des dons
            </a>
            <a class="nav-link" href="../organisation/organisationList.php">
                <i class="bi bi-building me-2"></i>
                Organisations
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
                        <i class="bi bi-currency-euro me-2"></i>
                        Liste des Dons
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 mt-2">
                            <li class="breadcrumb-item"><a href="/projet-dons/backoffice.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Dons</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="../organisation/organisationList.php" class="btn btn-outline-primary">
                        <i class="bi bi-building me-2"></i>
                        Organisations
                    </a>
                </div>
            </div>
        </div>

        <div class="container-fluid py-4">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Donateur</th>
                                <th>Montant</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Organisation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $donsData = $dons->fetchAll();
                            $total = 0;
                            ?>
                            <?php if (empty($donsData)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-3">Aucun don trouvé</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($donsData as $d): 
                                    $total += $d['montant'];
                                    $nomComplet = '';
                                    if (!empty($d['prenom_donateur']) || !empty($d['nom_donateur'])) {
                                        $nomComplet = trim(($d['prenom_donateur'] ?? '') . ' ' . ($d['nom_donateur'] ?? ''));
                                    }
                                ?>
                                <tr class="type-<?= strtolower($d['typeDon']) ?>">
                                    <td><strong>#<?= $d['id'] ?></strong></td>
                                    <td class="donateur">
                                        <?php if (!empty($nomComplet)): ?>
                                            <i class="bi bi-person me-1"></i><?= htmlspecialchars($nomComplet) ?>
                                        <?php else: ?>
                                            <span class="anonyme"><i class="bi bi-eye-slash me-1"></i>Anonyme</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="montant"><?= number_format($d['montant'], 2) ?> €</td>
                                    <td><?= date('d/m/Y', strtotime($d['dateDon'])) ?></td>
                                    <td>
                                        <?php if ($d['typeDon'] === 'Monétaire'): ?>
                                            <span class="badge bg-success"><i class="bi bi-currency-euro me-1"></i><?= htmlspecialchars($d['typeDon']) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-primary"><i class="bi bi-box me-1"></i><?= htmlspecialchars($d['typeDon']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><i class="bi bi-building me-1"></i><?= htmlspecialchars($d['organisation_nom'] ?? 'N/A') ?></strong></td>
                                    <td>
                                        <a href="deleteDon.php?id=<?= $d['id'] ?>" class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ? Cette action est irréversible.')">
                                           <i class="bi bi-trash me-1"></i>Supprimer
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <!-- Ligne du total -->
                                <tr class="total-row">
                                    <td colspan="2" class="total-label">
                                        <i class="bi bi-graph-up me-2"></i>Total des dons :
                                    </td>
                                    <td class="montant" style="font-size: 1.2rem;"><?= number_format($total, 2) ?> €</td>
                                    <td colspan="4"></td>
                                </tr>
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