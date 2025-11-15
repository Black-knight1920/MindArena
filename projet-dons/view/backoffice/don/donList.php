<?php
require_once __DIR__."/../../../Controller/DonController.php";

$donCtrl = new DonController();
$dons = $donCtrl->listDon();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Dons</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #081624; color: white; }
        .btn { padding: 8px 16px; text-decoration: none; border-radius: 5px; font-size: 14px; margin: 2px; }
        .btn-add { background: #4CAF50; color: white; }
        .btn-edit { background: #2196F3; color: white; }
        .btn-delete { background: #f44336; color: white; }
        .btn-back { background: #FF9800; color: white; }
        .montant { font-weight: bold; color: #2E7D32; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .type-monetaire { background: #e8f5e8; }
        .type-materiel { background: #e3f2fd; }
        .type-temps { background: #fff3e0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 Gestion des Dons</h1>
            <div>
                <a href="addDon.php" class="btn btn-add">+ Nouveau Don</a>
                <a href="../organisation/organisationList.php" class="btn btn-back">Gestion des Organisations</a>
                <a href="../../../backoffice.php" class="btn" style="background: #6c757d;">Accueil Admin</a>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
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
                if (empty($donsData)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Aucun don trouvé</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($donsData as $d): ?>
                    <tr class="type-<?= strtolower($d['typeDon']) ?>">
                        <td><?= $d['id'] ?></td>
                        <td class="montant"><?= number_format($d['montant'], 2) ?> €</td>
                        <td><?= date('d/m/Y', strtotime($d['dateDon'])) ?></td>
                        <td><?= htmlspecialchars($d['typeDon']) ?></td>
                        <td><strong><?= htmlspecialchars($d['organisation_nom'] ?? 'N/A') ?></strong></td>
                        <td>
                            <a href="modifyDon.php?id=<?= $d['id'] ?>" class="btn btn-edit">Modifier</a>
                            <a href="deleteDon.php?id=<?= $d['id'] ?>" class="btn btn-delete" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?')">
                               Supprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 20px; color: #666;">
            <strong>Total des dons :</strong> 
            <?php
            $total = 0;
            foreach ($donsData as $d) {
                $total += $d['montant'];
            }
            echo number_format($total, 2) . ' €';
            ?>
        </div>
    </div>
</body>
</html>
