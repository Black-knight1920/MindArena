<?php
require_once __DIR__."/../../Controller/DonController.php";
require_once __DIR__."/../../Controller/OrganisationController.php";
require_once __DIR__."/../../Model/Don.php";

$donCtrl = new DonController();
$orgCtrl = new OrganisationController();

$message = '';
$success = false;
$errors = [];

// Traitement du formulaire
if ($_POST && isset($_POST['montant'])) {
    try {
        // ... (le reste du code de traitement reste inchangé) ...
    } catch (Exception $e) {
        $message = "❌ Erreur: " . $e->getMessage();
    }
}

// Récupérer les organisations avec leurs montants
$organisations = $orgCtrl->listOrganisations(); // CHANGÉ ICI
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
  </style>
</head>

<body>

  <header>
    <h1>🎮 Mind Arena Magazine</h1>
    <nav>
      <a href="#accueil">Accueil</a>
      <a href="#donation">Faire un don</a>
      <a href="#organisations">Associations</a>
      <a href="../../backoffice.php" style="color: #b01ba5;">Espace Admin</a>
    </nav>
  </header>

  <section class="hero" id="accueil">
    <h2>Game For Good !</h2>
    <p>
      Bienvenue sur le portail Mind Arena ! Jouez, gagnez de l'XP et convertissez-le en dons pour des associations caritatives. 
      Votre gaming a du sens !
    </p>
    <a href="#donation" class="btn-don">🎁 Faire un don</a>
  </section>

  <section id="donation">
    <div class="don-container">
      <h2>Faire un Don</h2>
      
      <?php if ($message): ?>
        <div class="<?= $success ? 'success' : 'error' ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; text-align: center;">
          <?= $message ?>
        </div>
      <?php endif; ?>

      <form method="POST" id="donForm">
        <div>
          <label>💶 Montant (€)</label>
          <input type="number" name="montant" 
                 value="<?= htmlspecialchars($_POST['montant'] ?? '') ?>" 
                 placeholder="Ex: 50.00">
          <?php if (isset($errors['montant'])): ?>
            <span class="error-message"><?= $errors['montant'] ?></span>
          <?php endif; ?>
        </div>
        
        <div>
          <label>📅 Date du Don</label>
          <input type="date" name="dateDon" 
                 value="<?= htmlspecialchars($_POST['dateDon'] ?? '') ?>">
        </div>
        
        <div>
          <label>🎯 Type de Don</label>
          <select name="typeDon">
            <option value="">-- Choisir un type --</option>
            <option value="Monétaire" <?= ($_POST['typeDon'] ?? '') == 'Monétaire' ? 'selected' : '' ?>>Monétaire</option>
            <option value="Matériel" <?= ($_POST['typeDon'] ?? '') == 'Matériel' ? 'selected' : '' ?>>Matériel</option>
            <option value="Temps" <?= ($_POST['typeDon'] ?? '') == 'Temps' ? 'selected' : '' ?>>Temps (Bénévolat)</option>
          </select>
          <?php if (isset($errors['typeDon'])): ?>
            <span class="error-message"><?= $errors['typeDon'] ?></span>
          <?php endif; ?>
        </div>
        
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
        
        <button type="submit">💾 Enregistrer le Don</button>
      </form>
    </div>
  </section>

  <section id="organisations" style="background: #2d1854; padding: 80px 20px; text-align: center;">
    <h2 style="color: white; margin-bottom: 50px; font-size: 2.5rem;">Nos Associations Partenaires</h2>
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; max-width: 1200px; margin: 0 auto;">
      <?php foreach ($organisations as $org): ?>
        <div style="background: rgba(255,255,255,0.1); padding: 25px; border-radius: 15px; width: 280px; border: 1px solid #b01ba5; transition: transform 0.3s;">
          <h3 style="color: #b01ba5; margin-bottom: 15px; font-size: 1.3rem;"><?= htmlspecialchars($org['nom']) ?></h3>
          <p style="color: #ccc; font-size: 0.95em; margin-bottom: 10px;"><?= htmlspecialchars(substr($org['description'], 0, 100)) ?>...</p>
          <div style="color: #4cff4c; font-weight: bold; font-size: 1.1rem;">
            <?= number_format($org['montant_total'] ?? 0, 2) ?> € collectés
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer>
    © 2024 Mind Arena — Plateforme de dons gaming solidaire
    <br><small>Jouez utile, donnez intelligemment</small>
  </footer>

  <script src="script.js"></script>
</body>
</html>