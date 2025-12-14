<?php
require_once __DIR__."/../../Controller/OrganisationController.php";
require_once __DIR__."/../../Controller/DonController.php";
require_once __DIR__."/../../Model/Don.php";
require_once __DIR__."/LanguageHelper.php";

$lang = LanguageHelper::getInstance();
$t = function($key, $params = []) use ($lang) { 
    return $lang->translate($key, $params); 
};
$formatMoney = function($amount) use ($lang) { 
  // Formater selon la langue (USD si EN, sinon EUR)
  return $lang->formatMoneyDisplay($amount); 
};

$currentLang = $lang->getCurrentLang();
$supportedLangs = $lang->getSupportedLanguages();
$currencyInfo = $lang->getCurrencyInfo();

$orgCtrl = new OrganisationController();
$donCtrl = new DonController();

$message = '';
$success = false;
$errors = [];
$selectedOrgId = isset($_GET['orgId']) ? intval($_GET['orgId']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['montant'])) {
    $montant = isset($_POST['montant']) ? floatval(str_replace(',', '.', $_POST['montant'])) : 0;
    $dateDon = $_POST['dateDon'] ?? '';
    $typeDon = $_POST['typeDon'] ?? '';
    $organisationId = isset($_POST['organisationId']) ? intval($_POST['organisationId']) : $selectedOrgId;
    $nomDonateur = $_POST['nom_donateur'] ?? '';
    $prenomDonateur = $_POST['prenom_donateur'] ?? '';
    // Email retiré
    
    // Validation côté serveur
    if (!isset($montant) || $montant <= 0) {
        $errors['montant'] = $t('error_amount_zero');
    }
    
    if (empty($dateDon) || !DateTime::createFromFormat('Y-m-d', $dateDon)) {
        $errors['dateDon'] = $t('error_invalid_date');
    } else {
        $selectedDate = new DateTime($dateDon);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        $selectedDate->setTime(0, 0, 0);
        
        if ($selectedDate != $today) {
            $errors['dateDon'] = $t('error_date_today') . " (" . $today->format('d/m/Y') . ")";
        }
    }
    
    if (empty($typeDon)) {
        $errors['typeDon'] = $t('error_donation_type');
    }
    
    if (empty($organisationId)) {
        $errors['organisationId'] = $t('error_select_org');
    }
    
    if (empty(trim($nomDonateur))) {
        $errors['nom_donateur'] = $t('error_donor_name');
    } else if (!preg_match('/^[a-zA-ZÀ-ÿ\s\-\']+$/u', $nomDonateur)) {
        $errors['nom_donateur'] = $t('error_invalid_name');
    } else if (strlen($nomDonateur) < 2) {
        $errors['nom_donateur'] = $t('error_name_min_length');
    } else if (strlen($nomDonateur) > 50) {
        $errors['nom_donateur'] = $t('error_name_max_length');
    }
    
    if (empty(trim($prenomDonateur))) {
        $errors['prenom_donateur'] = $t('error_donor_firstname');
    } else if (!preg_match('/^[a-zA-ZÀ-ÿ\s\-\']+$/u', $prenomDonateur)) {
        $errors['prenom_donateur'] = $t('error_invalid_firstname');
    } else if (strlen($prenomDonateur) < 2) {
        $errors['prenom_donateur'] = $t('error_firstname_min_length');
    } else if (strlen($prenomDonateur) > 50) {
        $errors['prenom_donateur'] = $t('error_firstname_max_length');
    }    
    if (empty($errors)) {
      try {
        // Convertir en EUR si la langue est anglaise (saisie en USD)
        $montantEnEUR = $lang->convertToEUR($montant);
            
        $don = new Don(
          null,
          $montantEnEUR,
          new DateTime($dateDon),
          $typeDon,
          $organisationId,
          null, 
          $nomDonateur,
          $prenomDonateur
        );
            
            $validationErrors = $donCtrl->validateDon($don);
            if (empty($validationErrors)) {
                if ($donCtrl->addDon($don)) {
                    $message = $t('donation_success');
                    $success = true;
                    $_POST = [];
                } else {
                    $message = $t('donation_error');
                }
            } else {
                $message = $t('validation_errors') . "<br>" . implode("<br>", $validationErrors);
            }
        } catch (Exception $e) {
            $message = "❌ Erreur: " . $e->getMessage();
        }
    } else {
        $message = $t('please_correct_errors');
    }
}

$selectedOrg = null;
if ($selectedOrgId) {
    $organisations = $orgCtrl->listOrganisations();
    foreach ($organisations as $org) {
        if ($org['id'] === $selectedOrgId) {
            $selectedOrg = $org;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
  <meta charset="UTF-8">
  <title><?= $t('site_title') ?> - <?= $t('make_donation') ?></title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    :root {
      --ma-bg: #160820;
      --ma-card: rgba(8,22,36,0.95);
      --ma-border: rgba(255,255,255,0.10);
      --ma-accent: #ff4df0;
      --ma-accent-soft: #b01ba5;
      --ma-warning: #ffca5f;
      --ma-danger: #ff4b5c;
      --ma-primary-glow: rgba(255,77,240,0.6);
      --ma-secondary-glow: rgba(123,47,247,0.4);
      --ma-success: #4cff4c;
      --ma-glass: rgba(255,255,255,0.05);
      --ma-dark: #0a0515;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Roboto', sans-serif;
      /* Même arrière-plan que les autres pages */
      background: 
          url('/projet-dons/img/slider-bg-1.jpg') center/cover no-repeat fixed,
          radial-gradient(
              ellipse at top, 
              rgba(77, 27, 125, 0.85) 0%, 
              rgba(42, 15, 74, 0.88) 25%, 
              rgba(22, 8, 32, 0.92) 50%, 
              rgba(10, 5, 21, 0.95) 100%
          );
      background-blend-mode: multiply;
      background-attachment: fixed;
      color: #fff;
      line-height: 1.6;
      overflow-x: hidden;
      min-height: 100vh;
    }

    .language-selector {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1100;
    }
    
    .language-dropdown {
      position: relative;
      display: inline-block;
    }
    
    .current-language {
      background: rgba(8,22,36,0.9);
      border: 1px solid rgba(255,77,240,0.3);
      color: white;
      padding: 10px 20px;
      border-radius: 25px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      transition: all 0.3s;
      min-width: 160px;
    }
    
    .current-language:hover {
      background: rgba(255,77,240,0.1);
      transform: translateY(-2px);
    }
    
    .language-flag {
      font-size: 1.2rem;
    }
    
    .language-options {
      position: absolute;
      top: 100%;
      right: 0;
      margin-top: 10px;
      background: rgba(8,22,36,0.95);
      border: 1px solid rgba(255,77,240,0.3);
      border-radius: 15px;
      padding: 10px;
      min-width: 180px;
      display: none;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      backdrop-filter: blur(10px);
    }
    
    .language-option {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 15px;
      color: white;
      text-decoration: none;
      border-radius: 10px;
      transition: all 0.3s;
    }
    
    .language-option:hover {
      background: rgba(255,77,240,0.15);
    }
    
    .language-option.active {
      background: linear-gradient(135deg, var(--ma-accent), var(--ma-accent-soft));
      color: white;
      font-weight: bold;
    }
    
    .currency-indicator {
      position: fixed;
      top: 70px;
      right: 20px;
      z-index: 1100;
      background: rgba(8,22,36,0.8);
      padding: 8px 15px;
      border-radius: 20px;
      border: 1px solid rgba(255,77,240,0.3);
      color: var(--ma-success);
      font-weight: 600;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    header {
      background: rgba(8,22,36,0.95);
      backdrop-filter: blur(10px);
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 0 20px rgba(176,27,165,0.3);
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      left: 0;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    header h1 {
      font-size: 1.5rem;
      background: linear-gradient(135deg, #ff4df0, #ffb8ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin: 0;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    nav {
      display: flex;
      align-items: center;
      gap: 2rem;
      justify-content: center;
      flex: 1;
      margin-left: -300px;
    }

    nav a {
      color: #e1d7ff;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.95rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    nav a:hover { 
      color: var(--ma-accent);
      transform: translateY(-2px);
      text-shadow: 0 0 15px rgba(255,77,240,0.6);
    }

    nav a[href="stats-live.php"] {
      color: var(--ma-accent) !important;
      font-weight: 700;
      position: relative;
    }

    nav a[href="stats-live.php"]::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 100%;
      height: 2px;
      background: linear-gradient(90deg, var(--ma-accent), transparent);
      border-radius: 1px;
    }

    section#donation {
      padding: 140px 20px 80px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: 
        radial-gradient(circle at 50% 30%, rgba(255,77,240,0.1), transparent 50%),
        radial-gradient(circle at 50% 70%, rgba(123,47,247,0.1), transparent 50%);
      position: relative;
      overflow: hidden;
    }

    .donation-container {
      max-width: 600px;
      width: 100%;
      margin: 0 auto;
    }

    .donation-card {
      background: linear-gradient(145deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
      border-radius: 30px;
      padding: 50px 40px;
      border: 1px solid var(--ma-border);
      backdrop-filter: blur(20px);
      box-shadow: 
        0 30px 80px rgba(0,0,0,0.7),
        inset 0 1px 0 rgba(255,255,255,0.1),
        0 0 0 1px rgba(255,255,255,0.05);
      position: relative;
      overflow: hidden;
    }

    .donation-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, 
        var(--ma-accent) 0%, 
        #7b2ff7 50%, 
        var(--ma-accent) 100%);
      background-size: 200% 100%;
      animation: shimmer 3s infinite linear;
    }

    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }

    .form-header {
      text-align: center;
      margin-bottom: 40px;
      position: relative;
      z-index: 1;
    }

    .form-header h2 {
      font-size: 2.8rem;
      background: linear-gradient(135deg, #ff4df0, #ffb8ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-weight: 900;
      margin-bottom: 10px;
      letter-spacing: -0.5px;
    }

    .form-header p {
      color: #e1d7ff;
      opacity: 0.8;
      font-size: 1.1rem;
    }

    .org-badge {
      display: inline-block;
      background: linear-gradient(135deg, rgba(176,27,165,0.2), rgba(123,47,247,0.1));
      color: var(--ma-accent);
      padding: 12px 25px;
      border-radius: 50px;
      font-weight: 600;
      margin-top: 15px;
      border: 1px solid rgba(255,77,240,0.3);
      backdrop-filter: blur(5px);
    }

    .alert-modern {
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      gap: 15px;
      backdrop-filter: blur(10px);
      animation: slideIn 0.5s ease-out;
      position: relative;
      z-index: 1;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .alert-success {
      background: rgba(76, 255, 76, 0.1);
      border: 1px solid var(--ma-success);
      color: var(--ma-success);
    }

    .alert-error {
      background: rgba(255, 77, 92, 0.1);
      border: 1px solid var(--ma-danger);
      color: var(--ma-danger);
    }

    .alert-icon {
      font-size: 1.5rem;
      flex-shrink: 0;
    }

    .form-group-modern {
      margin-bottom: 30px;
      position: relative;
      z-index: 1;
    }

    .form-label {
      display: block;
      margin-bottom: 12px;
      color: #ffb8ff;
      font-weight: 600;
      font-size: 0.95rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .input-with-icon {
      position: relative;
    }

    .form-input-modern {
      width: 100%;
      padding: 18px 20px 18px 55px;
      border-radius: 15px;
      border: 2px solid rgba(255,255,255,0.15);
      background: rgba(0, 0, 0, 0.3);
      color: #fff;
      font-size: 1rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
      font-family: inherit;
    }

    .form-input-modern:focus {
      outline: none;
      border-color: var(--ma-accent);
      background: rgba(0, 0, 0, 0.4);
      box-shadow: 
        0 0 0 3px rgba(255,77,240,0.1),
        0 10px 30px rgba(0,0,0,0.3);
    }

    .form-input-modern.error {
      border-color: var(--ma-danger) !important;
      background: rgba(255, 77, 92, 0.1) !important;
      animation: shake 0.5s ease;
    }

    .form-input-modern.valid {
      border-color: var(--ma-success) !important;
      background: rgba(76, 255, 76, 0.1) !important;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
      20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .input-icon {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--ma-accent);
      font-size: 1.2rem;
      z-index: 1;
    }

    .quick-amounts {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-top: 15px;
    }

    .amount-btn {
      padding: 15px;
      border: 2px solid rgba(255,255,255,0.15);
      background: rgba(0,0,0,0.2);
      color: #fff;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 700;
      font-size: 1.2rem;
      position: relative;
      overflow: hidden;
    }

    .amount-btn:hover {
      border-color: var(--ma-accent);
      background: rgba(255,77,240,0.1);
      transform: translateY(-2px);
    }

    .amount-btn.active {
      background: linear-gradient(135deg, var(--ma-accent), var(--ma-accent-soft));
      border-color: var(--ma-accent);
      color: white;
      box-shadow: 0 8px 20px rgba(255,77,240,0.3);
    }

    .amount-btn::after {
      content: attr(data-currency);
      position: absolute;
      top: 5px;
      right: 8px;
      font-size: 0.7rem;
      opacity: 0.7;
    }

    .custom-amount {
      grid-column: span 3;
      position: relative;
      margin-top: 10px;
    }

    .custom-amount .input-icon {
      left: 15px;
    }

    .custom-amount input {
      padding-left: 50px;
      font-size: 1.1rem;
      font-weight: 600;
    }

    .date-info-modern {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
      color: var(--ma-success);
      font-size: 0.9rem;
      font-weight: 500;
    }

    .date-info-modern i {
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 0.7; }
      50% { opacity: 1; }
    }

    /* CORRECTION ICI : Sélecteur avec texte noir */
    .select-modern {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23ff4df0' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 20px center;
      background-size: 16px;
      color: #fdfdfdff !important; /* Texte en noir */
    }

    /* Styles spécifiques pour les options du select */
    .select-modern option {
      color: #000000; /* Texte des options en noir */
      background-color: #ffffff; /* Fond blanc pour les options */
      padding: 10px;
    }

    /* Style pour le select ouvert */
    .select-modern:focus option {
      color: #000000;
      background-color: #ffffff;
    }

    /* S'assurer que la valeur sélectionnée est visible */
    .select-modern option:checked,
    .select-modern option:selected {
      color: #000000 !important;
      background-color: #f0f0f0 !important;
    }

    .form-actions {
      display: flex;
      gap: 20px;
      margin-top: 40px;
    }

    .btn-submit-modern {
      flex: 1;
      background: linear-gradient(135deg, var(--ma-accent), var(--ma-accent-soft));
      color: white;
      border: none;
      padding: 22px;
      border-radius: 15px;
      font-size: 1.2rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      position: relative;
      overflow: hidden;
    }

    .btn-submit-modern:hover:not(:disabled) {
      transform: translateY(-3px);
      box-shadow: 
        0 20px 40px rgba(255,77,240,0.4),
        0 0 30px rgba(255,77,240,0.3);
    }

    .btn-submit-modern:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .btn-back-modern {
      padding: 22px 30px;
      background: var(--ma-glass);
      border: 2px solid var(--ma-accent);
      color: var(--ma-accent);
      border-radius: 15px;
      text-decoration: none;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-back-modern:hover {
      background: linear-gradient(135deg, var(--ma-accent), var(--ma-accent-soft));
      color: white;
      transform: translateY(-2px);
    }

    .error-message {
      color: var(--ma-danger);
      font-size: 0.85rem;
      margin-top: 8px;
      display: none;
      align-items: center;
      gap: 8px;
      font-weight: 500;
    }

    .error-message.show {
      display: flex;
      animation: slideIn 0.3s ease-out;
    }

    .success-message {
      color: var(--ma-success);
      font-size: 0.85rem;
      margin-top: 8px;
      display: none;
      align-items: center;
      gap: 8px;
      font-weight: 500;
    }

    .success-message.show {
      display: flex;
      animation: slideIn 0.3s ease-out;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .char-counter {
      text-align: right;
      font-size: 0.75rem;
      color: #999;
      margin-top: 5px;
      display: none;
    }

    .char-counter.show {
      display: block;
    }

    .char-counter.warning {
      color: var(--ma-warning);
    }

    .char-counter.error {
      color: var(--ma-danger);
    }

    .required-star {
      color: var(--ma-danger);
      margin-left: 5px;
    }

    .validation-summary {
      background: rgba(255, 77, 92, 0.1);
      border: 1px solid var(--ma-danger);
      color: var(--ma-danger);
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      display: none;
    }

    .validation-summary.show {
      display: block;
      animation: slideIn 0.5s ease-out;
    }

    .validation-summary h4 {
      margin: 0 0 10px 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .validation-summary ul {
      margin: 0;
      padding-left: 20px;
    }

    .validation-summary li {
      margin-bottom: 5px;
    }

    footer {
      background: linear-gradient(180deg, transparent, rgba(8,22,36,0.9));
      text-align: center;
      padding: 40px 20px;
      border-top: 1px solid rgba(255,255,255,0.1);
      position: relative;
    }
    
    footer p {
      margin: 0;
      color: #999;
      font-size: 0.9rem;
      line-height: 1.8;
    }

    @media (max-width: 768px) {
      .language-selector {
        top: 10px;
        right: 10px;
      }
      
      .currency-indicator {
        top: 60px;
        right: 10px;
        font-size: 0.8rem;
      }
      
      header {
        flex-direction: column;
        gap: 15px;
        padding: 15px 20px;
      }
      
      nav {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-left: 0;
      }
      
      section#donation {
        padding: 120px 15px 60px;
      }
      
      .donation-card {
        padding: 30px 20px;
      }
      
      .form-header h2 {
        font-size: 2.2rem;
      }
      
      .form-actions {
        flex-direction: column;
      }
      
      .quick-amounts {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .custom-amount {
        grid-column: span 2;
      }
      
      .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .btn-submit-modern,
      .btn-back-modern {
        padding: 18px;
      }
    }
    
    @media (max-width: 480px) {
      .form-header h2 {
        font-size: 1.8rem;
      }
      
      .donation-card {
        padding: 25px 15px;
      }
      
      .form-input-modern {
        padding: 16px 20px 16px 50px;
      }
      
      .input-icon {
        left: 15px;
      }
      
      .amount-btn {
        padding: 12px;
        font-size: 1rem;
      }
      
      .btn-submit-modern {
        font-size: 1.1rem;
      }
    }
  </style>
  <!-- Stripe.js -->
  <script src="https://js.stripe.com/v3/"></script>
</head>

<body>

  <div class="language-selector">
    <div class="language-dropdown" id="languageDropdown">
      <div class="current-language" onclick="toggleLanguageMenu()">
        <span class="language-flag"><?= $supportedLangs[$currentLang]['flag'] ?? '🌐' ?></span>
        <span><?= $supportedLangs[$currentLang]['name'] ?? 'Language' ?></span>
        <i class="bi bi-chevron-down" style="margin-left: auto;"></i>
      </div>
      
      <div class="language-options" id="languageOptions">
        <?php foreach ($supportedLangs as $code => $langInfo): ?>
          <a href="?lang=<?= $code ?>" 
             class="language-option <?= $currentLang === $code ? 'active' : '' ?>"
             onclick="changeLanguage('<?= $code ?>')">
            <span style="font-size: 1.2rem;"><?= $langInfo['flag'] ?></span>
            <div>
              <div style="font-weight: 600;"><?= $langInfo['name'] ?></div>
              <div style="font-size: 0.8rem; opacity: 0.8;"><?= $langInfo['native'] ?></div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    
    <div class="currency-indicator">
      <i class="bi bi-currency-exchange"></i>
      <?= $currencyInfo['code'] ?> (<?= $currencyInfo['symbol'] ?>)
    </div>
  </div>

  <header>
    <h1>🎮 <?= $t('site_title') ?></h1>
    <nav>
      <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i> <?= $t('home') ?>
      </a>
      <a href="index.php#organisations">
        <i class="bi bi-people-fill"></i> <?= $t('our_partners') ?>
      </a>
      <a href="stats-live.php" 
         class="<?= basename($_SERVER['PHP_SELF']) == 'stats-live.php' ? 'active' : '' ?>"
         style="color: #ff4df0;">
        <i class="bi bi-graph-up-arrow"></i> <?= $t('live_stats') ?>
      </a>
      <a href="../../backoffice.php">
        <i class="bi bi-shield-lock"></i> <?= $t('admin_area') ?>
      </a>
    </nav>
  </header>

  <section id="donation">
    <div class="donation-container">
      <div class="donation-card">
        <div class="form-header">
          <h2><?= $t('make_donation') ?></h2>
          <p><?= $t('make_difference') ?></p>
          
          <?php if ($selectedOrg): ?>
            <div class="org-badge">
              <i class="bi bi-heart-fill"></i> 
              <?= htmlspecialchars($selectedOrg['nom']) ?>
            </div>
          <?php elseif (!$selectedOrgId): ?>
            <div class="alert-modern alert-error">
              <i class="bi bi-exclamation-triangle-fill alert-icon"></i>
              <div><?= $t('error_no_org_selected') ?></div>
            </div>
          <?php endif; ?>
        </div>
        
        <?php if ($message): ?>
          <div class="alert-modern <?= $success ? 'alert-success' : 'alert-error' ?>">
            <i class="bi <?= $success ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> alert-icon"></i>
            <div><?= $message ?></div>
          </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
          <div class="form-actions">
            <a href="index.php" class="btn-submit-modern" style="text-decoration: none; text-align: center;">
              <i class="bi bi-arrow-left"></i> <?= $t('back_to_home') ?>
            </a>
          </div>
        <?php elseif ($selectedOrgId): ?>
          <!-- Résumé des erreurs de validation -->
          <div class="validation-summary" id="validationSummary">
            <h4><i class="bi bi-exclamation-triangle-fill"></i> Veuillez corriger les erreurs suivantes :</h4>
            <ul id="validationErrorsList"></ul>
          </div>
          
          <form method="POST" id="donForm" action="">
            <input type="hidden" name="organisationId" value="<?= $selectedOrgId ?>">
            
            <div class="form-row">
              <div class="form-group-modern">
                <label class="form-label">
                  <i class="bi bi-person"></i> <?= $t('donor_lastname') ?> <span class="required-star">*</span>
                </label>
                <div class="input-with-icon">
                  <i class="bi bi-person input-icon"></i>
                  <input type="text" 
                         name="nom_donateur" 
                         value="<?= htmlspecialchars($_POST['nom_donateur'] ?? '') ?>" 
                         placeholder="<?= $t('placeholder_lastname') ?>"
                         class="form-input-modern"
                         id="nomDonateur"
                         autocomplete="family-name">
                  <div class="char-counter" id="nomCounter">0/50</div>
                </div>
                <div class="error-message" id="nomError"></div>
              </div>
              
              <div class="form-group-modern">
                <label class="form-label">
                  <i class="bi bi-person"></i> <?= $t('donor_firstname') ?> <span class="required-star">*</span>
                </label>
                <div class="input-with-icon">
                  <i class="bi bi-person input-icon"></i>
                  <input type="text" 
                         name="prenom_donateur" 
                         value="<?= htmlspecialchars($_POST['prenom_donateur'] ?? '') ?>" 
                         placeholder="<?= $t('placeholder_firstname') ?>"
                         class="form-input-modern"
                         id="prenomDonateur"
                         autocomplete="given-name">
                  <div class="char-counter" id="prenomCounter">0/50</div>
                </div>
                <div class="error-message" id="prenomError"></div>
              </div>
            </div>
            
            <!-- CHAMP EMAIL RETIRÉ -->
            
            <div class="form-group-modern">
              <label class="form-label">
                <i class="bi bi-cash-stack"></i> <?= $t('donation_amount') ?> (<?= $currencyInfo['symbol'] ?>) <span class="required-star">*</span>
              </label>
              
              <div class="quick-amounts">
                <button type="button" class="amount-btn" data-amount="10" data-currency="<?= $currencyInfo['symbol'] ?>">10</button>
                <button type="button" class="amount-btn" data-amount="25" data-currency="<?= $currencyInfo['symbol'] ?>">25</button>
                <button type="button" class="amount-btn" data-amount="50" data-currency="<?= $currencyInfo['symbol'] ?>">50</button>
                <button type="button" class="amount-btn" data-amount="100" data-currency="<?= $currencyInfo['symbol'] ?>">100</button>
                <button type="button" class="amount-btn" data-amount="250" data-currency="<?= $currencyInfo['symbol'] ?>">250</button>
                <button type="button" class="amount-btn" data-amount="500" data-currency="<?= $currencyInfo['symbol'] ?>">500</button>
                
                <div class="custom-amount">
                  <div class="input-with-icon">
                    <i class="bi bi-pencil input-icon"></i>
                    <input type="text" 
                           name="montant" 
                           value="<?= htmlspecialchars($_POST['montant'] ?? '') ?>" 
                           placeholder="<?= $t('custom_amount') ?>"
                           class="form-input-modern"
                           id="montant"
                           autocomplete="transaction-amount">
                  </div>
                </div>
              </div>
              <div class="error-message" id="montantError"></div>
            </div>
            
            <div class="form-row">
              <div class="form-group-modern">
                <label class="form-label">
                  <i class="bi bi-calendar-event"></i> <?= $t('donation_date') ?> <span class="required-star">*</span>
                </label>
                <div class="input-with-icon">
                  <i class="bi bi-calendar-event input-icon"></i>
                  <input type="text" 
                         name="dateDon" 
                         value="<?= htmlspecialchars($_POST['dateDon'] ?? '') ?>"
                         class="form-input-modern date-locked"
                         id="dateDon"
                         readonly>
                </div>
                <div class="date-info-modern">
                  <i class="bi bi-info-circle"></i>
                  <span><?= $t('date_auto_today') ?></span>
                </div>
                <div class="error-message" id="dateDonError"></div>
              </div>
              
              <div class="form-group-modern">
                <label class="form-label">
                  <i class="bi bi-tag"></i> <?= $t('donation_type') ?> <span class="required-star">*</span>
                </label>
                <div class="input-with-icon">
                  <i class="bi bi-tag input-icon"></i>
                  <select name="typeDon" 
                          class="form-input-modern select-modern" 
                          id="typeDon">
                    <option value="">-- <?= $t('select_type') ?> --</option>
                    <option value="Monétaire" <?= ($_POST['typeDon'] ?? '') == 'Monétaire' ? 'selected' : '' ?>><?= $t('monetary') ?></option>
                    <option value="Matériel" <?= ($_POST['typeDon'] ?? '') == 'Matériel' ? 'selected' : '' ?>><?= $t('material') ?></option>
                  </select>
                </div>
                <div class="error-message" id="typeDonError"></div>
              </div>
            </div>

            <!-- Payment Section -->
            <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid rgba(255,77,240,0.2);">
              <h3 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem;">
                <i class="bi bi-credit-card"></i> <?= $t('payment_method') ?? 'Méthode de paiement' ?>
              </h3>
              
              <div style="background: linear-gradient(135deg, rgba(139,92,246,0.1), rgba(168,85,247,0.1)); 
                          padding: 20px; 
                          border-radius: 12px; 
                          border: 1px solid rgba(168,85,247,0.3);
                          margin-bottom: 20px;">
                <div id="card-element" style="color: #fff;"></div>
                <div id="card-errors" style="color: #ff4b5c; margin-top: 10px; font-size: 0.9rem;"></div>
              </div>
            </div>
            
            <div class="form-actions">
              <a href="index.php" class="btn-back-modern">
                <i class="bi bi-arrow-left"></i> <?= $t('back_home') ?>
              </a>
              <button type="submit" class="btn-submit-modern" id="submitBtn">
                <i class="bi bi-credit-card"></i>
                <span><?= $t('donate_by_card') ?? 'Donner par carte' ?></span>
              </button>
            </div>
            
            <p style="color: #ccc; font-size: 0.8rem; margin-top: 20px; text-align: center; opacity: 0.7;">
              <i class="bi bi-lock-fill"></i> <?= $t('secure_payment') ?> • 
              <i class="bi bi-shield-check"></i> <?= $t('data_protected') ?>
            </p>
          </form>
        <?php else: ?>
          <div class="form-actions">
            <a href="index.php" class="btn-submit-modern" style="text-decoration: none; text-align: center;">
              <i class="bi bi-arrow-left"></i> <?= $t('back_to_home_to_select') ?>
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <footer>
    <p>
      <?= $t('copyright') ?>
      <br><small><?= $t('tagline') ?></small>
      <br><small style="opacity: 0.7;">
        <?= $t('current_language') ?>: <?= $supportedLangs[$currentLang]['name'] ?> | 
        <?= $t('currency') ?>: <?= $currencyInfo['code'] ?>
      </small>
    </p>
  </footer>

  <script>
    // Fonctions utilitaires
    function toggleLanguageMenu() {
      const options = document.getElementById('languageOptions');
      options.style.display = options.style.display === 'block' ? 'none' : 'block';
    }
    
    function changeLanguage(langCode) {
      localStorage.setItem('preferred_language', langCode);
      const url = new URL(window.location);
      url.searchParams.set('lang', langCode);
      window.location.href = url.toString();
    }
    
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('languageDropdown');
      if (!dropdown.contains(event.target)) {
        document.getElementById('languageOptions').style.display = 'none';
      }
    });

    // Système de validation avec affichage uniquement à la soumission
    class FormValidator {
      constructor() {
        this.validatedOnce = false; // Flag pour savoir si la validation a déjà été faite
        this.errors = {};
        this.init();
      }
      
      init() {
        // Récupérer la date du jour
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        this.todayStr = `${year}-${month}-${day}`;
        
        // Initialiser les champs
        this.initFields();
        this.setTodayDate();
        this.initAmountButtons();
        this.initFormSubmit();
        this.initRealTimeValidation();
      }
      
      initFields() {
        this.fields = {
          nomDonateur: document.getElementById('nomDonateur'),
          prenomDonateur: document.getElementById('prenomDonateur'),
          montant: document.getElementById('montant'),
          dateDon: document.getElementById('dateDon'),
          typeDon: document.getElementById('typeDon')
        };
        
        // Compteurs de caractères
        this.counters = {
          nom: document.getElementById('nomCounter'),
          prenom: document.getElementById('prenomCounter')
        };
        
        // Éléments d'erreur
        this.errorElements = {
          nom: document.getElementById('nomError'),
          prenom: document.getElementById('prenomError'),
          montant: document.getElementById('montantError'),
          dateDon: document.getElementById('dateDonError'),
          typeDon: document.getElementById('typeDonError')
        };
        
        // Résumé des validations
        this.validationSummary = document.getElementById('validationSummary');
        this.validationErrorsList = document.getElementById('validationErrorsList');
      }
      
      initRealTimeValidation() {
        // Seulement le formatage en temps réel, pas les messages d'erreur
        this.fields.nomDonateur.addEventListener('input', (e) => {
          this.updateCharCounter(e.target, this.counters.nom);
          this.formatNameField(e.target);
        });
        
        this.fields.prenomDonateur.addEventListener('input', (e) => {
          this.updateCharCounter(e.target, this.counters.prenom);
          this.formatNameField(e.target);
        });
        
        this.fields.montant.addEventListener('input', (e) => {
          this.formatAmountField(e.target);
        });
        
        // Nettoyage des noms quand on quitte le champ
        this.fields.nomDonateur.addEventListener('blur', (e) => {
          this.cleanNameField(e.target);
        });
        
        this.fields.prenomDonateur.addEventListener('blur', (e) => {
          this.cleanNameField(e.target);
        });
      }
      
      setTodayDate() {
        if (this.fields.dateDon) {
          this.fields.dateDon.value = this.todayStr;
          this.fields.dateDon.addEventListener('keydown', (e) => e.preventDefault());
          this.fields.dateDon.addEventListener('focus', (e) => e.target.blur());
        }
      }
      
      initAmountButtons() {
        const amountBtns = document.querySelectorAll('.amount-btn');
        amountBtns.forEach(btn => {
          btn.addEventListener('click', () => {
            amountBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const amount = btn.getAttribute('data-amount');
            this.fields.montant.value = amount;
            this.formatAmountField(this.fields.montant);
          });
        });
        
        this.fields.montant.addEventListener('input', () => {
          amountBtns.forEach(b => b.classList.remove('active'));
        });
      }
      
      initFormSubmit() {
        const form = document.getElementById('donForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
          form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.validatedOnce = true;
            
            // Valider tous les champs
            const isValid = this.validateAll();
            
            if (isValid) {
              // Désactiver le bouton pour éviter la double soumission
              if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> ${this.getTranslation('processing')}...`;
              }
              
              // Formater les données avant envoi
              this.formatDataForSubmit();
              
              // Soumettre le formulaire
              setTimeout(() => {
                form.submit();
              }, 500);
            } else {
              // Afficher le résumé des erreurs
              this.showValidationSummary();
              
              // Scroll vers le haut du formulaire
              this.validationSummary.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
              });
            }
          });
        }
      }
      
      updateCharCounter(input, counter) {
        if (!input || !counter) return;
        
        const length = input.value.length;
        const maxLength = 50;
        counter.textContent = `${length}/${maxLength}`;
        
        // Montrer le compteur seulement si on a commencé à taper
        if (length > 0) {
          counter.classList.add('show');
        } else {
          counter.classList.remove('show');
        }
        
        // Changer la couleur
        counter.className = 'char-counter show';
        if (length === 0) {
          // Rien
        } else if (length < 2) {
          counter.classList.add('error');
        } else if (length > maxLength - 10) {
          counter.classList.add('warning');
        }
      }
      
      formatNameField(input) {
        // Empêcher les caractères non autorisés
        const value = input.value;
        const lastChar = value.slice(-1);
        const pattern = /^[a-zA-ZÀ-ÿ\s\-\']$/;
        
        if (value.length > 0 && !pattern.test(lastChar) && lastChar !== ' ') {
          input.value = value.slice(0, -1);
        }
      }
      
      cleanNameField(input) {
        if (!input) return;
        
        // Supprimer les espaces multiples et les espaces au début/fin
        input.value = input.value.replace(/\s+/g, ' ').trim();
        
        // Mettre la première lettre en majuscule
        if (input.value.length > 0) {
          input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1).toLowerCase();
        }
      }
      
      formatAmountField(input) {
        let value = input.value;
        
        // Remplacer la virgule par un point
        value = value.replace(',', '.');
        
        // Supprimer tout sauf les chiffres et le point
        value = value.replace(/[^\d.]/g, '');
        
        // S'assurer qu'il n'y a qu'un seul point décimal
        const parts = value.split('.');
        if (parts.length > 2) {
          value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        // Limiter à 2 décimales
        if (parts.length === 2) {
          value = parts[0] + '.' + parts[1].slice(0, 2);
        }
        
        input.value = value;
      }
      
      validateAll() {
        // Réinitialiser les erreurs
        this.errors = {};
        
        // Cacher tous les messages d'erreur précédents
        this.hideAllErrorMessages();
        
        // Valider chaque champ
        const validations = [
          this.validateField('nomDonateur', this.validateName.bind(this)),
          this.validateField('prenomDonateur', this.validateName.bind(this)),
          this.validateField('montant', this.validateAmount.bind(this)),
          this.validateField('typeDon', this.validateType.bind(this))
        ];
        
        return validations.every(valid => valid === true);
      }
      
      validateField(fieldName, validationFunction) {
        const field = this.fields[fieldName];
        const value = field.value.trim();
        
        // Réinitialiser l'état du champ
        field.classList.remove('error', 'valid');
        
        // Valider le champ
        return validationFunction(field, value, fieldName);
      }
      
      validateName(field, value, fieldName) {
        const fieldType = fieldName === 'nomDonateur' ? 'nom' : 'prenom';
        
        if (value.length === 0) {
          this.errors[fieldName] = `${fieldType === 'nom' ? 'Le nom' : 'Le prénom'} est requis`;
          this.showFieldError(field, fieldName, this.errors[fieldName]);
          return false;
        }
        
        if (value.length < 2) {
          this.errors[fieldName] = `${fieldType === 'nom' ? 'Le nom' : 'Le prénom'} doit contenir au moins 2 caractères`;
          this.showFieldError(field, fieldName, this.errors[fieldName]);
          return false;
        }
        
        if (value.length > 50) {
          this.errors[fieldName] = `${fieldType === 'nom' ? 'Le nom' : 'Le prénom'} ne peut pas dépasser 50 caractères`;
          this.showFieldError(field, fieldName, this.errors[fieldName]);
          return false;
        }
        
        const pattern = /^[a-zA-ZÀ-ÿ\s\-\']+$/;
        if (!pattern.test(value)) {
          this.errors[fieldName] = `${fieldType === 'nom' ? 'Le nom' : 'Le prénom'} ne peut contenir que des lettres, espaces, tirets et apostrophes`;
          this.showFieldError(field, fieldName, this.errors[fieldName]);
          return false;
        }
        
        field.classList.add('valid');
        return true;
      }
      
      validateAmount(field, value) {
        if (value.length === 0) {
          this.errors.montant = 'Le montant est requis';
          this.showFieldError(field, 'montant', this.errors.montant);
          return false;
        }
        
        // Convertir en nombre
        const amount = parseFloat(value.replace(',', '.'));
        
        if (isNaN(amount)) {
          this.errors.montant = 'Veuillez entrer un montant valide';
          this.showFieldError(field, 'montant', this.errors.montant);
          return false;
        }
        
        if (amount <= 0) {
          this.errors.montant = 'Le montant doit être supérieur à 0';
          this.showFieldError(field, 'montant', this.errors.montant);
          return false;
        }
        
        if (amount > 1000000) {
          this.errors.montant = 'Le montant ne peut pas dépasser 1,000,000';
          this.showFieldError(field, 'montant', this.errors.montant);
          return false;
        }
        
        field.classList.add('valid');
        return true;
      }
      
      validateType(field, value) {
        if (value === '') {
          this.errors.typeDon = 'Veuillez choisir un type de don';
          this.showFieldError(field, 'typeDon', this.errors.typeDon);
          return false;
        }
        
        field.classList.add('valid');
        return true;
      }
      
      showFieldError(field, fieldName, message) {
        field.classList.add('error');
        
        const errorElement = this.errorElements[fieldName.replace('Donateur', '').toLowerCase()];
        if (errorElement) {
          errorElement.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${message}`;
          errorElement.classList.add('show');
        }
      }
      
      hideAllErrorMessages() {
        // Cacher tous les messages d'erreur
        Object.values(this.errorElements).forEach(element => {
          if (element) {
            element.classList.remove('show');
            element.innerHTML = '';
          }
        });
        
        // Cacher le résumé des validations
        if (this.validationSummary) {
          this.validationSummary.classList.remove('show');
        }
      }
      
      showValidationSummary() {
        if (!this.validationSummary || !this.validationErrorsList) return;
        
        // Vider la liste actuelle
        this.validationErrorsList.innerHTML = '';
        
        // Ajouter les erreurs à la liste
        Object.values(this.errors).forEach(error => {
          if (error) {
            const li = document.createElement('li');
            li.textContent = error;
            this.validationErrorsList.appendChild(li);
          }
        });
        
        // Afficher le résumé
        this.validationSummary.classList.add('show');
        
        // Afficher aussi les messages d'erreur individuels
        Object.keys(this.errors).forEach(fieldName => {
          if (this.errors[fieldName]) {
            const field = this.fields[fieldName];
            const errorKey = fieldName.replace('Donateur', '').toLowerCase();
            const errorElement = this.errorElements[errorKey];
            
            if (field && errorElement) {
              field.classList.add('error');
              errorElement.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${this.errors[fieldName]}`;
              errorElement.classList.add('show');
            }
          }
        });
      }
      
      formatDataForSubmit() {
        // Formater le montant avec point comme séparateur décimal
        if (this.fields.montant.value) {
          const amount = parseFloat(this.fields.montant.value.replace(',', '.'));
          if (!isNaN(amount)) {
            this.fields.montant.value = amount.toFixed(2);
          }
        }
        
        // S'assurer que la date est correcte
        if (this.fields.dateDon) {
          this.fields.dateDon.value = this.todayStr;
        }
      }
      
      getTranslation(key) {
        const translations = {
          'processing': 'En cours'
        };
        
        return translations[key] || key;
      }
    }
    
    // Initialiser le validateur quand le DOM est chargé
    document.addEventListener('DOMContentLoaded', () => {
      new FormValidator();
      initStripePayment();
      
      // Animation de fond pour le header au scroll
      window.addEventListener('scroll', function() {
        const header = document.querySelector('header');
        if (window.scrollY > 50) {
          header.style.background = 'rgba(8,22,36,0.98)';
          header.style.backdropFilter = 'blur(15px)';
        } else {
          header.style.background = 'rgba(8,22,36,0.95)';
          header.style.backdropFilter = 'blur(10px)';
        }
      });
      
      // Appliquer la langue sauvegardée
      const savedLang = localStorage.getItem('preferred_language');
      const currentLang = '<?= $currentLang ?>';
      const urlParams = new URLSearchParams(window.location.search);
      
      if (savedLang && savedLang !== currentLang && !urlParams.has('lang')) {
        changeLanguage(savedLang);
      }
    });

    // Stripe Payment Integration
    let stripe, cardElement;

    function initStripePayment() {
      // IMPORTANT: Remplacer par votre clé publique Stripe
      const publishableKey = 'pk_test_YOUR_KEY_HERE'; // À remplacer avec votre clé publique
      
      // Initialiser Stripe
      stripe = Stripe(publishableKey);
      const elements = stripe.elements();
      
      // Créer l'élément de carte
      cardElement = elements.create('card', {
        style: {
          base: {
            fontSize: '16px',
            color: '#fff',
            fontFamily: 'Roboto, sans-serif',
            '::placeholder': {
              color: 'rgba(255, 255, 255, 0.5)',
            },
          },
          invalid: {
            color: '#ff4b5c',
          },
        },
      });
      
      // Monter l'élément de carte
      cardElement.mount('#card-element');
      
      // Afficher les erreurs de la carte
      cardElement.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
          displayError.textContent = event.error.message;
        } else {
          displayError.textContent = '';
        }
      });
      
      // Gérer la soumission du formulaire de paiement
      const form = document.getElementById('donForm');
      if (form) {
        form.addEventListener('submit', handleFormSubmit);
      }
    }

    function handleFormSubmit(event) {
      // Ne pas soumettre le formulaire immédiatement
      // Le FormValidator va gérer la validation et appeler stripe si tout est bon
      const submitBtn = document.getElementById('submitBtn');
      if (submitBtn && submitBtn.dataset.processing === 'true') {
        event.preventDefault();
        return;
      }
    }

    // Intégrer Stripe au système de validation
    const originalFormValidatorInit = FormValidator.prototype.init;
    FormValidator.prototype.initFormSubmit = function() {
      const form = document.getElementById('donForm');
      const submitBtn = document.getElementById('submitBtn');
      
      if (form) {
        form.addEventListener('submit', (e) => {
          e.preventDefault();
          this.validatedOnce = true;
          
          // Valider tous les champs
          const isValid = this.validateAll();
          
          if (isValid) {
            // Procéder au paiement Stripe
            processStripePayment(form, submitBtn, this);
          } else {
            // Afficher le résumé des erreurs
            this.showValidationSummary();
            
            // Scroll vers le haut du formulaire
            this.validationSummary.scrollIntoView({ 
              behavior: 'smooth', 
              block: 'start' 
            });
          }
        });
      }
    };

    async function processStripePayment(form, submitBtn, validator) {
      // Désactiver le bouton
      submitBtn.disabled = true;
      submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> ${validator.getTranslation('processing')}...`;
      submitBtn.dataset.processing = 'true';
      
      try {
        // Créer un PaymentIntent côté serveur
        const response = await fetch('./process-payment.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            montant: parseFloat(document.getElementById('montant').value),
            devise: '<?= $currencyInfo['code'] ?>'
          })
        });
        
        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.error || 'Erreur serveur');
        }

        // Mode simulation (Stripe non installé) - soumettre directement
        if (data.skipPayment === true) {
          console.log('Mode simulation - Formulaire soumis directement');
          form.submit();
          return;
        }

        // Mode production - traiter avec Stripe
        if (!data.clientSecret) {
          throw new Error('Erreur serveur: clientSecret non reçu');
        }
        
        // Utiliser Stripe pour traiter le paiement
        const {paymentIntent, error} = await stripe.confirmCardPayment(
          data.clientSecret,
          {
            payment_method: {
              card: cardElement,
              billing_details: {
                name: `${document.getElementById('prenomDonateur').value} ${document.getElementById('nomDonateur').value}`
              }
            }
          }
        );
        
        if (error) {
          // Afficher l'erreur
          document.getElementById('card-errors').textContent = error.message;
          submitBtn.disabled = false;
          submitBtn.innerHTML = `<i class="bi bi-credit-card"></i> <span>${validator.getTranslation('donate_by_card') || 'Donner par carte'}</span>`;
          submitBtn.dataset.processing = 'false';
        } else if (paymentIntent.status === 'succeeded') {
          // Le paiement a réussi - soumettre le formulaire
          form.submit();
        }
      } catch (err) {
        console.error('Erreur:', err);
        document.getElementById('card-errors').textContent = err.message || 'Une erreur est survenue';
        submitBtn.disabled = false;
        submitBtn.innerHTML = `<i class="bi bi-credit-card"></i> <span>${validator.getTranslation('donate_by_card') || 'Donner par carte'}</span>`;
        submitBtn.dataset.processing = 'false';
      }
    }
  </script>

</body>
</html>