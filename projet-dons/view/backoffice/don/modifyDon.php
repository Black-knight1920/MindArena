<?php
require_once __DIR__."/../../../Controller/DonController.php";
require_once __DIR__."/../../../Model/Don.php";

$donCtrl = new DonController();
$message = '';
$messageType = '';

// Récupérer le don à modifier
$id = $_GET['id'] ?? 0;
$don = $donCtrl->getDon($id);

if (!$don) {
    header("Location: donList.php");
    exit;
}

if ($_POST) {
    try {
        $updatedDon = new Don(
            $id,
            (float)$_POST['montant'],
            new DateTime($_POST['dateDon']),
            $_POST['typeDon'],
            (int)$_POST['organisationId']
        );
        
        if ($donCtrl->updateDon($id, $updatedDon)) {
            $message = "✅ Don modifié avec succès!";
            $messageType = 'success';
            header("refresh:2;url=donList.php");
        }
    } catch (Exception $e) {
        $message = "❌ Erreur: " . $e->getMessage();
        $messageType = 'error';
    }
}
$organisations = $donCtrl->getOrganisationsForSelect();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Don</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f5f5ff; }
        .container { max-width: 600px; margin: 20px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box; }
        button { background: #2179f3ff; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-right: 10px; }
        button:hover { background: #216ef3ff; }
        .message { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .btn-cancel { background: #6c757d; color: white; text-decoration: none; padding: 12px 20px; border-radius: 5px; display: inline-block; }
        .btn-cancel:hover { background: #5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Modifier le Don #<?= $don['id'] ?></h1>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="montant">Montant (€) *</label>
                <input type="number" id="montant" name="montant" step="0.01" min="0.01" 
                       value="<?= htmlspecialchars($don['montant']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="dateDon">Date du don *</label>
                <input type="date" id="dateDon" name="dateDon" 
                       value="<?= htmlspecialchars($don['dateDon']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="typeDon">Type de don *</label>
                <select id="typeDon" name="typeDon" required>
                    <option value="">-- Choisir un type --</option>
                    <option value="Monétaire" <?= $don['typeDon'] == 'Monétaire' ? 'selected' : '' ?>>Monétaire</option>
                    <option value="Matériel" <?= $don['typeDon'] == 'Matériel' ? 'selected' : '' ?>>Matériel</option>
                    <option value="Temps" <?= $don['typeDon'] == 'Temps' ? 'selected' : '' ?>>Temps</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="organisationId">Organisation *</label>
                <select id="organisationId" name="organisationId" required>
                    <option value="">-- Sélectionner une organisation --</option>
                    <?php foreach ($organisations as $org): ?>
                        <option value="<?= $org['id'] ?>" 
                            <?= $don['organisationId'] == $org['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($org['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <button type="submit">💾 Mettre à jour</button>
                <a href="donList.php" class="btn-cancel">❌ Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>