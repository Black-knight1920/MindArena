<?php
require_once __DIR__."/../../Controller/DonController.php";
require_once __DIR__."/../../Controller/OrganisationController.php";
require_once __DIR__."/../../Model/Don.php";

$donCtrl = new DonController();
$orgCtrl = new OrganisationController();

$message = '';
$success = false;
$errors = [];
$selectedOrgId = isset($_GET['orgId']) ? intval($_GET['orgId']) : null;

// Traitement du formulaire
if ($_POST && isset($_POST['montant'])) {
    $montant = floatval($_POST['montant']);
    $dateDon = $_POST['dateDon'];
    $typeDon = $_POST['typeDon'];
    $organisationId = intval($_POST['organisationId']);
    $nomDonateur = $_POST['nom_donateur'] ?? '';
    $prenomDonateur = $_POST['prenom_donateur'] ?? '';
    
    // Validation des données
    if (empty($montant) || $montant <= 0) {
        $errors['montant'] = "Le montant doit être supérieur à 0";
    }
    if (empty($dateDon)) {
        $errors['dateDon'] = "La date est obligatoire";
    }
    if (empty($typeDon)) {
        $errors['typeDon'] = "Veuillez choisir un type de don";
    }
    if (empty($organisationId)) {
        $errors['organisationId'] = "Veuillez sélectionner une organisation";
    }
    
    // Si pas d'erreurs, créer le don
    if (empty($errors)) {
        $don = new Don(
            null,
            $montant,
            new DateTime($dateDon),
            $typeDon,
            $organisationId,
            null,
            $nomDonateur,
            $prenomDonateur
        );
        
        // Validation et ajout
        $validationErrors = $donCtrl->validateDon($don);
        if (empty($validationErrors)) {
            if ($donCtrl->addDon($don)) {
                $message = "✅ Don ajouté avec succès!";
                $success = true;
                // Réinitialiser le formulaire
                $_POST = [];
                $selectedOrgId = null;
            } else {
                $message = "❌ Erreur lors de l'ajout du don";
            }
        } else {
            $message = "❌ Erreurs de validation:<br>" . implode("<br>", $validationErrors);
        }
    } else {
        $message = "❌ Veuillez corriger les erreurs ci-dessous";
    }
}

// Récupérer les organisations avec leurs montants
$organisations = $orgCtrl->listOrganisations();

// Si une organisation est sélectionnée via GET, on la pré-remplit
if ($selectedOrgId) {
    $selectedOrg = null;
    foreach ($organisations as $org) {
        if ($org['id'] === $selectedOrgId) {
            $selectedOrg = $org;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mind Arena - Faire un Don</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .error-message {
      color: #ff4d4d;
      font-size: 0.85rem;
      margin-top: 5px;
      display: block;
    }
    .back-button {
      display: inline-block;
      background: #6b7280;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      margin-bottom: 20px;
      transition: background 0.3s;
    }
    .back-button:hover {
      background: #4b5563;
    }
    .org-selected {
      background: rgba(176, 27, 165, 0.2);
      padding: 15px;
      border-radius: 10px;
      border: 1px solid #b01ba5;
      margin-bottom: 20px;
    }
  </style>
</head>

<body>

  <header>
    <h1>🎮 Mind Arena Magazine</h1>
    <nav>
      <a href="index.php">Accueil</a>
      <a href="addDon.php">Faire un don</a>
      <a href="index.php#organisations">Associations</a>
      <a href="../../backoffice.php" style="color: #b01ba5;">Espace Admin</a>
    </nav>
  </header>

  <section id="donation" style="padding: 100px 20px 80px;">
    <div class="don-container">
      <a href="index.php" class="back-button">← Retour à l'accueil</a>
      
      <h2>Faire un Don</h2>
      
      <?php if ($message): ?>
        <div class="<?= $success ? 'success' : 'error' ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; text-align: center;">
          <?= $message ?>
        </div>
      <?php endif; ?>

      <?php if ($selectedOrgId && $selectedOrg): ?>
        <div class="org-selected">
          <h3 style="color: #b01ba5; margin: 0;">
            🎯 Vous faites un don à : <?= htmlspecialchars($selectedOrg['nom']) ?>
          </h3>
          <p style="color: #ccc; margin: 5px 0 0 0; font-size: 0.9em;">
            <?= number_format($selectedOrg['montant_total'] ?? 0, 2) ?> € déjà collectés
          </p>
        </div>
      <?php endif; ?>

      <form method="POST" id="donForm">
        <input type="hidden" name="organisationId" value="<?= $selectedOrgId ?>">
        
        <div>
          <label>👤 Nom du donateur (optionnel)</label>
          <input type="text" name="nom_donateur" 
                 value="<?= htmlspecialchars($_POST['nom_donateur'] ?? '') ?>" 
                 placeholder="Ex: Dupont">
        </div>
        
        <div>
          <label>👤 Prénom du donateur (optionnel)</label>
          <input type="text" name="prenom_donateur" 
                 value="<?= htmlspecialchars($_POST['prenom_donateur'] ?? '') ?>" 
                 placeholder="Ex: Jean">
        </div>
        
        <div>
          <label>💶 Montant (€)</label>
          <input type="number" name="montant" 
                 value="<?= htmlspecialchars($_POST['montant'] ?? '') ?>" 
                 placeholder="Ex: 50.00" step="0.01">
          <?php if (isset($errors['montant'])): ?>
            <span class="error-message"><?= $errors['montant'] ?></span>
          <?php endif; ?>
        </div>
        
        <div>
          <label>📅 Date du Don</label>
          <input type="date" name="dateDon" 
                 value="<?= htmlspecialchars($_POST['dateDon'] ?? '') ?>">
          <?php if (isset($errors['dateDon'])): ?>
            <span class="error-message"><?= $errors['dateDon'] ?></span>
          <?php endif; ?>
        </div>
        
        <div>
          <label>🎯 Type de Don</label>
          <select name="typeDon">
            <option value="">-- Choisir un type --</option>
            <option value="Monétaire" <?= ($_POST['typeDon'] ?? '') == 'Monétaire' ? 'selected' : '' ?>>Monétaire</option>
            <option value="Matériel" <?= ($_POST['typeDon'] ?? '') == 'Matériel' ? 'selected' : '' ?>>Matériel</option>
          </select>
          <?php if (isset($errors['typeDon'])): ?>
            <span class="error-message"><?= $errors['typeDon'] ?></span>
          <?php endif; ?>
        </div>
        
        <?php if (!$selectedOrgId): ?>
          <div>
            <label>🏢 Organisation Bénéficiaire</label>
            <select name="organisationId">
              <option value="">-- Sélectionner une organisation --</option>
              <?php foreach ($organisations as $org): ?>
                <option value="<?= $org['id'] ?>" 
                  <?= ($_POST['organisationId'] ?? '') == $org['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($org['nom']) ?> 
                  (<?= number_format($org['montant_total'] ?? 0, 2) ?> € collectés)
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['organisationId'])): ?>
              <span class="error-message"><?= $errors['organisationId'] ?></span>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <input type="hidden" name="organisationId" value="<?= $selectedOrgId ?>">
        <?php endif; ?>
        
        <button type="submit">💾 Enregistrer le Don</button>
      </form>
    </div>
  </section>

  <footer>
    © 2024 Mind Arena — Plateforme de dons gaming solidaire
    <br><small>Jouez utile, donnez intelligemment</small>
  </footer>

  <script src="script.js"></script>
</body>
</html>