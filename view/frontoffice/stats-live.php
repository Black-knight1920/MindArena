<?php
require_once __DIR__."/../../Controller/DonController.php";
require_once __DIR__."/../../Controller/OrganisationController.php";
// Inclure le helper de langue
require_once __DIR__."/LanguageHelper.php";

// Initialiser le gestionnaire de langue
$lang = LanguageHelper::getInstance();

// Fonction raccourci pour les templates
$t = function($key, $params = []) use ($lang) { 
    return $lang->translate($key, $params); 
};

$formatMoney = function($amount) use ($lang) { 
    // Montants en base EUR -> convertir selon langue puis formater
    return $lang->formatMoneyFromEUR($amount); 
};

// Récupérer les informations courantes
$currentLang = $lang->getCurrentLang();
$supportedLangs = $lang->getSupportedLanguages();
$currencyInfo = $lang->getCurrencyInfo();

$donCtrl = new DonController();
$orgCtrl = new OrganisationController();

// Fonction pour récupérer les stats du jour
function getTodayStats($donCtrl) {
    $sql = "SELECT COUNT(*) as nb_dons, COALESCE(SUM(montant), 0) as total, 
            COALESCE(AVG(montant), 0) as moyenne, MAX(montant) as max_don 
            FROM don WHERE DATE(dateDon) = CURDATE()";
    $db = config::getConnexion();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour top donateurs du jour
function getTodayTopDonors($donCtrl) {
    $sql = "SELECT CONCAT(prenom_donateur, ' ', nom_donateur) as nom_complet, 
            SUM(montant) as total 
            FROM don 
            WHERE DATE(dateDon) = CURDATE() 
            AND nom_donateur IS NOT NULL 
            AND prenom_donateur IS NOT NULL 
            GROUP BY nom_donateur, prenom_donateur 
            ORDER BY total DESC LIMIT 5";
    $db = config::getConnexion();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour derniers dons
function getRecentDonations($donCtrl, $limit = 10) {
    $sql = "SELECT d.*, o.nom as organisation_nom, 
            CONCAT(d.prenom_donateur, ' ', d.nom_donateur) as nom_complet 
            FROM don d 
            LEFT JOIN organisation o ON d.organisationId = o.id 
            ORDER BY d.dateDon DESC, d.id DESC LIMIT :limit";
    $db = config::getConnexion();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour stats des 7 derniers jours
function getLast7DaysStats($donCtrl) {
    $sql = "SELECT DATE(dateDon) as date, 
            COUNT(*) as nb_dons, 
            COALESCE(SUM(montant), 0) as total,
            COALESCE(AVG(montant), 0) as moyenne
            FROM don 
            WHERE dateDon >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(dateDon)
            ORDER BY date ASC";
    $db = config::getConnexion();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les dates
    $formattedResults = [];
    foreach ($results as $row) {
        $date = new DateTime($row['date']);
        $formattedResults[] = [
            'date' => $date->format('d/m'),
            'jour' => $date->format('D'),
            'nb_dons' => (int)$row['nb_dons'],
            'total' => (float)$row['total'],
            'moyenne' => (float)$row['moyenne']
        ];
    }
    return $formattedResults;
}

// Fonction pour stats par organisation
function getStatsByOrganisation($donCtrl) {
    $sql = "SELECT o.nom, 
            COUNT(d.id) as nb_dons, 
            COALESCE(SUM(d.montant), 0) as total,
            COALESCE(AVG(d.montant), 0) as moyenne
            FROM organisation o
            LEFT JOIN don d ON o.id = d.organisationId
            GROUP BY o.id, o.nom
            ORDER BY total DESC
            LIMIT 10";
    $db = config::getConnexion();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour stats par heure du jour
function getHourlyStats($donCtrl) {
    $sql = "SELECT HOUR(dateDon) as heure, 
            COUNT(*) as nb_dons, 
            COALESCE(SUM(montant), 0) as total
            FROM don 
            WHERE DATE(dateDon) = CURDATE()
            GROUP BY HOUR(dateDon)
            ORDER BY heure ASC";
    $db = config::getConnexion();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Compléter les heures manquantes
    $hourlyData = [];
    for ($i = 0; $i < 24; $i++) {
        $found = false;
        foreach ($results as $row) {
            if ($row['heure'] == $i) {
                $hourlyData[] = [
                    'heure' => $i,
                    'label' => sprintf('%02dh', $i),
                    'nb_dons' => (int)$row['nb_dons'],
                    'total' => (float)$row['total']
                ];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $hourlyData[] = [
                'heure' => $i,
                'label' => sprintf('%02dh', $i),
                'nb_dons' => 0,
                'total' => 0
            ];
        }
    }
    return $hourlyData;
}

// Fonction pour distribution des montants
function getAmountDistribution($donCtrl, $currencySymbol) {
    $ranges = [
        ['min' => 0, 'max' => 50, 'label' => '0-50' . $currencySymbol],
        ['min' => 50, 'max' => 100, 'label' => '50-100' . $currencySymbol],
        ['min' => 100, 'max' => 500, 'label' => '100-500' . $currencySymbol],
        ['min' => 500, 'max' => 1000, 'label' => '500-1000' . $currencySymbol],
        ['min' => 1000, 'max' => 10000, 'label' => '1000+' . $currencySymbol]
    ];
    
    $distribution = [];
    foreach ($ranges as $range) {
        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(montant), 0) as total
                FROM don 
                WHERE montant > :min AND montant <= :max";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':min', $range['min']);
        $stmt->bindValue(':max', $range['max']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $distribution[] = [
            'label' => $range['label'],
            'count' => (int)$result['count'],
            'total' => (float)$result['total']
        ];
    }
    
    return $distribution;
}

// Récupérer toutes les données
$todayStats = getTodayStats($donCtrl);
$todayTopDonors = getTodayTopDonors($donCtrl);
$recentDonations = getRecentDonations($donCtrl);
$last7DaysStats = getLast7DaysStats($donCtrl);
$orgStats = getStatsByOrganisation($donCtrl);
$hourlyStats = getHourlyStats($donCtrl);
$amountDistribution = getAmountDistribution($donCtrl, $currencyInfo['symbol']);

// Calculer le total général
$sqlTotal = "SELECT COALESCE(SUM(montant), 0) as total FROM don";
$db = config::getConnexion();
$totalResult = $db->query($sqlTotal)->fetch();
$totalGeneral = $totalResult['total'];

// Préparer les données pour les graphiques (JSON)
$chart7DaysData = json_encode($last7DaysStats);
$chartHourlyData = json_encode($hourlyStats);
$chartOrgData = json_encode($orgStats);
$chartAmountData = json_encode($amountDistribution);

// Récupérer le timestamp de dernière mise à jour
$lastUpdate = date('H:i:s');
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
  <meta charset="UTF-8">
  <title><?= $t('site_title') ?> - <?= $t('live_stats') ?></title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
  
  <!-- Style pour sélecteur de langue -->
  <style>
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
    
    /* Responsive pour sélecteur de langue */
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
    }
  </style>
  
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
        --ma-info: #4db8ff;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Roboto', sans-serif;
        /* Même arrière-plan que l'index.php */
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
        overflow-x: hidden;
        min-height: 100vh;
        padding-top: 80px;
    }

    /* ----- HEADER ----- */
    header {
      background: rgba(8,22,36,0.95);
      backdrop-filter: blur(10px);
      padding: 15px 40px;
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

    /* Main Container */
    .main-container {
        padding: 140px 20px 80px;
        max-width: 1600px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    /* Live Status Bar */
    .live-status-bar {
        position: fixed;
        top: 80px;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, rgba(176, 27, 165, 0.9), rgba(226, 30, 228, 0.9));
        color: white;
        padding: 14px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 999;
        box-shadow: 0 4px 30px rgba(176, 27, 165, 0.5);
        backdrop-filter: blur(10px);
        animation: pulse-border 2s infinite;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    @keyframes pulse-border {
        0%, 100% { 
            box-shadow: 0 4px 20px rgba(176, 27, 165, 0.4),
                        0 0 30px rgba(255,77,240,0.3); 
        }
        50% { 
            box-shadow: 0 4px 30px rgba(176, 27, 165, 0.7),
                        0 0 40px rgba(255,77,240,0.5); 
        }
    }

    .live-indicator {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .live-dot {
        width: 12px;
        height: 12px;
        background: var(--ma-success);
        border-radius: 50%;
        animation: pulse-dot 1.5s infinite;
        box-shadow: 0 0 10px var(--ma-success);
    }

    @keyframes pulse-dot {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }

    .update-info {
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 0.9rem;
    }

    .last-update {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(0, 0, 0, 0.3);
        padding: 6px 12px;
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .refresh-countdown {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(0, 0, 0, 0.3);
        padding: 6px 12px;
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .refresh-btn {
        background: linear-gradient(135deg, var(--ma-accent), var(--ma-accent-soft));
        border: 2px solid rgba(255,255,255,0.3);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(255,77,240,0.4);
    }

    .refresh-btn:hover {
        background: linear-gradient(135deg, var(--ma-accent), #ffb8ff);
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 25px rgba(255,77,240,0.6);
    }

    /* Stats Header */
    .stats-header {
        text-align: center;
        margin-bottom: 50px;
        position: relative;
        padding-top: 20px;
        animation: fadeInUp 0.8s ease-out;
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

    .stats-header h1 {
        font-size: 3rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 2px;
        background: linear-gradient(135deg, #ff4df0, #ffb8ff, #b01ba5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 15px;
        text-shadow: 0 0 20px rgba(176,27,165,0.5);
    }

    .stats-header .subtitle {
        color: #e1d7ff;
        font-size: 1.2rem;
        max-width: 600px;
        margin: 0 auto 30px;
        opacity: 0.9;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 50px;
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
        border: 1px solid var(--ma-border);
        border-radius: 18px;
        padding: 30px 25px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.6),
                    inset 0 1px 0 rgba(255,255,255,0.1);
        animation: fadeInUp 0.6s ease-out both;
    }

    .stat-card::before {
        content:'';
        position:absolute;
        inset:-30%;
        opacity:0;
        background:radial-gradient(circle at top left,rgba(255,77,240,0.3),transparent 60%),
                    radial-gradient(circle at bottom right,rgba(123,47,247,0.25),transparent 60%);
        transition:opacity 0.3s ease;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff4df0, #7b2ff7, #ff4df0);
        background-size: 200% 100%;
        opacity: 0.5;
        animation: shimmer 2s linear infinite;
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .stat-card:hover::before { opacity:1; }
    .stat-card:hover {
        transform:translateY(-8px) scale(1.03);
        border-color: rgba(255,77,240,0.4);
        box-shadow: 0 30px 60px rgba(0,0,0,0.8),
                    0 0 40px rgba(255,77,240,0.4),
                    inset 0 1px 0 rgba(255,255,255,0.15);
    }

    .stat-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        display: block;
        background: linear-gradient(135deg, #ff4df0, #ffb8ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 10px rgba(255,77,240,0.5));
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 10px;
        background: linear-gradient(135deg, #fff, #ffb8ff, #ff4df0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 0 15px rgba(255,77,240,0.3);
    }

    .stat-label {
        color: #e1d7ff;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
        opacity: 0.9;
    }

    /* Section Title */
    .section-title {
        text-align:center;
        padding: 40px 15px 30px;
        position: relative;
        margin-bottom: 30px;
    }

    .section-title::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: linear-gradient(90deg, transparent, var(--ma-accent), transparent);
        border-radius: 2px;
    }

    .section-title h2 {
        font-size: 2.5rem;
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

    /* Tabs */
    .tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(255,255,255,0.1);
        flex-wrap: wrap;
        justify-content: center;
    }

    .tab {
        padding: 14px 28px;
        background: rgba(255,255,255,.05);
        border: 1px solid var(--ma-border);
        border-radius: 20px;
        color: #e1d7ff;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1rem;
        letter-spacing: 0.5px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .tab.active {
        background: linear-gradient(135deg, var(--ma-accent), var(--ma-accent-soft));
        color: white;
        border-color: transparent;
        box-shadow: 0 6px 25px rgba(255,77,240,0.5),
                    0 0 30px rgba(255,77,240,0.3);
        transform: translateY(-2px);
    }

    .tab:hover:not(.active) {
        background: rgba(255,255,255,.1);
        border-color: rgba(255,77,240,0.3);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255,77,240,0.2);
    }

    /* Charts Grid */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    @media (max-width: 1100px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }
    }

    .chart-container {
        background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
        border: 1px solid var(--ma-border);
        border-radius: 18px;
        padding: 25px;
        position: relative;
        height: 400px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.6),
                    inset 0 1px 0 rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        animation: fadeInUp 0.6s ease-out both;
    }

    .chart-container:hover {
        border-color: rgba(255,77,240,0.3);
        box-shadow: 0 25px 50px rgba(0,0,0,0.7),
                    0 0 40px rgba(255,77,240,0.2),
                    inset 0 1px 0 rgba(255,255,255,0.1);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .chart-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #ffb8ff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-title i {
        color: var(--ma-accent);
        filter: drop-shadow(0 0 8px rgba(255,77,240,0.5));
    }

    .chart-period {
        color: #d4c5ff;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    /* Chart Wrapper */
    .chart-wrapper {
        position: relative;
        height: calc(100% - 60px);
        width: 100%;
    }

    canvas {
        width: 100% !important;
        height: 100% !important;
    }

    /* Live Feed */
    .feed-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 20px;
    }

    .feed-item {
        display: flex;
        align-items: center;
        padding: 18px 22px;
        background: linear-gradient(135deg, rgba(8,22,36,0.7), rgba(15,30,50,0.6));
        border: 1px solid var(--ma-border);
        border-radius: 15px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: slideIn 0.5s ease;
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .feed-item:hover {
        background: linear-gradient(135deg, rgba(8,22,36,0.8), rgba(15,30,50,0.7));
        border-color: rgba(255,77,240,0.4);
        transform: translateX(5px) translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.6),
                    0 0 30px rgba(255,77,240,0.2);
    }

    .feed-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255,77,240,0.2), rgba(176,27,165,0.3));
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 18px;
        flex-shrink: 0;
        font-size: 1.2rem;
        color: var(--ma-accent);
        box-shadow: 0 0 15px rgba(255,77,240,0.4);
    }

    .feed-details {
        flex-grow: 1;
    }

    .feed-donor {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #ffd9ff;
    }

    .anonymous {
        font-style: italic;
        color: #d4c5ff;
        opacity: 0.9;
    }

    .feed-org {
        color: var(--ma-accent);
        font-weight: 600;
        font-size: 0.95rem;
    }

    .feed-time {
        color: #b8a8ff;
        font-size: 0.85rem;
        opacity: 0.8;
    }

    .feed-amount {
        font-size: 1.4rem;
        font-weight: 900;
        color: var(--ma-success);
        text-shadow: 0 0 15px rgba(76, 255, 76, 0.5);
        background: linear-gradient(135deg, var(--ma-success), #80ff80);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Tab Content */
    .tab-content {
        display: none;
        animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .tab-content.active {
        display: block;
    }

    /* Chart Sections */
    .chart-section {
        background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
        border: 1px solid var(--ma-border);
        border-radius: 25px;
        padding: 35px;
        margin-bottom: 40px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.7),
                    inset 0 1px 0 rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        animation: fadeInUp 0.6s ease-out both;
    }

    /* Footer */
    footer {
        background: linear-gradient(180deg, transparent, rgba(8,22,36,0.8));
        text-align:center;
        padding: 40px 10px;
        margin-top: 60px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    footer p {
        margin:0;
        color:#999;
        font-size: 0.9rem;
    }

    /* Update Notification */
    .update-notification {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, var(--ma-success), #00cc66);
        color: #000;
        padding: 18px 28px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        gap: 18px;
        z-index: 1000;
        box-shadow: 0 15px 40px rgba(76, 255, 76, 0.5),
                    0 0 30px rgba(76, 255, 76, 0.3);
        animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1), fadeOut 0.5s ease 4.5s forwards;
        font-weight: 700;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }

    @keyframes slideUp {
        from {
            transform: translateY(100px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateY(100px);
        }
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #d4c5ff;
        font-size: 1.1rem;
        background: linear-gradient(135deg, rgba(8,22,36,0.5), rgba(15,30,50,0.4));
        border-radius: 18px;
        border: 2px dashed rgba(255,255,255,0.1);
        margin: 30px 0;
        backdrop-filter: blur(10px);
    }

    .empty-state i {
        font-size: 3.5rem;
        margin-bottom: 20px;
        opacity: 0.5;
        color: var(--ma-accent);
        filter: drop-shadow(0 0 10px rgba(255,77,240,0.3));
    }

    /* Responsive */
    @media (max-width: 768px) {
        body {
            padding-top: 120px;
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
        
        .main-container {
            padding: 160px 15px 60px;
        }
        
        .live-status-bar {
            top: 120px;
            padding: 12px 15px;
            flex-direction: column;
            gap: 12px;
        }
        
        .update-info {
            width: 100%;
            justify-content: space-between;
        }
        
        .stats-header h1 {
            font-size: 2.2rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .chart-section {
            padding: 25px;
        }
        
        .charts-grid {
            grid-template-columns: 1fr;
        }
        
        .chart-container {
            height: 350px;
            padding: 20px;
        }
        
        .tabs {
            flex-wrap: wrap;
        }
        
        .tab {
            flex: 1;
            min-width: 140px;
            justify-content: center;
            padding: 12px 20px;
        }
        
        .update-notification {
            left: 20px;
            right: 20px;
            bottom: 20px;
        }
    }

    @media (max-width: 480px) {
        .stats-header h1 {
            font-size: 1.8rem;
        }
        
        .stat-value {
            font-size: 2rem;
        }
        
        .section-title h2 {
            font-size: 1.8rem;
        }
        
        .chart-title {
            font-size: 1.1rem;
        }
        
        .feed-amount {
            font-size: 1.2rem;
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
        <span><?= $supportedLangs[$currentLang]['name'] ?? 'Langue' ?></span>
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

  <!-- Header Traduit -->
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

  <!-- Live Status Bar -->
  <div class="live-status-bar">
    <div class="live-indicator">
      <div class="live-dot"></div>
      <span><i class="fas fa-chart-line"></i> <?= $t('live_dashboard') ?></span>
    </div>
    <div class="update-info">
      <div class="last-update">
        <i class="fas fa-clock"></i>
        <span><?= $t('last_update') ?>: <span id="lastUpdateTime"><?= $lastUpdate ?></span></span>
      </div>
      <div class="refresh-countdown">
        <i class="fas fa-sync-alt"></i>
        <span><?= $t('next_update') ?>: <span id="countdown">30</span>s</span>
      </div>
      <button class="refresh-btn" onclick="forceRefresh()">
        <i class="fas fa-redo"></i>
        <?= $t('refresh_now') ?>
      </button>
    </div>
  </div>

  <div class="main-container">
    <!-- Stats Header -->
    <div class="stats-header">
      <h1><i class="fas fa-chart-line"></i> <?= $t('statistics_dashboard') ?></h1>
      <p class="subtitle"><?= $t('dashboard_subtitle') ?></p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-value"><?= $formatMoney($todayStats['total']) ?></div>
        <div class="stat-label"><?= $t('collected_today') ?></div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">❤️</div>
        <div class="stat-value"><?= $todayStats['nb_dons'] ?></div>
        <div class="stat-label"><?= $t('today_donations') ?></div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-value"><?= $formatMoney($todayStats['moyenne']) ?></div>
        <div class="stat-label"><?= $t('average_per_donation') ?></div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">🏆</div>
        <div class="stat-value"><?= $formatMoney($totalGeneral) ?></div>
        <div class="stat-label"><?= $t('total_general') ?></div>
      </div>
    </div>

    <!-- Section Title -->
    <div class="section-title">
      <h2><?= $t('detailed_analysis') ?></h2>
      <p><?= $t('analysis_subtitle') ?></p>
    </div>

    <!-- Tabs for Charts -->
    <div class="tabs">
      <button class="tab active" onclick="showTab('tab-trends')">
        <i class="fas fa-chart-line"></i> <?= $t('trends') ?>
      </button>
      <button class="tab" onclick="showTab('tab-distribution')">
        <i class="fas fa-chart-pie"></i> <?= $t('distribution') ?>
      </button>
      <button class="tab" onclick="showTab('tab-organisations')">
        <i class="fas fa-building"></i> <?= $t('organizations') ?>
      </button>
      <button class="tab" onclick="showTab('tab-donateurs')">
        <i class="fas fa-users"></i> <?= $t('recent_donations') ?>
      </button>
    </div>

    <!-- Trends Tab -->
    <div id="tab-trends" class="tab-content active">
      <div class="charts-grid">
        <!-- Chart 1: Evolution 7 jours -->
        <div class="chart-container">
          <div class="chart-header">
            <div class="chart-title">
              <i class="fas fa-calendar-week"></i>
              <?= $t('7_days_evolution') ?>
            </div>
            <div class="chart-period"><?= $t('last_week') ?> • <?= $t('realtime_update') ?></div>
          </div>
          <div class="chart-wrapper">
            <canvas id="trendChart7Days"></canvas>
          </div>
        </div>

        <!-- Chart 2: Heures -->
        <div class="chart-container">
          <div class="chart-header">
            <div class="chart-title">
              <i class="fas fa-clock"></i>
              <?= $t('activity_by_hour') ?>
            </div>
            <div class="chart-period"><?= $t('today') ?> • <?= $t('updated_every_30_seconds') ?></div>
          </div>
          <div class="chart-wrapper">
            <canvas id="hourlyChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Distribution Tab -->
    <div id="tab-distribution" class="tab-content">
      <div class="charts-grid">
        <!-- Chart 3: Distribution montants -->
        <div class="chart-container">
          <div class="chart-header">
            <div class="chart-title">
              <i class="fas fa-money-bill-wave"></i>
              <?= $t('amount_distribution') ?>
            </div>
            <div class="chart-period"><?= $t('all_donations') ?> • <?= $t('instant_update') ?></div>
          </div>
          <div class="chart-wrapper">
            <canvas id="amountDistributionChart"></canvas>
          </div>
        </div>

        <!-- Chart 4: Top organisations -->
        <div class="chart-container">
          <div class="chart-header">
            <div class="chart-title">
              <i class="fas fa-hand-holding-heart"></i>
              <?= $t('top_organizations') ?>
            </div>
            <div class="chart-period"><?= $t('by_amount_collected') ?> • <?= $t('live_data') ?></div>
          </div>
          <div class="chart-wrapper">
            <canvas id="orgBarChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Organisations Tab -->
    <div id="tab-organisations" class="tab-content">
      <div class="chart-section">
        <div class="section-title">
          <h2><i class="fas fa-chart-bar"></i> <?= $t('performance_by_organization') ?></h2>
          <p><?= $t('performance_subtitle') ?></p>
        </div>
        <div class="chart-container" style="height: 500px;">
          <div class="chart-wrapper">
            <canvas id="orgPerformanceChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Donateurs Tab -->
    <div id="tab-donateurs" class="tab-content">
      <div class="chart-section">
        <div class="section-title">
          <h2><i class="fas fa-bolt"></i> <?= $t('latest_donations') ?></h2>
          <p><?= $t('realtime_feed') ?></p>
        </div>
        
        <div class="feed-list">
          <?php foreach ($recentDonations as $don): ?>
            <div class="feed-item">
              <div class="feed-icon">
                <i class="fas fa-heart"></i>
              </div>
              <div class="feed-details">
                <div class="feed-donor">
                  <?php if (!empty($don['nom_complet'])): ?>
                    <?= htmlspecialchars($don['nom_complet']) ?>
                  <?php else: ?>
                    <span class="anonymous"><?= $t('anonymous_donor') ?></span>
                  <?php endif; ?>
                  <span class="feed-org"> → <?= htmlspecialchars($don['organisation_nom'] ?? 'N/A') ?></span>
                </div>
                <div class="feed-time">
                  <?= date('H:i', strtotime($don['dateDon'])) ?> - <?= date('d/m/Y', strtotime($don['dateDon'])) ?>
                  • <?= $t('updated_live') ?>
                </div>
              </div>
              <div class="feed-amount">
                <?= $formatMoney($don['montant']) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <footer>
    © 2024 Mind Arena — <?= $t('realtime_dashboard') ?>
    <br><small><i class="fas fa-sync-alt"></i> <?= $t('auto_update_30_seconds') ?></small>
    <br><small style="opacity: 0.7;">
      <?= $t('current_language') ?>: <?= $supportedLangs[$currentLang]['name'] ?> | 
      <?= $t('currency') ?>: <?= $currencyInfo['code'] ?>
    </small>
  </footer>

  <!-- Update Notification -->
  <div id="updateNotification" class="update-notification" style="display: none;">
    <i class="fas fa-check-circle"></i>
    <span id="updateMessage"><?= $t('dashboard_updated_success') ?></span>
  </div>

  <script>
    // Données des graphiques
    const last7DaysData = <?= $chart7DaysData ?>;
    const hourlyData = <?= $chartHourlyData ?>;
    const orgData = <?= $chartOrgData ?>;
    const amountData = <?= $chartAmountData ?>;

    // Configuration globale de Chart.js
    Chart.defaults.color = '#fff';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

    // Couleurs pour les graphiques
    const chartColors = {
      purple: 'rgba(176, 27, 165, 0.8)',
      pink: 'rgba(226, 30, 228, 0.8)',
      green: 'rgba(76, 255, 76, 0.8)',
      blue: 'rgba(92, 124, 246, 0.8)',
      orange: 'rgba(255, 107, 107, 0.8)',
      accent: 'rgba(255, 77, 240, 0.8)',
      gradientPurple: (context) => {
        const chart = context.chart;
        const {ctx, chartArea} = chart;
        if (!chartArea) return null;
        
        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        gradient.addColorStop(0, 'rgba(176, 27, 165, 0.2)');
        gradient.addColorStop(1, 'rgba(226, 30, 228, 0.8)');
        return gradient;
      },
      gradientAccent: (context) => {
        const chart = context.chart;
        const {ctx, chartArea} = chart;
        if (!chartArea) return null;
        
        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        gradient.addColorStop(0, 'rgba(255, 77, 240, 0.2)');
        gradient.addColorStop(1, 'rgba(255, 77, 240, 0.8)');
        return gradient;
      }
    };

    // Variables globales pour les graphiques
    let trendChart, hourlyChart, amountChart, orgBarChart, orgPerformanceChart;
    let countdownInterval;
    let countdownValue = 30;
    let autoRefreshEnabled = true;

    // 1. Graphique évolution 7 jours
    function create7DaysChart() {
        const ctx = document.getElementById('trendChart7Days').getContext('2d');
        const dates = last7DaysData.map(d => d.date);
        const totals = last7DaysData.map(d => d.total);
        const counts = last7DaysData.map(d => d.nb_dons);

        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: '<?= $t('total_amount') ?> (<?= $currencyInfo['symbol'] ?>)',
                        data: totals,
                        borderColor: chartColors.accent,
                        backgroundColor: chartColors.gradientAccent,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y',
                        pointBackgroundColor: chartColors.accent,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    },
                    {
                        label: '<?= $t('number_of_donations') ?>',
                        data: counts,
                        borderColor: chartColors.green,
                        backgroundColor: 'rgba(76, 255, 76, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1',
                        pointBackgroundColor: chartColors.green,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff',
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(8, 22, 36, 0.9)',
                        titleColor: '#ff4df0',
                        bodyColor: '#fff',
                        borderColor: '#ff4df0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y} ${context.datasetIndex === 0 ? '<?= $currencyInfo['symbol'] ?>' : ''}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#e1d7ff'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: '<?= $t('amount') ?> (<?= $currencyInfo['symbol'] ?>)',
                            color: '#e1d7ff'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#e1d7ff',
                            callback: function(value) {
                                return value + ' <?= $currencyInfo['symbol'] ?>';
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: '<?= $t('number_of_donations') ?>',
                            color: '#e1d7ff'
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            color: '#e1d7ff'
                        }
                    }
                }
            }
        });
    }

    // 2. Graphique horaire
    function createHourlyChart() {
        const ctx = document.getElementById('hourlyChart').getContext('2d');
        const labels = hourlyData.map(h => h.label);
        const totals = hourlyData.map(h => h.total);

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '<?= $t('amount_per_hour') ?> (<?= $currencyInfo['symbol'] ?>)',
                    data: totals,
                    backgroundColor: labels.map((_, i) => {
                        const ratio = totals[i] / Math.max(...totals) || 0;
                        return `rgba(255, 77, 240, ${0.3 + ratio * 0.7})`;
                    }),
                    borderColor: chartColors.accent,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff',
                            font: {
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(8, 22, 36, 0.9)',
                        titleColor: '#ff4df0',
                        bodyColor: '#fff',
                        borderColor: '#ff4df0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `<?= $t('amount') ?>: ${context.parsed.y} <?= $currencyInfo['symbol'] ?>`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#e1d7ff'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#e1d7ff',
                            callback: function(value) {
                                return value + ' <?= $currencyInfo['symbol'] ?>';
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Graphique distribution montants
    function createAmountDistributionChart() {
        const ctx = document.getElementById('amountDistributionChart').getContext('2d');
        const labels = amountData.map(d => d.label);
        const counts = amountData.map(d => d.count);

        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: [
                        'rgba(76, 255, 76, 0.8)',
                        'rgba(92, 124, 246, 0.8)',
                        'rgba(255, 77, 240, 0.8)',
                        'rgba(255, 107, 107, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ],
                    borderColor: [
                        'rgba(76, 255, 76, 1)',
                        'rgba(92, 124, 246, 1)',
                        'rgba(255, 77, 240, 1)',
                        'rgba(255, 107, 107, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 2,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#fff',
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(8, 22, 36, 0.9)',
                        titleColor: '#ff4df0',
                        bodyColor: '#fff',
                        borderColor: '#ff4df0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label;
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} <?= $t('donations') ?> (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 4. Graphique barres organisations
    function createOrgBarChart() {
        const ctx = document.getElementById('orgBarChart').getContext('2d');
        const labels = orgData.map(o => o.nom.substring(0, 20) + (o.nom.length > 20 ? '...' : ''));
        const totals = orgData.map(o => o.total);

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '<?= $t('total_amount') ?> (<?= $currencyInfo['symbol'] ?>)',
                    data: totals,
                    backgroundColor: chartColors.gradientAccent,
                    borderColor: chartColors.accent,
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff',
                            font: {
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(8, 22, 36, 0.9)',
                        titleColor: '#ff4df0',
                        bodyColor: '#fff',
                        borderColor: '#ff4df0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `<?= $t('amount') ?>: ${context.parsed.x} <?= $currencyInfo['symbol'] ?>`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#e1d7ff',
                            callback: function(value) {
                                return value + ' <?= $currencyInfo['symbol'] ?>';
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#e1d7ff',
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }

    // 5. Graphique performance organisations
    function createOrgPerformanceChart() {
        const ctx = document.getElementById('orgPerformanceChart').getContext('2d');
        const labels = orgData.map(o => o.nom);
        const totals = orgData.map(o => o.total);
        const averages = orgData.map(o => o.moyenne);

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '<?= $t('total_amount') ?> (<?= $currencyInfo['symbol'] ?>)',
                        data: totals,
                        backgroundColor: chartColors.gradientAccent,
                        borderColor: chartColors.accent,
                        borderWidth: 2,
                        borderRadius: 5,
                        yAxisID: 'y'
                    },
                    {
                        label: '<?= $t('average_per_donation') ?> (<?= $currencyInfo['symbol'] ?>)',
                        data: averages,
                        backgroundColor: 'rgba(76, 255, 76, 0.6)',
                        borderColor: chartColors.green,
                        borderWidth: 2,
                        type: 'line',
                        yAxisID: 'y1',
                        pointBackgroundColor: chartColors.green,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff',
                            font: {
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(8, 22, 36, 0.9)',
                        titleColor: '#ff4df0',
                        bodyColor: '#fff',
                        borderColor: '#ff4df0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#e1d7ff',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: '<?= $t('total_amount') ?> (<?= $currencyInfo['symbol'] ?>)',
                            color: '#e1d7ff'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#e1d7ff',
                            callback: function(value) {
                                return value + ' <?= $currencyInfo['symbol'] ?>';
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: '<?= $t('average') ?> (<?= $currencyInfo['symbol'] ?>)',
                            color: '#e1d7ff'
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            color: '#e1d7ff',
                            callback: function(value) {
                                return value + ' <?= $currencyInfo['symbol'] ?>';
                            }
                        }
                    }
                }
            }
        });
    }

    // Initialisation des graphiques
    function initCharts() {
        if (trendChart) trendChart.destroy();
        if (hourlyChart) hourlyChart.destroy();
        if (amountChart) amountChart.destroy();
        if (orgBarChart) orgBarChart.destroy();
        if (orgPerformanceChart) orgPerformanceChart.destroy();
        
        trendChart = create7DaysChart();
        hourlyChart = createHourlyChart();
        amountChart = createAmountDistributionChart();
        orgBarChart = createOrgBarChart();
        orgPerformanceChart = createOrgPerformanceChart();
    }

    // Gestion du compte à rebours
    function startCountdown() {
        clearInterval(countdownInterval);
        countdownValue = 30;
        updateCountdownDisplay();
        
        countdownInterval = setInterval(() => {
            countdownValue--;
            updateCountdownDisplay();
            
            if (countdownValue <= 0 && autoRefreshEnabled) {
                performAutoRefresh();
            }
        }, 1000);
    }

    function updateCountdownDisplay() {
        const countdownElement = document.getElementById('countdown');
        if (countdownElement) {
            countdownElement.textContent = countdownValue;
            
            // Changer la couleur en fonction du temps restant
            if (countdownValue <= 10) {
                countdownElement.style.color = '#ff4b5c';
                countdownElement.style.textShadow = '0 0 10px #ff4b5c';
            } else if (countdownValue <= 20) {
                countdownElement.style.color = '#ffca5f';
                countdownElement.style.textShadow = '0 0 10px #ffca5f';
            } else {
                countdownElement.style.color = '#4cff4c';
                countdownElement.style.textShadow = '0 0 10px #4cff4c';
            }
        }
    }

    // Mise à jour auto
    function performAutoRefresh() {
        showUpdateNotification("<?= $t('auto_update_in_progress') ?>");
        
        // Simuler un chargement
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    // Refresh manuel
    function forceRefresh() {
        showUpdateNotification("<?= $t('manual_update_in_progress') ?>");
        
        // Réinitialiser le compte à rebours
        startCountdown();
        
        // Mettre à jour l'heure de dernière mise à jour
        const now = new Date();
        const timeString = now.toLocaleTimeString('<?= $currentLang ?>', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('lastUpdateTime').textContent = timeString;
        
        // Rafraîchir les graphiques
        initCharts();
        
        // Afficher la notification de succès
        setTimeout(() => {
            showUpdateNotification("<?= $t('dashboard_updated_success') ?>");
        }, 500);
    }

    // Notification de mise à jour
    function showUpdateNotification(message) {
        const notification = document.getElementById('updateNotification');
        const messageElement = document.getElementById('updateMessage');
        
        messageElement.textContent = message;
        notification.style.display = 'flex';
        
        // Réinitialiser l'animation
        notification.style.animation = 'none';
        setTimeout(() => {
            notification.style.animation = 'slideUp 0.5s ease, fadeOut 0.5s ease 4.5s forwards';
        }, 10);
        
        // Cacher après l'animation
        setTimeout(() => {
            notification.style.display = 'none';
        }, 5000);
    }

    // Gestion des tabs
    function showTab(tabId) {
        // Désactiver tous les tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Activer le tab sélectionné
        document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('active');
        document.getElementById(tabId).classList.add('active');
        
        // Redessiner les graphiques du tab actif
        setTimeout(() => {
            switch(tabId) {
                case 'tab-trends':
                    trendChart.resize();
                    hourlyChart.resize();
                    break;
                case 'tab-distribution':
                    amountChart.resize();
                    orgBarChart.resize();
                    break;
                case 'tab-organisations':
                    orgPerformanceChart.resize();
                    break;
            }
        }, 100);
    }

    // Fonctions pour la gestion des langues
    function toggleLanguageMenu() {
      const options = document.getElementById('languageOptions');
      options.style.display = options.style.display === 'block' ? 'none' : 'block';
    }
    
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

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
        startCountdown();
        
        // Animation des items du feed
        const feedItems = document.querySelectorAll('.feed-item');
        feedItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
        });
        
        // Mettre à jour l'heure de dernière mise à jour
        const now = new Date();
        const timeString = now.toLocaleTimeString('<?= $currentLang ?>', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('lastUpdateTime').textContent = timeString;
        
        // Effet de hover sur les cartes de stats
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.03)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
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
    });
  </script>
</body>
</html>