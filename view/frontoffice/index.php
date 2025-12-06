<?php
require_once __DIR__."/../../Controller/OrganisationController.php";
require_once __DIR__."/../../Controller/DonController.php";
// Inclure le helper de langue
require_once __DIR__."/LanguageHelper.php";

// Initialiser le gestionnaire de langue
$lang = LanguageHelper::getInstance();

// Fonctions raccourcis pour les templates
$t = function($key, $params = []) use ($lang) { 
    return $lang->translate($key, $params); 
};

$formatMoney = function($amount) use ($lang) { 
    // Toujours formater en euros
    return number_format($amount, 2) . ' €';
};

$plural = function($key, $count, $params = []) use ($lang) { 
    return $lang->plural($key, $count, $params); 
};

// Récupérer les informations courantes
$currentLang = $lang->getCurrentLang();
$supportedLangs = $lang->getSupportedLanguages();

// Forcer l'euro pour toutes les langues
$currencyInfo = [
    'code' => 'EUR',
    'symbol' => '€',
    'name' => 'Euro',
    'locale' => 'fr_FR'
];

// Contrôleurs
$orgCtrl = new OrganisationController();
$donCtrl = new DonController();
$organisations = $orgCtrl->listOrganisations();

// Définir les objectifs de collecte par organisation ID
$objectifsParOrganisation = [
    1 => 10000, // ID 1 : 10 000€
    2 => 5000,  // ID 2 : 5 000€
    3 => 15000, // ID 3 : 15 000€
    4 => 3000,  // ID 4 : 3 000€
    5 => 8000,  // ID 5 : 8 000€
    6 => 12000, // ID 6 : 12 000€
    7 => 6000,  // ID 7 : 6 000€
    8 => 20000, // ID 8 : 20 000€
    9 => 4000,  // ID 9 : 4 000€
    10 => 7000  // ID 10 : 7 000€
];
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
  <meta charset="UTF-8">
  <title><?= $t('site_title') ?> - <?= $t('home') ?></title>
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
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Roboto', sans-serif;
      background: radial-gradient(ellipse at top, #4d1b7d 0%, #2a0f4a 25%, #160820 50%, #0a0515 100%);
      background-attachment: fixed;
      color: #fff;
      line-height: 1.6;
      overflow-x: hidden;
    }

    /* --- Sélecteur de Langue --- */
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

    /* ----- HEADER ----- */
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

    /* Navigation */
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

    /* Style spécifique pour le lien Live Stats */
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

    /* ----- HERO SECTION ----- */
    .hero {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      background: url('/projet-dons/img/slider-bg-1.jpg') center/cover no-repeat;
      position: relative;
      overflow: hidden;
      margin-top: 0;
    }
    
    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(8,22,36,0.92), rgba(80,23,85,0.78), rgba(22,8,32,0.95));
    }
    
    .hero::after {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 30% 50%, rgba(255,77,240,0.15), transparent 50%),
                  radial-gradient(circle at 70% 50%, rgba(123,47,247,0.12), transparent 50%);
      animation: pulse 8s ease-in-out infinite;
    }
    
    @keyframes pulse {
      0%, 100% { opacity: 0.6; }
      50% { opacity: 1; }
    }
    
    .hero-inner {
      position: relative;
      z-index: 1;
      max-width: 800px;
      padding: 40px 20px;
      animation: fadeInUp 0.8s ease-out;
      margin-top: 60px;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .hero-title {
      font-size: 4rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 3px;
      text-shadow: 0 0 20px rgba(176,27,165,0.8),
                   0 0 40px rgba(255,77,240,0.5),
                   0 4px 8px rgba(0,0,0,0.3);
      background: linear-gradient(135deg, #ff4df0, #ffb8ff, #b01ba5);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1.1;
      margin-bottom: 20px;
    }
    
    .hero-sub {
      margin-top: 20px;
      font-size: 1.25rem;
      color: #e1d7ff;
      opacity: 0.95;
      line-height: 1.6;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .hero-btns {
      margin-top: 40px;
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }
    
    .btn-neon {
      padding: 16px 36px;
      border-radius: 999px;
      border: none;
      text-decoration: none;
      color: #fff;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 1rem;
      letter-spacing: 0.5px;
      background: linear-gradient(135deg, var(--ma-accent), var(--ma-accent-soft));
      box-shadow: 0 0 20px rgba(255,77,240,0.6),
                  0 4px 15px rgba(255,77,240,0.3),
                  inset 0 1px 0 rgba(255,255,255,0.2);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    
    .btn-neon::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.5s;
    }
    
    .btn-neon:hover::before {
      left: 100%;
    }
    
    .btn-neon:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 0 30px rgba(255,77,240,0.9),
                  0 8px 25px rgba(255,77,240,0.4);
    }

    /* ----- SECTION ORGANISATIONS ----- */
    .section-title {
      text-align: center;
      padding: 80px 15px 40px;
      position: relative;
    }
    
    .section-title::before {
      content: '';
      position: absolute;
      top: 60px;
      left: 50%;
      transform: translateX(-50%);
      width: 150px;
      height: 4px;
      background: linear-gradient(90deg, transparent, var(--ma-accent), transparent);
      border-radius: 2px;
    }
    
    .section-title h2 {
      font-size: 2.8rem;
      text-transform: uppercase;
      font-weight: 900;
      letter-spacing: 2px;
      background: linear-gradient(135deg, #ff4df0, #ffb8ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 12px;
      text-shadow: 0 0 30px rgba(255,77,240,0.3);
    }
    
    .section-title p {
      color: #ddd;
      opacity: .9;
      max-width: 650px;
      margin: 0 auto;
      font-size: 1.1rem;
      line-height: 1.6;
    }

    #organisations {
      padding: 0 20px 80px;
      background: transparent;
    }
    
    .organisations-wrapper {
      max-width: 1200px;
      margin: 0 auto 60px;
      padding: 0 15px;
    }

    .organisations-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 30px;
    }
    
    /* Carte organisation */
    .org-card {
      background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
      border-radius: 20px;
      padding: 25px;
      border: 1px solid var(--ma-border);
      box-shadow: 0 20px 40px rgba(0,0,0,0.6),
                  inset 0 1px 0 rgba(255,255,255,0.1);
      position: relative;
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      animation: fadeInUp 0.6s ease-out both;
      display: flex;
      flex-direction: column;
    }
    
    .org-card::before {
      content: '';
      position: absolute;
      inset: -30%;
      opacity: 0;
      background: radial-gradient(circle at top left, rgba(255,77,240,0.3), transparent 60%),
                  radial-gradient(circle at bottom right, rgba(123,47,247,0.25), transparent 60%);
      transition: opacity 0.4s ease;
    }
    
    .org-card::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #ff4df0, #7b2ff7, #ff4df0);
      background-size: 200% 100%;
      opacity: 0;
      transition: opacity 0.3s ease;
      animation: shimmer 2s linear infinite;
    }
    
    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }
    
    .org-card:hover::before {
      opacity: 1;
    }
    
    .org-card:hover::after {
      opacity: 1;
    }
    
    .org-card:hover {
      transform: translateY(-8px) scale(1.02);
      border-color: rgba(255,77,240,0.4);
      box-shadow: 0 30px 60px rgba(0,0,0,0.8),
                  0 0 40px rgba(255,77,240,0.4),
                  inset 0 1px 0 rgba(255,255,255,0.15);
    }
    
    /* Indicateur de lien */
    .org-link-indicator {
      position: absolute;
      top: 15px;
      right: 15px;
      background: linear-gradient(135deg, var(--ma-success), #2ecc71);
      color: #000;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
      z-index: 2;
      box-shadow: 0 4px 12px rgba(76,255,76,0.4);
      transition: all 0.3s ease;
    }
    
    .org-link-indicator:hover {
      transform: scale(1.1) rotate(15deg);
      box-shadow: 0 6px 20px rgba(76,255,76,0.6);
    }
    
    /* Image container */
    .org-image-container {
      width: 100%;
      height: 200px;
      border-radius: 15px;
      overflow: hidden;
      margin-bottom: 20px;
      position: relative;
      background: rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(255,255,255,0.1);
    }
    
    .org-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    
    .org-card:hover .org-image {
      transform: scale(1.08);
    }
    
    .org-image-placeholder {
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #8b5cf6, #ec4899);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2.5rem;
      font-weight: bold;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    /* Contenu */
    .org-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      position: relative;
      z-index: 1;
    }
    
    .org-title {
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: 15px;
      color: #ff9cff;
      line-height: 1.3;
      transition: color 0.3s;
    }
    
    .org-card:hover .org-title {
      color: #ffb8ff;
      text-shadow: 0 0 10px rgba(255,77,240,0.5);
    }
    
    .org-description {
      font-size: 0.95rem;
      color: #e1d7ff;
      opacity: .9;
      margin-bottom: 20px;
      line-height: 1.5;
      flex: 1;
    }
    
    /* Barre de progression améliorée */
    .progress-section {
      margin: 20px 0;
      background: rgba(0, 0, 0, 0.25);
      padding: 20px;
      border-radius: 15px;
      border: 1px solid rgba(176, 27, 165, 0.3);
      backdrop-filter: blur(5px);
    }
    
    .progress-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }
    
    .progress-label {
      color: #ccc;
      font-size: 0.9rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .progress-percentage {
      color: var(--ma-success);
      font-weight: bold;
      font-size: 1.2rem;
      background: rgba(76, 255, 76, 0.1);
      padding: 5px 15px;
      border-radius: 20px;
      border: 1px solid rgba(76, 255, 76, 0.3);
    }
    
    .progress-bar-container {
      width: 100%;
      height: 12px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 6px;
      overflow: hidden;
      margin: 12px 0;
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .progress-bar-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--ma-success), var(--ma-accent));
      border-radius: 6px;
      transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    
    .progress-bar-fill::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      animation: shimmer 2s infinite;
    }
    
    .progress-details {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .progress-current {
      color: var(--ma-success);
      font-weight: bold;
      font-size: 1.1rem;
    }
    
    .progress-goal {
      color: var(--ma-accent);
      font-weight: bold;
      font-size: 0.95rem;
      text-align: right;
    }
    
    .progress-remaining {
      color: var(--ma-warning);
      font-size: 0.85rem;
      margin-top: 10px;
      text-align: center;
      font-style: italic;
      width: 100%;
    }
    
    /* Boutons */
    .org-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 25px;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .btn-don-org {
      flex: 1;
      font-size: .9rem;
      border-radius: 999px;
      padding: 12px 20px;
      border: none;
      text-decoration: none;
      color: #fff;
      font-weight: 700;
      letter-spacing: 0.3px;
      background: linear-gradient(135deg, var(--ma-accent), var(--ma-accent-soft));
      box-shadow: 0 4px 12px rgba(255,77,240,0.4);
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      cursor: pointer;
      text-transform: uppercase;
    }
    
    .btn-don-org:hover {
      transform: translateY(-2px) scale(1.05);
      filter: brightness(1.1);
      box-shadow: 0 6px 20px rgba(255,77,240,0.5);
    }
    
    .btn-details {
      flex: 1;
      font-size: .9rem;
      border-radius: 999px;
      padding: 12px 20px;
      border: 2px solid var(--ma-accent);
      text-decoration: none;
      color: #ffb8ff;
      font-weight: 700;
      letter-spacing: 0.3px;
      background: rgba(8,22,36,0.7);
      backdrop-filter: blur(10px);
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      cursor: pointer;
      text-transform: uppercase;
    }
    
    .btn-details:hover:not(:disabled) {
      background: linear-gradient(135deg, var(--ma-accent), var(--ma-accent-soft));
      color: #160819;
      transform: translateY(-2px) scale(1.05);
      box-shadow: 0 6px 20px rgba(255,77,240,0.5);
    }
    
    .btn-details:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    /* ----- FOOTER ----- */
    footer {
      background: linear-gradient(180deg, transparent, rgba(8,22,36,0.8));
      text-align: center;
      padding: 40px 20px;
      margin-top: 60px;
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    
    footer p {
      margin: 0;
      color: #999;
      font-size: 0.9rem;
      line-height: 1.8;
    }

    /* Responsive */
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
        margin-left: 0; /* Retirer le décalage sur mobile */
      }
      
      .hero {
        min-height: 100vh;
        padding-top: 100px;
      }
      
      .hero-inner {
        margin-top: 40px;
      }
      
      .hero-title {
        font-size: 2.8rem;
        letter-spacing: 1px;
      }
      
      .hero-sub {
        font-size: 1.1rem;
      }
      
      .hero-btns {
        flex-direction: column;
        width: 100%;
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
        gap: 12px;
      }
      
      .section-title h2 {
        font-size: 2rem;
      }
      
      .organisations-container {
        grid-template-columns: 1fr;
        gap: 25px;
      }
      
      .org-card {
        max-width: 500px;
        margin: 0 auto;
      }
    }
    
    @media (max-width: 480px) {
      .hero-title {
        font-size: 2.2rem;
      }
      
      .hero-sub {
        font-size: 1rem;
      }
      
      .org-card {
        padding: 20px;
      }
      
      .org-image-container {
        height: 180px;
      }
      
      .hero-btns .btn-neon {
        padding: 14px 28px;
        font-size: 0.9rem;
      }
    }

    /* Animation de fondu pour les cartes */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Animation de chargement pour les barres */
    .progress-bar-fill {
      animation: slideIn 1s ease-out forwards;
    }

    @keyframes slideIn {
      from {
        width: 0;
      }
    }
  </style>
</head>

<body>

  <!-- Sélecteur de Langue -->
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
      <i class="bi bi-currency-euro"></i>
      EUR (€)
    </div>
  </div>

  <!-- HEADER Traduit -->
  <header>
    <h1>🎮 <?= $t('site_title') ?></h1>
    <nav>
      <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i> <?= $t('home') ?>
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

  <!-- HERO SECTION Traduite -->
  <section class="hero" id="accueil">
    <div class="hero-inner">
      <h1 class="hero-title"><?= $t('game_for_good') ?></h1>
      <p class="hero-sub">
        <?= $t('welcome') ?> ! <?= $t('slogan') ?>.
      </p>
      <div class="hero-btns">
        <a href="#organisations" class="btn-neon">
          <i class="bi bi-heart-fill"></i> <?= $t('support_cause') ?>
        </a>
        <a href="stats-live.php" class="btn-neon" style="background: linear-gradient(135deg, #7b2ff7, #4c1c95);">
          <i class="bi bi-bar-chart-fill"></i> <?= $t('see_live_stats') ?>
        </a>
      </div>
    </div>
  </section>

  <!-- SECTION ORGANISATIONS Traduite -->
  <section id="organisations">
    <div class="section-title">
      <h2><?= $t('our_partners') ?></h2>
      <p><?= $t('choose_cause') ?></p>
    </div>
    
    <div class="organisations-wrapper">
      <div class="organisations-container">
        <?php 
        $index = 0;
        foreach ($organisations as $org): 
          $montantTotal = $org['montant_total'] ?? 0;
          $orgId = $org['id'] ?? 0;
          $objectif = $objectifsParOrganisation[$orgId] ?? 5000;
          $pourcentage = $objectif > 0 ? min(100, ($montantTotal / $objectif) * 100) : 0;
          $montantRestant = max(0, $objectif - $montantTotal);
          
          // Utiliser l'image uploadée si elle existe
          $imagePath = '';
          if (!empty($org['image_url'])) {
              $imagePath = $org['image_url'];
          } else {
              // Image par défaut
              $imagePath = '/projet-dons/View/frontoffice/images/default-org.jpg';
          }
        ?>
          <div class="org-card" style="animation-delay: <?= $index * 0.1 ?>s;">
            
            <?php if (!empty($org['website_url'])): ?>
              <div class="org-link-indicator" title="<?= $t('visit_website') ?>" 
                   onclick="window.open('<?= htmlspecialchars($org['website_url']) ?>', '_blank')">
                <i class="bi bi-link-45deg"></i>
              </div>
            <?php endif; ?>
            
            <!-- Image de l'organisation -->
            <div class="org-image-container">
              <?php if (!empty($imagePath)): ?>
                <img src="<?= htmlspecialchars($imagePath) ?>" 
                     alt="<?= htmlspecialchars($org['nom']) ?>" 
                     class="org-image"
                     onerror="this.onerror=null; this.src='/projet-dons/View/frontoffice/images/default-org.jpg';">
              <?php else: ?>
                <div class="org-image-placeholder">
                  <?= substr(htmlspecialchars($org['nom']), 0, 2) ?>
                </div>
              <?php endif; ?>
            </div>
            
            <div class="org-content">
              <h3 class="org-title"><?= htmlspecialchars($org['nom']) ?></h3>
              
              <div class="org-description">
                <p><?= htmlspecialchars(substr($org['description'], 0, 120)) ?>...</p>
              </div>
              
              <!-- Barre de progression avec textes traduits -->
              <div class="progress-section">
                <div class="progress-header">
                  <span class="progress-label"><?= $t('progress') ?></span>
                  <span class="progress-percentage"><?= number_format($pourcentage, 1) ?>%</span>
                </div>
                
                <div class="progress-bar-container">
                  <div class="progress-bar-fill" data-percentage="<?= $pourcentage ?>" style="width: <?= $pourcentage ?>%"></div>
                </div>
                
                <div class="progress-details">
                  <!-- Utilisation de formatMoney qui retourne toujours des euros -->
                  <span class="progress-current">
                    <?= $formatMoney($montantTotal) ?> <?= $t('collected') ?>
                  </span>
                  <span class="progress-goal">
                    <?= $t('goal') ?>: <?= $formatMoney($objectif) ?>
                  </span>
                </div>
                
                <?php if ($montantRestant > 0): ?>
                  <div class="progress-remaining">
                    <i class="bi bi-arrow-up-right"></i> 
                    <?= $t('remaining') ?> <?= $formatMoney($montantRestant) ?> <?= $t('to_collect') ?>
                  </div>
                <?php else: ?>
                  <div class="progress-remaining" style="color: var(--ma-success);">
                    <i class="bi bi-trophy-fill"></i> <?= $t('goal_reached') ?>
                  </div>
                <?php endif; ?>
              </div>
              
              <!-- Boutons traduits -->
              <div class="org-actions">
                <a href="addDon.php?orgId=<?= $org['id'] ?>" class="btn-don-org">
                  <i class="bi bi-gift-fill"></i> <?= $t('make_donation') ?>
                </a>
                
                <?php if (!empty($org['website_url'])): ?>
                  <button class="btn-details" onclick="window.open('<?= htmlspecialchars($org['website_url']) ?>', '_blank')">
                    <i class="bi bi-globe"></i> <?= $t('visit_website') ?>
                  </button>
                <?php else: ?>
                  <button class="btn-details" disabled>
                    <i class="bi bi-globe"></i> <?= $t('visit_website') ?>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php $index++; endforeach; ?>
      </div>
    </div>
  </section>

  <!-- FOOTER Traduit -->
  <footer>
    <p>
      <?= $t('copyright') ?>
      <br><small><?= $t('tagline') ?></small>
      <br><small style="opacity: 0.7;">
        <?= $t('current_language') ?>: <?= $supportedLangs[$currentLang]['name'] ?> | 
        <?= $t('currency') ?>: EUR (€)
      </small>
    </p>
  </footer>

  <!-- Scripts pour la gestion des langues -->
  <script>
    // Toggle du menu des langues
    function toggleLanguageMenu() {
      const options = document.getElementById('languageOptions');
      options.style.display = options.style.display === 'block' ? 'none' : 'block';
    }
    
    // Changer de langue
    function changeLanguage(langCode) {
      // Sauvegarder la préférence
      localStorage.setItem('preferred_language', langCode);
      
      // Rediriger avec le paramètre langue
      const url = new URL(window.location);
      url.searchParams.set('lang', langCode);
      window.location.href = url.toString();
    }
    
    // Fermer le menu en cliquant ailleurs
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('languageDropdown');
      if (!dropdown.contains(event.target)) {
        document.getElementById('languageOptions').style.display = 'none';
      }
    });
    
    // Appliquer la langue sauvegardée au chargement
    document.addEventListener('DOMContentLoaded', function() {
      const savedLang = localStorage.getItem('preferred_language');
      const currentLang = '<?= $currentLang ?>';
      const urlParams = new URLSearchParams(window.location.search);
      
      if (savedLang && savedLang !== currentLang && !urlParams.has('lang')) {
        changeLanguage(savedLang);
      }
    });

    // Animation au scroll
    // Smooth scroll pour les liens d'ancrage
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Animation des barres de progression
    const progressBars = document.querySelectorAll('.progress-bar-fill');
    progressBars.forEach(bar => {
      const percentage = bar.getAttribute('data-percentage') || 0;
      bar.style.width = '0';
      setTimeout(() => {
        bar.style.transition = 'width 1.5s cubic-bezier(0.4, 0, 0.2, 1)';
        bar.style.width = percentage + '%';
      }, 300 + Math.random() * 500);
    });

    // Animation au scroll pour les cartes
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);
    
    // Observer toutes les cartes d'organisations
    document.querySelectorAll('.org-card').forEach(card => {
      observer.observe(card);
    });

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
  </script>

</body>
</html>