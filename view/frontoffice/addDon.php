<?php
require_once __DIR__."/../../Controller/DonController.php";
require_once __DIR__."/../../Controller/OrganisationController.php";
require_once __DIR__."/../../Model/Don.php";
require_once __DIR__."/../../Model/DonateurClasse.php";

$donCtrl = new DonController();
$orgCtrl = new OrganisationController();

$message = '';
$success = false;
$errors = [];
$selectedOrgId = isset($_GET['orgId']) ? intval($_GET['orgId']) : null;
$infosDonateur = null;

// Traitement du formulaire
if ($_POST && isset($_POST['montant'])) {
    $montant = isset($_POST['montant']) ? floatval(str_replace(',', '.', $_POST['montant'])) : 0;
    $dateDon = $_POST['dateDon'] ?? '';
    $typeDon = $_POST['typeDon'] ?? '';
    $organisationId = isset($_POST['organisationId']) ? intval($_POST['organisationId']) : 0;
    $nomDonateur = $_POST['nom_donateur'] ?? '';
    $prenomDonateur = $_POST['prenom_donateur'] ?? '';
    
    // Validation des données
    if (!isset($montant) || $montant <= 0) {
        $errors['montant'] = "❌ Le montant doit être supérieur à 0";
    }
    
    if (empty($dateDon) || !DateTime::createFromFormat('Y-m-d', $dateDon)) {
        $errors['dateDon'] = "❌ Date invalide ou format incorrect (YYYY-MM-DD)";
    } else {
        $selectedDate = new DateTime($dateDon);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        if ($selectedDate > $today) {
            $errors['dateDon'] = "❌ La date ne peut pas être dans le futur";
        }
    }
    
    if (empty($typeDon)) {
        $errors['typeDon'] = "❌ Veuillez choisir un type de don";
    }
    
    if (empty($organisationId)) {
        $errors['organisationId'] = "❌ Veuillez sélectionner une organisation";
    }
    
    // NOUVEAU : Validation du nom et prénom (obligatoires)
    if (empty(trim($nomDonateur))) {
        $errors['nom_donateur'] = "❌ Le nom du donateur est obligatoire";
    }
    
    if (empty(trim($prenomDonateur))) {
        $errors['prenom_donateur'] = "❌ Le prénom du donateur est obligatoire";
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
                
                // AFFICHER LES INFOS DU DONATEUR APRÈS SUCCÈS
                if (!empty($nomDonateur) && !empty($prenomDonateur)) {
                    $infosDonateur = $donCtrl->getInfosDonateur($nomDonateur, $prenomDonateur);
                    $prochainPalier = DonateurClasse::getProchainPalier($infosDonateur['total_dons']);
                    $progression = DonateurClasse::getProgression($infosDonateur['total_dons']);
                    
                    $message .= "<div class='donateur-success'>";
                    $message .= "<div class='classe-badge' style='font-size: 2rem; margin: 10px 0;'>{$infosDonateur['badge']}</div>";
                    $message .= "<h3 style='color: {$infosDonateur['couleur']}; margin: 10px 0;'>Félicitations ! Vous êtes {$infosDonateur['classe']}</h3>";
                    $message .= "<p>Total des dons: <strong>" . number_format($infosDonateur['total_dons'], 2) . " €</strong></p>";
                    
                    if ($prochainPalier) {
                        $message .= "<div style='margin: 15px 0;'>";
                        $message .= "<p>Prochain palier: <strong>{$prochainPalier}</strong></p>";
                        $message .= "<div class='progress-bar' style='width: 100%; height: 15px; background: #333; border-radius: 10px; overflow: hidden;'>";
                        $message .= "<div class='progress' style='height: 100%; background: linear-gradient(90deg, {$infosDonateur['couleur']}, #b01ba5); width: {$progression}%'></div>";
                        $message .= "</div>";
                        $message .= "<small>Progression: " . number_format($progression, 1) . "%</small>";
                        $message .= "</div>";
                    } else {
                        $message .= "<p>🎉 Vous avez atteint le niveau maximum !</p>";
                    }
                    $message .= "</div>";
                }
                
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
$selectedOrg = null;
if ($selectedOrgId) {
    foreach ($organisations as $org) {
        if ($org['id'] === $selectedOrgId) {
            $selectedOrg = $org;
            break;
        }
    }
}

// Afficher les infos du donateur si nom/prénom sont remplis
$nomActuel = $_POST['nom_donateur'] ?? '';
$prenomActuel = $_POST['prenom_donateur'] ?? '';
if (!empty($nomActuel) && !empty($prenomActuel) && !$success) {
    $infosDonateur = $donCtrl->getInfosDonateur($nomActuel, $prenomActuel);
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
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Roboto', sans-serif;
        background: #501755;
        color: #fff;
        line-height: 1.6;
        overflow-x: hidden;
        width: 100%;
    }

    /* ----- Header ----- */
    header {
        background: #081624;
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 0 9px 3px rgba(226,30,228,.24);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        left: 0;
    }

    header h1 {
        font-size: 1.5rem;
        color: #fff;
        margin: 0;
    }

    nav a {
        color: #fff;
        text-decoration: none;
        margin-left: 1.5rem;
        font-weight: 500;
        transition: color 0.3s;
    }

    nav a:hover { 
        color: #b01ba5; 
    }

    /* ----- Donation Form ----- */
    section#donation {
        background: linear-gradient(45deg,#501755 0%,#2d1854 100%);
        padding: 100px 20px 80px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: 100vh;
    }

    .don-container {
        background: rgba(255,255,255,.08);
        border: 2px solid #b01ba5;
        box-shadow: 0 0 15px rgba(176,27,165,.4);
        border-radius: 16px;
        max-width: 500px;
        width: 100%;
        padding: 2rem 2.5rem;
        text-align: left;
        backdrop-filter: blur(10px);
        margin: 0 auto;
    }

    .don-container h2 {
        text-align: center;
        font-style: italic;
        color: #fff;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        font-size: 1.8rem;
    }

    .error-message {
      color: #ff4d4d;
      font-size: 0.85rem;
      margin-top: 5px;
      display: block;
      font-weight: 500;
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
    .form-group {
      margin-bottom: 20px;
      position: relative;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: white;
      font-weight: 500;
    }
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 2px solid #4a5568;
      background: rgba(255, 255, 255, 0.1);
      color: white;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #b01ba5;
      background: rgba(255, 255, 255, 0.15);
    }
    .form-group input.invalid,
    .form-group select.invalid {
      border-color: #ff4d4d !important;
      background: rgba(255, 77, 77, 0.1) !important;
    }
    .form-group input.valid,
    .form-group select.valid {
      border-color: #4cff4c !important;
      background: rgba(76, 255, 76, 0.1) !important;
    }
    .success {
      background: rgba(76, 255, 76, 0.2);
      color: #4cff4c;
      border: 1px solid #4cff4c;
      padding: 15px;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 20px;
    }
    .error {
      background: rgba(255, 77, 77, 0.2);
      color: #ff4d4d;
      border: 1px solid #ff4d4d;
      padding: 15px;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 20px;
    }
    button[type="submit"] {
      background: #b01ba5;
      color: white;
      border: none;
      padding: 15px 30px;
      border-radius: 8px;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s ease;
      margin-top: 20px;
    }
    button[type="submit"]:hover {
      background: #d93ee7;
      transform: translateY(-2px);
    }
    .donateur-info {
      background: rgba(255, 255, 255, 0.1);
      padding: 15px;
      border-radius: 10px;
      border: 2px solid #b01ba5;
      margin-bottom: 20px;
      text-align: center;
    }
    .donateur-success {
      background: rgba(76, 255, 76, 0.1);
      padding: 20px;
      border-radius: 10px;
      border: 2px solid #4cff4c;
      margin-top: 15px;
      text-align: center;
    }
    .classe-badge {
      font-size: 2rem;
      margin: 10px 0;
    }
    .progress-bar {
      width: 100%;
      height: 15px;
      background: #333;
      border-radius: 10px;
      overflow: hidden;
      margin: 10px 0;
    }
    .progress {
      height: 100%;
      transition: width 0.5s ease;
    }
    .classement-link {
      display: inline-block;
      background: #6b7280;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      margin-top: 15px;
      transition: background 0.3s;
    }
    .classement-link:hover {
      background: #4b5563;
    }

    /* ----- Footer ----- */
    footer {
        background: #190d36;
        text-align: center;
        padding: 2rem 1rem;
        font-size: .9rem;
        color: #aaa;
        line-height: 1.8;
        width: 100%;
        left: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        header {
            padding: 15px 20px;
            flex-direction: column;
            gap: 15px;
        }
        
        nav a {
            margin: 0 10px;
        }
        
        section#donation {
            padding: 120px 15px 60px;
        }
        
        .don-container {
            margin: 0 10px;
            padding: 1.5rem;
        }
        
        .error-message {
            font-size: 0.8rem;
        }
    }
  </style>
</head>

<body>

  <header>
    <h1>🎮 Mind Arena Magazine</h1>
    <nav>
      <a href="index.php">Accueil</a>
      <a href="classementDonateurs.php">Classement</a>
      <a href="index.php#organisations">Associations</a>
      <a href="../../backoffice.php" style="color: #b01ba5;">Espace Admin</a>
    </nav>
  </header>

  <section id="donation">
    <div class="don-container">
      <a href="index.php" class="back-button">← Retour à l'accueil</a>
      
      <h2>Faire un Don</h2>
      
      <?php if ($message): ?>
        <div class="<?= $success ? 'success' : 'error' ?>">
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

      <!-- AFFICHAGE DES INFOS DU DONATEUR SI REMPLI -->
      <?php if ($infosDonateur && !$success): ?>
        <div class="donateur-info">
          <div class="classe-badge"><?= $infosDonateur['badge'] ?></div>
          <h3 style="color: <?= $infosDonateur['couleur'] ?>; margin: 10px 0;">
            <?= htmlspecialchars($infosDonateur['prenom']) ?> <?= htmlspecialchars($infosDonateur['nom']) ?> - <?= $infosDonateur['classe'] ?>
          </h3>
          <p>Total des dons: <strong><?= number_format($infosDonateur['total_dons'], 2) ?> €</strong></p>
          
          <?php 
          $prochainPalier = DonateurClasse::getProchainPalier($infosDonateur['total_dons']);
          $progression = DonateurClasse::getProgression($infosDonateur['total_dons']);
          ?>
          
          <?php if ($prochainPalier): ?>
            <div style="margin: 15px 0;">
              <p>Prochain palier: <strong><?= $prochainPalier ?></strong></p>
              <div class="progress-bar">
                <div class="progress" style="width: <?= $progression ?>%; background: linear-gradient(90deg, <?= $infosDonateur['couleur'] ?>, #b01ba5);"></div>
              </div>
              <small>Progression: <?= number_format($progression, 1) ?>%</small>
            </div>
          <?php else: ?>
            <p>🎉 Vous avez atteint le niveau maximum !</p>
          <?php endif; ?>
          
          <a href="classementDonateurs.php" class="classement-link">🏆 Voir le classement</a>
        </div>
      <?php endif; ?>

      <form method="POST" id="donForm">
        <input type="hidden" name="organisationId" value="<?= $selectedOrgId ?>">
        
        <div class="form-group">
          <label>👤 Nom du donateur *</label>
          <input type="text" name="nom_donateur" 
                 value="<?= htmlspecialchars($_POST['nom_donateur'] ?? '') ?>" 
                 placeholder="Ex: Dupont"
                 class="form-control"
                 id="nomDonateur">
          <span class="error-message" id="nomError">
            <?php if (isset($errors['nom_donateur'])) echo $errors['nom_donateur']; ?>
          </span>
        </div>
        
        <div class="form-group">
          <label>👤 Prénom du donateur *</label>
          <input type="text" name="prenom_donateur" 
                 value="<?= htmlspecialchars($_POST['prenom_donateur'] ?? '') ?>" 
                 placeholder="Ex: Jean"
                 class="form-control"
                 id="prenomDonateur">
          <span class="error-message" id="prenomError">
            <?php if (isset($errors['prenom_donateur'])) echo $errors['prenom_donateur']; ?>
          </span>
        </div>
        
        <div class="form-group">
          <label>💶 Montant (€) *</label>
          <input type="number" name="montant" 
                 value="<?= htmlspecialchars($_POST['montant'] ?? '') ?>" 
                 placeholder="Ex: 50.00" step="0.01" min="0.01" max="1000000"
                 class="form-control"
                 id="montant">
          <span class="error-message" id="montantError">
            <?php if (isset($errors['montant'])) echo $errors['montant']; ?>
          </span>
        </div>
        
        <div class="form-group">
          <label>📅 Date du Don *</label>
          <input type="date" name="dateDon" 
                 value="<?= htmlspecialchars($_POST['dateDon'] ?? '') ?>"
                 max="<?= date('Y-m-d') ?>"
                 class="form-control"
                 id="dateDon">
          <span class="error-message" id="dateDonError">
            <?php if (isset($errors['dateDon'])) echo $errors['dateDon']; ?>
          </span>
        </div>
        
        <div class="form-group">
          <label>🎯 Type de Don *</label>
          <select name="typeDon" class="form-control" id="typeDon">
            <option value="">-- Choisir un type --</option>
            <option value="Monétaire" <?= ($_POST['typeDon'] ?? '') == 'Monétaire' ? 'selected' : '' ?>>Monétaire</option>
            <option value="Matériel" <?= ($_POST['typeDon'] ?? '') == 'Matériel' ? 'selected' : '' ?>>Matériel</option>
          </select>
          <span class="error-message" id="typeDonError">
            <?php if (isset($errors['typeDon'])) echo $errors['typeDon']; ?>
          </span>
        </div>
        
        <?php if (!$selectedOrgId): ?>
          <div class="form-group">
            <label>🏢 Organisation Bénéficiaire *</label>
            <select name="organisationId" class="form-control" id="organisationId">
              <option value="">-- Sélectionner une organisation --</option>
              <?php foreach ($organisations as $org): ?>
                <option value="<?= $org['id'] ?>" 
                  <?= ($_POST['organisationId'] ?? '') == $org['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($org['nom']) ?> 
                  (<?= number_format($org['montant_total'] ?? 0, 2) ?> € collectés)
                </option>
              <?php endforeach; ?>
            </select>
            <span class="error-message" id="organisationIdError">
              <?php if (isset($errors['organisationId'])) echo $errors['organisationId']; ?>
            </span>
          </div>
        <?php else: ?>
          <input type="hidden" name="organisationId" value="<?= $selectedOrgId ?>">
        <?php endif; ?>
        
        <button type="submit">💾 Enregistrer le Don</button>
        <p style="color: #ccc; font-size: 0.8rem; margin-top: 10px; text-align: center;">
          * Champs obligatoires
        </p>
      </form>
      
      <?php if (!$infosDonateur && !$success): ?>
        <div style="text-align: center; margin-top: 30px; padding: 20px; background: rgba(255,255,255,0.05); border-radius: 10px;">
          <h3 style="color: #b01ba5;">🎮 Système de Classes</h3>
          <p style="color: #ccc; margin-bottom: 15px;">
            Remplissez votre nom et prénom pour suivre votre progression et débloquer des classes !
          </p>
          <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
            <span style="color: #00FF00;">🟢 Novice (0€)</span>
            <span style="color: #0066FF;">🔵 Bénévole (500€)</span>
            <span style="color: #9900FF;">🟣 Partenaire (2 000€)</span>
            <span style="color: #FF6600;">🟠 Mécène (10 000€)</span>
            <span style="color: #FF0000;">⚡ Légende (50 000€)</span>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <footer>
    © 2024 Mind Arena — Plateforme de dons gaming solidaire
    <br><small>Jouez utile, donnez intelligemment</small>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('donForm');
        const fields = {
            nom_donateur: document.getElementById('nomDonateur'),
            prenom_donateur: document.getElementById('prenomDonateur'),
            montant: document.getElementById('montant'),
            dateDon: document.getElementById('dateDon'),
            typeDon: document.getElementById('typeDon'),
            organisationId: document.getElementById('organisationId')
        };

        // Validation en temps réel
        Object.keys(fields).forEach(fieldName => {
            const field = fields[fieldName];
            if (field) {
                field.addEventListener('blur', function() {
                    validateField(fieldName);
                });
                
                field.addEventListener('input', function() {
                    // Supprimer le style d'erreur quand l'utilisateur commence à taper
                    if (this.classList.contains('invalid')) {
                        this.classList.remove('invalid');
                        const errorElement = document.getElementById(fieldName + 'Error');
                        if (errorElement) {
                            errorElement.textContent = '';
                        }
                    }
                });
            }
        });

        // Validation à la soumission
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            Object.keys(fields).forEach(fieldName => {
                if (!validateField(fieldName)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Scroll vers la première erreur
                const firstError = document.querySelector('.form-control.invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        function validateField(fieldName) {
            const field = fields[fieldName];
            if (!field) return true;
            
            const errorElement = document.getElementById(fieldName + 'Error');
            const value = field.value.trim();
            
            // Réinitialiser
            field.classList.remove('valid', 'invalid');
            if (errorElement) {
                errorElement.textContent = '';
            }
            
            let isValid = true;
            let message = '';
            
            switch(fieldName) {
                case 'nom_donateur':
                    if (!value) {
                        message = "❌ Le nom du donateur est obligatoire";
                        isValid = false;
                    } else if (value.length < 2) {
                        message = "❌ Le nom doit contenir au moins 2 caractères";
                        isValid = false;
                    } else if (value.length > 100) {
                        message = "❌ Le nom ne peut pas dépasser 100 caractères";
                        isValid = false;
                    }
                    break;
                    
                case 'prenom_donateur':
                    if (!value) {
                        message = "❌ Le prénom du donateur est obligatoire";
                        isValid = false;
                    } else if (value.length < 2) {
                        message = "❌ Le prénom doit contenir au moins 2 caractères";
                        isValid = false;
                    } else if (value.length > 100) {
                        message = "❌ Le prénom ne peut pas dépasser 100 caractères";
                        isValid = false;
                    }
                    break;
                    
                case 'montant':
                    const montant = parseFloat(value.replace(',', '.'));
                    if (!value) {
                        message = "❌ Le montant est obligatoire";
                        isValid = false;
                    } else if (isNaN(montant)) {
                        message = "❌ Veuillez entrer un montant valide";
                        isValid = false;
                    } else if (montant <= 0) {
                        message = "❌ Le montant doit être supérieur à 0€";
                        isValid = false;
                    } else if (montant > 1000000) {
                        message = "❌ Le montant ne peut pas dépasser 1,000,000€";
                        isValid = false;
                    }
                    break;
                    
                case 'dateDon':
                    if (!value) {
                        message = "❌ La date est obligatoire";
                        isValid = false;
                    } else {
                        const selectedDate = new Date(value + 'T00:00:00');
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        
                        if (selectedDate > today) {
                            message = "❌ La date ne peut pas être dans le futur";
                            isValid = false;
                        }
                    }
                    break;
                    
                case 'typeDon':
                    if (!value) {
                        message = "❌ Veuillez choisir un type de don";
                        isValid = false;
                    }
                    break;
                    
                case 'organisationId':
                    if (!value) {
                        message = "❌ Veuillez sélectionner une organisation";
                        isValid = false;
                    }
                    break;
            }
            
            if (!isValid) {
                field.classList.add('invalid');
                if (errorElement) {
                    errorElement.textContent = message;
                }
            } else {
                field.classList.add('valid');
            }
            
            return isValid;
        }

        // Rafraîchir les infos du donateur quand le nom/prénom changent
        const nomInput = document.getElementById('nomDonateur');
        const prenomInput = document.getElementById('prenomDonateur');
        
        function checkDonateurInfos() {
            const nom = nomInput.value.trim();
            const prenom = prenomInput.value.trim();
            
            if (nom.length > 2 && prenom.length > 2) {
                console.log('Donateur:', prenom, nom);
            }
        }
        
        if (nomInput && prenomInput) {
            nomInput.addEventListener('input', checkDonateurInfos);
            prenomInput.addEventListener('input', checkDonateurInfos);
        }
    });
  </script>
</body>
</html>