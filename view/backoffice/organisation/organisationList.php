<?php
require_once __DIR__."/../../../Controller/OrganisationController.php";
require_once __DIR__."/../../../Controller/DonController.php";

$orgCtrl = new OrganisationController();
$donCtrl = new DonController();

// Récupérer les organisations avec leurs montants calculés
$organisations = $orgCtrl->listOrganisations(); // CHANGÉ ICI

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
    <title>Gestion des Organisations</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5; }
        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin: 20px 0; }
        th, td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: center; }
        th { 
            background-color: #081624; 
            color: white; }
        .btn { padding: 8px 16px; 
            text-decoration: none; 
            border-radius: 5px; 
            font-size: 14px;
            margin: 2px; }
        .btn-add { background: #4CAF50; color: white; }
        .btn-edit { background: #2196F3; color: white; }
        .btn-delete { background: #f44336; color: white; }
        .btn-back { background: #FF9800; color: white; }
        .header { display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; }
        .montant { font-weight: bold; }
        .montant-positive { color: #2E7D32; }
        .montant-zero { color: #666; }
        .progress-bar { 
            background: #f0f0f0; 
            border-radius: 10px; 
            height: 20px; 
            margin-top: 5px; }
        .progress-fill { 
            background: linear-gradient(90deg, #4CAF50, #45a049); 
            height: 100%; 
            border-radius: 10px; 
            text-align: center; 
            color: white; 
            font-size: 12px; 
            line-height: 20px; }
        .stats { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; }
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; }
        .stat-card { 
            background: white; 
            padding: 15px; 
            border-radius: 8px; 
            text-align: center; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat-number { 
            font-size: 1.5rem; 
            font-weight: bold; 
            color: #081624; }
        .stat-label { 
            color: #666; 
            font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Gestion des Organisations</h1>
            <div>
                <a href="addOrganisation.php" class="btn btn-add">+ Nouvelle Organisation</a>
                <a href="../don/donList.php" class="btn btn-back">Gestion des Dons</a>
                <a href="../../../backoffice.php" class="btn" style="background: #6c757d;">Accueil Admin</a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= count($organisations) ?></div>
                    <div class="stat-label">Organisations</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= number_format($totalGeneral, 2) ?> €</div>
                    <div class="stat-label">Total des dons collectés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= number_format($totalGeneral > 0 ? $totalGeneral / count($organisations) : 0, 2) ?> €</div>
                    <div class="stat-label">Moyenne par organisation</div>
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Montant Total des Dons</th>
                    <th>Pourcentage</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($organisations)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Aucune organisation trouvée</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($organisations as $org): 
                        $montant = $org['montant_total'] ?? 0;
                        $pourcentage = $totalGeneral > 0 ? ($montant / $totalGeneral) * 100 : 0;
                    ?>
                    <tr>
                        <td><?= $org['id'] ?></td>
                        <td><strong><?= htmlspecialchars($org['nom']) ?></strong></td>
                        <td><?= htmlspecialchars(substr($org['description'], 0, 80)) ?>...</td>
                        <td class="montant <?= $montant > 0 ? 'montant-positive' : 'montant-zero' ?>">
                            <?= number_format($montant, 2) ?> €
                        </td>
                        <td>
                            <?php if ($totalGeneral > 0): ?>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= min($pourcentage, 100) ?>%">
                                        <?= number_format($pourcentage, 1) ?>%
                                    </div>
                                </div>
                            <?php else: ?>
                                <span style="color: #666;">0%</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="modifyOrganisation.php?id=<?= $org['id'] ?>" class="btn btn-edit">Modifier</a>
                            <a href="deleteOrganisation.php?id=<?= $org['id'] ?>" class="btn btn-delete" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer <?= htmlspecialchars($org['nom']) ?> ?')">
                               Supprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Total général -->
        <div style="margin-top: 20px; padding: 15px; background: #081624; color: white; border-radius: 8px; text-align: center;">
            <strong>Total général collecté : <?= number_format($totalGeneral, 2) ?> €</strong>
        </div>
    </div>
</body>
</html>