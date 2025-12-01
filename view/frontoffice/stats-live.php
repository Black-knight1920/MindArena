<?php
require_once __DIR__."/../../Controller/DonController.php";
require_once __DIR__."/../../Controller/OrganisationController.php";

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
function getAmountDistribution($donCtrl) {
    $ranges = [
        ['min' => 0, 'max' => 50, 'label' => '0-50€'],
        ['min' => 50, 'max' => 100, 'label' => '50-100€'],
        ['min' => 100, 'max' => 500, 'label' => '100-500€'],
        ['min' => 500, 'max' => 1000, 'label' => '500-1000€'],
        ['min' => 1000, 'max' => 10000, 'label' => '1000+€']
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
$amountDistribution = getAmountDistribution($donCtrl);

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
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mind Arena - Tableau de Bord Statistiques</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
  <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Roboto', sans-serif;
        background: linear-gradient(135deg, #501755 0%, #2d1854 50%, #081624 100%);
        color: #fff;
        line-height: 1.6;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    /* Effets de fond */
    .bg-particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
        overflow: hidden;
    }
    
    .particle {
        position: absolute;
        background: rgba(176, 27, 165, 0.3);
        border-radius: 50%;
        animation: float 15s infinite linear;
    }
    
    @keyframes float {
        0% { transform: translateY(0) translateX(0); }
        50% { transform: translateY(-100px) translateX(100px); }
        100% { transform: translateY(0) translateX(0); }
    }

    /* Header simplifié */
    header {
        background: rgba(8, 22, 36, 0.9);
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 0 9px 3px rgba(226,30,228,.24);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        backdrop-filter: blur(10px);
    }

    header h1 {
        font-size: 1.5rem;
        color: #fff;
        margin: 0;
    }

    nav a {
        color: #fff;
        text-decoration: none;
        margin-left: 2rem;
        font-weight: 500;
        transition: all 0.3s;
        padding: 8px 15px;
        border-radius: 20px;
    }

    nav a:hover { 
        color: #b01ba5;
        background: rgba(176, 27, 165, 0.1);
    }

    nav a.admin {
        color: #b01ba5;
    }

    /* Container principal */
    .main-container {
        padding: 100px 20px 80px;
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
        background: linear-gradient(90deg, rgba(176, 27, 165, 0.9), rgba(226, 30, 228, 0.9));
        color: white;
        padding: 12px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 999;
        box-shadow: 0 4px 20px rgba(176, 27, 165, 0.4);
        backdrop-filter: blur(10px);
        animation: pulse-border 2s infinite;
    }

    @keyframes pulse-border {
        0%, 100% { box-shadow: 0 4px 20px rgba(176, 27, 165, 0.4); }
        50% { box-shadow: 0 4px 30px rgba(176, 27, 165, 0.7); }
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
        background: #4cff4c;
        border-radius: 50%;
        animation: pulse-dot 1.5s infinite;
        box-shadow: 0 0 10px #4cff4c;
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
        border-radius: 15px;
    }

    .refresh-countdown {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(0, 0, 0, 0.3);
        padding: 6px 12px;
        border-radius: 15px;
    }

    .refresh-btn {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid white;
        color: white;
        padding: 6px 15px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .refresh-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    /* Stats Header */
    .stats-header {
        text-align: center;
        margin-bottom: 50px;
        position: relative;
        padding-top: 20px;
    }

    .stats-header h1 {
        font-size: 3rem;
        background: linear-gradient(45deg, #b01ba5, #e21ee4, #ff6bcb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 15px;
        font-weight: 900;
    }

    .stats-header .subtitle {
        color: #ccc;
        font-size: 1.2rem;
        max-width: 600px;
        margin: 0 auto 30px;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 50px;
    }

    .stat-card {
        background: rgba(255,255,255,.05);
        border: 2px solid;
        border-image: linear-gradient(45deg, #b01ba5, #e21ee4) 1;
        border-radius: 20px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #b01ba5, #e21ee4);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(176, 27, 165, 0.2);
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        display: block;
    }

    .stat-value {
        font-size: 2.2rem;
        font-weight: 900;
        margin-bottom: 10px;
        background: linear-gradient(45deg, #fff, #b01ba5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        color: #ccc;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 600;
    }

    /* Chart Sections */
    .chart-section {
        background: rgba(255,255,255,.03);
        border-radius: 25px;
        padding: 35px;
        margin-bottom: 40px;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid rgba(176, 27, 165, 0.3);
    }

    .section-title {
        font-size: 1.6rem;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .section-title i {
        color: #b01ba5;
    }

    .section-update {
        color: #4cff4c;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(76, 255, 76, 0.1);
        padding: 6px 12px;
        border-radius: 15px;
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
        background: rgba(0, 0, 0, 0.3);
        border-radius: 20px;
        padding: 25px;
        border: 1px solid rgba(176, 27, 165, 0.2);
        position: relative;
        height: 400px;
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
        font-weight: 600;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-period {
        color: #999;
        font-size: 0.9rem;
    }

    /* Chart Canvas */
    .chart-wrapper {
        position: relative;
        height: calc(100% - 60px);
        width: 100%;
    }

    canvas {
        width: 100% !important;
        height: 100% !important;
    }

    /* Top Donors */
    .donors-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 20px;
    }

    .donor-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 20px;
        background: rgba(255,255,255,.03);
        border-radius: 15px;
        transition: all 0.3s;
        border-left: 4px solid #b01ba5;
    }

    .donor-item:hover {
        background: rgba(255,255,255,.05);
        transform: translateX(5px);
    }

    .donor-rank {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .rank-1 .donor-rank { background: linear-gradient(135deg, #FFD700, #FFA500); color: #000; }
    .rank-2 .donor-rank { background: linear-gradient(135deg, #C0C0C0, #808080); color: #000; }
    .rank-3 .donor-rank { background: linear-gradient(135deg, #CD7F32, #8B4513); color: #000; }
    .rank-4 .donor-rank { background: #8b5cf6; color: white; }
    .rank-5 .donor-rank { background: #a855f7; color: white; }

    .donor-info {
        flex-grow: 1;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .donor-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #b01ba5, #e21ee4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: white;
        font-weight: 600;
    }

    .donor-details h3 {
        font-size: 1.1rem;
        margin-bottom: 5px;
    }

    .donor-details .time {
        color: #999;
        font-size: 0.85rem;
    }

    .donor-amount {
        font-size: 1.4rem;
        font-weight: 700;
        color: #4cff4c;
        text-shadow: 0 0 10px rgba(76, 255, 76, 0.5);
    }

    /* Live Feed */
    .feed-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 20px;
    }

    .feed-item {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        background: rgba(255,255,255,.02);
        border-radius: 12px;
        transition: all 0.3s;
        animation: slideIn 0.5s ease;
        border: 1px solid rgba(255,255,255,0.05);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .feed-item:hover {
        background: rgba(255,255,255,.04);
        transform: translateX(3px);
    }

    .feed-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(176, 27, 165, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
        font-size: 1.1rem;
        color: #b01ba5;
    }

    .feed-details {
        flex-grow: 1;
    }

    .feed-donor {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 3px;
    }

    .anonymous {
        font-style: italic;
        color: #999;
    }

    .feed-org {
        color: #b01ba5;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .feed-time {
        color: #888;
        font-size: 0.85rem;
    }

    .feed-amount {
        font-size: 1.3rem;
        font-weight: 700;
        color: #4cff4c;
        text-shadow: 0 0 10px rgba(76, 255, 76, 0.5);
    }

    /* Tabs */
    .tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        border-bottom: 2px solid rgba(255,255,255,0.1);
        padding-bottom: 20px;
    }

    .tab {
        padding: 12px 25px;
        background: rgba(255,255,255,.05);
        border: none;
        border-radius: 15px;
        color: #ccc;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tab.active {
        background: linear-gradient(135deg, #b01ba5, #e21ee4);
        color: white;
        box-shadow: 0 5px 15px rgba(176, 27, 165, 0.3);
    }

    .tab:hover:not(.active) {
        background: rgba(255,255,255,.1);
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .tab-content.active {
        display: block;
    }

    /* Tooltip */
    .chart-tooltip {
        background: rgba(0, 0, 0, 0.9);
        border: 2px solid #b01ba5;
        border-radius: 10px;
        padding: 15px;
        color: white;
        font-size: 0.9rem;
        pointer-events: none;
        z-index: 1000;
    }

    .tooltip-title {
        color: #b01ba5;
        font-weight: 600;
        margin-bottom: 5px;
    }

    /* Footer */
    footer {
        background: rgba(25, 13, 54, 0.9);
        text-align: center;
        padding: 2rem 1rem;
        font-size: .9rem;
        color: #aaa;
        line-height: 1.8;
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 1;
        margin-top: 80px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
        font-size: 1.1rem;
        background: rgba(255,255,255,.02);
        border-radius: 15px;
        border: 2px dashed rgba(255,255,255,0.1);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.3;
    }

    /* Update Notification */
    .update-notification {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, #4cff4c, #00cc66);
        color: #000;
        padding: 15px 25px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        z-index: 1000;
        box-shadow: 0 10px 30px rgba(76, 255, 76, 0.4);
        animation: slideUp 0.5s ease, fadeOut 0.5s ease 4.5s forwards;
        font-weight: 600;
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

    /* Responsive */
    @media (max-width: 768px) {
        header {
            padding: 15px 20px;
            flex-direction: column;
            gap: 15px;
        }
        
        nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }
        
        nav a {
            margin: 0;
        }
        
        .main-container {
            padding: 140px 15px 60px;
        }
        
        .live-status-bar {
            top: 120px;
            padding: 10px 15px;
            flex-direction: column;
            gap: 10px;
        }
        
        .update-info {
            width: 100%;
            justify-content: space-between;
        }
        
        .stats-header h1 {
            font-size: 2rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .chart-section {
            padding: 20px;
        }
        
        .charts-grid {
            grid-template-columns: 1fr;
        }
        
        .chart-container {
            height: 350px;
            padding: 15px;
        }
        
        .tabs {
            flex-wrap: wrap;
        }
        
        .tab {
            flex: 1;
            min-width: 120px;
            justify-content: center;
            padding: 10px 15px;
        }
        
        .update-notification {
            left: 20px;
            right: 20px;
            bottom: 20px;
        }
    }

    @media (max-width: 480px) {
        .stats-header h1 {
            font-size: 1.6rem;
        }
        
        .stat-value {
            font-size: 1.8rem;
        }
        
        .section-title {
            font-size: 1.4rem;
        }
        
        .chart-title {
            font-size: 1.1rem;
        }
        
        .donor-amount,
        .feed-amount {
            font-size: 1.1rem;
        }
    }
  </style>
</head>

<body>
  <!-- Effets de particules -->
  <div class="bg-particles" id="particles"></div>

  <!-- Header simplifié -->
  <header>
    <h1>Mind Arena Magazine</h1>
    <nav>
      <a href="index.php">Accueil</a>
      <a href="classementDonateurs.php">Classement</a>
      <a href="index.php#organisations">Associations</a>
      <a href="../../backoffice.php" class="admin">Espace Admin</a>
    </nav>
  </header>

  <!-- Live Status Bar -->
  <div class="live-status-bar">
    <div class="live-indicator">
      <div class="live-dot"></div>
      <span>TABLEAU DE BORD EN DIRECT</span>
    </div>
    <div class="update-info">
      <div class="last-update">
        <i class="fas fa-clock"></i>
        <span>Dernière mise à jour : <span id="lastUpdateTime"><?= $lastUpdate ?></span></span>
      </div>
      <div class="refresh-countdown">
        <i class="fas fa-sync-alt"></i>
        <span>Prochaine mise à jour : <span id="countdown">30</span>s</span>
      </div>
      <button class="refresh-btn" onclick="forceRefresh()">
        <i class="fas fa-redo"></i>
        Actualiser maintenant
      </button>
    </div>
  </div>

  <div class="main-container">
    <!-- Stats Header -->
    <div class="stats-header">
      <h1><i class="fas fa-chart-line"></i> Tableau de Bord Statistiques</h1>
      <p class="subtitle">Données en temps réel • Mise à jour automatique • Analyses détaillées</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-value"><?= number_format($todayStats['total'], 2) ?> €</div>
        <div class="stat-label">Collecté aujourd'hui</div>
        <div class="section-update" style="margin-top: 10px; font-size: 0.8rem;">
          <i class="fas fa-bolt"></i> Mise à jour en direct
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">❤️</div>
        <div class="stat-value"><?= $todayStats['nb_dons'] ?></div>
        <div class="stat-label">Dons du jour</div>
        <div class="section-update" style="margin-top: 10px; font-size: 0.8rem;">
          <i class="fas fa-bolt"></i> Mise à jour en direct
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-value"><?= number_format($todayStats['moyenne'], 2) ?> €</div>
        <div class="stat-label">Moyenne par don</div>
        <div class="section-update" style="margin-top: 10px; font-size: 0.8rem;">
          <i class="fas fa-bolt"></i> Mise à jour en direct
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">🏆</div>
        <div class="stat-value"><?= number_format($totalGeneral, 2) ?> €</div>
        <div class="stat-label">Total général</div>
        <div class="section-update" style="margin-top: 10px; font-size: 0.8rem;">
          <i class="fas fa-bolt"></i> Mise à jour en direct
        </div>
      </div>
    </div>

    <!-- Tabs for Charts -->
    <div class="tabs">
      <button class="tab active" onclick="showTab('tab-trends')">
        <i class="fas fa-chart-line"></i> Tendances
      </button>
      <button class="tab" onclick="showTab('tab-distribution')">
        <i class="fas fa-chart-pie"></i> Distribution
      </button>
      <button class="tab" onclick="showTab('tab-organisations')">
        <i class="fas fa-building"></i> Organisations
      </button>
      <button class="tab" onclick="showTab('tab-donateurs')">
        <i class="fas fa-users"></i> Donateurs
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
              Évolution sur 7 jours
            </div>
            <div class="chart-period">Dernière semaine • Mise à jour en temps réel</div>
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
              Activité par heure
            </div>
            <div class="chart-period">Aujourd'hui • Actualisé toutes les 30 secondes</div>
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
              Distribution des montants
            </div>
            <div class="chart-period">Tous les dons • Mise à jour instantanée</div>
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
              Top organisations
            </div>
            <div class="chart-period">Par montant collecté • Données en direct</div>
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
        <div class="section-header">
          <h2 class="section-title"><i class="fas fa-chart-bar"></i> Performance par organisation</h2>
          <div class="section-update">
            <i class="fas fa-sync-alt fa-spin"></i>
            Données en temps réel
          </div>
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
      <div class="charts-grid">
        <!-- Top Donors -->
        <div class="chart-section">
          <div class="section-header">
            <h2 class="section-title"><i class="fas fa-crown"></i> Top Donateurs du Jour</h2>
            <div class="section-update">
              <i class="fas fa-bolt"></i>
              Classement mis à jour en direct
            </div>
          </div>
          
          <?php if (!empty($todayTopDonors)): ?>
            <div class="donors-list">
              <?php foreach ($todayTopDonors as $index => $donor): ?>
                <div class="donor-item rank-<?= $index + 1 ?>">
                  <div class="donor-info">
                    <div class="donor-rank"><?= $index + 1 ?></div>
                    <div class="donor-avatar">
                      <?= strtoupper(substr($donor['nom_complet'], 0, 1)) ?>
                    </div>
                    <div class="donor-details">
                      <h3><?= htmlspecialchars($donor['nom_complet']) ?></h3>
                      <div class="time">Aujourd'hui • Mise à jour en direct</div>
                    </div>
                  </div>
                  <div class="donor-amount"><?= number_format($donor['total'], 2) ?> €</div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-users"></i>
              <p>Aucun donateur aujourd'hui... Soyez le premier ! 🌟</p>
            </div>
          <?php endif; ?>
        </div>

        <!-- Recent Donations -->
        <div class="chart-section">
          <div class="section-header">
            <h2 class="section-title"><i class="fas fa-bolt"></i> Derniers Dons</h2>
            <div class="section-update">
              <i class="fas fa-clock"></i>
              Flux en temps réel
            </div>
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
                      <span class="anonymous">Donateur anonyme</span>
                    <?php endif; ?>
                    <span class="feed-org"> → <?= htmlspecialchars($don['organisation_nom'] ?? 'N/A') ?></span>
                  </div>
                  <div class="feed-time">
                    <?= date('H:i', strtotime($don['dateDon'])) ?> - <?= date('d/m/Y', strtotime($don['dateDon'])) ?>
                    • Mis à jour en direct
                  </div>
                </div>
                <div class="feed-amount">
                  <?= number_format($don['montant'], 2) ?> €
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer>
    © 2024 Mind Arena — Tableau de bord en temps réel
    <br><small><i class="fas fa-sync-alt"></i> Mise à jour automatique toutes les 30 secondes</small>
  </footer>

  <!-- Update Notification -->
  <div id="updateNotification" class="update-notification" style="display: none;">
    <i class="fas fa-check-circle"></i>
    <span>Tableau de bord mis à jour avec succès !</span>
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
      gradientPurple: (context) => {
        const chart = context.chart;
        const {ctx, chartArea} = chart;
        if (!chartArea) return null;
        
        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        gradient.addColorStop(0, 'rgba(176, 27, 165, 0.2)');
        gradient.addColorStop(1, 'rgba(226, 30, 228, 0.8)');
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
                        label: 'Montant total (€)',
                        data: totals,
                        borderColor: chartColors.purple,
                        backgroundColor: chartColors.gradientPurple,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Nombre de dons',
                        data: counts,
                        borderColor: chartColors.green,
                        backgroundColor: 'rgba(76, 255, 76, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1'
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
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#b01ba5',
                        bodyColor: '#fff',
                        borderColor: '#b01ba5',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y} ${context.datasetIndex === 0 ? '€' : ''}`;
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
                            color: '#ccc'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Montant (€)',
                            color: '#ccc'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ccc',
                            callback: function(value) {
                                return value + ' €';
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Nombre de dons',
                            color: '#ccc'
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            color: '#ccc'
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
                    label: 'Montant par heure (€)',
                    data: totals,
                    backgroundColor: labels.map((_, i) => {
                        const ratio = totals[i] / Math.max(...totals) || 0;
                        return `rgba(176, 27, 165, ${0.3 + ratio * 0.7})`;
                    }),
                    borderColor: chartColors.purple,
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Montant: ${context.parsed.y} €`;
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
                            color: '#ccc'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ccc',
                            callback: function(value) {
                                return value + ' €';
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
                        'rgba(176, 27, 165, 0.8)',
                        'rgba(255, 107, 107, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ],
                    borderColor: [
                        'rgba(76, 255, 76, 1)',
                        'rgba(92, 124, 246, 1)',
                        'rgba(176, 27, 165, 1)',
                        'rgba(255, 107, 107, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 2
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
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label;
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} dons (${percentage}%)`;
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
                    label: 'Montant total (€)',
                    data: totals,
                    backgroundColor: chartColors.gradientPurple,
                    borderColor: chartColors.purple,
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Montant: ${context.parsed.x} €`;
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
                            color: '#ccc',
                            callback: function(value) {
                                return value + ' €';
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ccc',
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
                        label: 'Montant total (€)',
                        data: totals,
                        backgroundColor: chartColors.gradientPurple,
                        borderColor: chartColors.purple,
                        borderWidth: 2,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Moyenne par don (€)',
                        data: averages,
                        backgroundColor: 'rgba(76, 255, 76, 0.6)',
                        borderColor: chartColors.green,
                        borderWidth: 2,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ccc',
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
                            text: 'Montant total (€)',
                            color: '#ccc'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ccc',
                            callback: function(value) {
                                return value + ' €';
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Moyenne (€)',
                            color: '#ccc'
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            color: '#ccc',
                            callback: function(value) {
                                return value + ' €';
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
                countdownElement.style.color = '#ff4757';
                countdownElement.style.textShadow = '0 0 10px #ff4757';
            } else if (countdownValue <= 20) {
                countdownElement.style.color = '#ffa502';
                countdownElement.style.textShadow = '0 0 10px #ffa502';
            } else {
                countdownElement.style.color = '#4cff4c';
                countdownElement.style.textShadow = '0 0 10px #4cff4c';
            }
        }
    }

    // Mise à jour auto
    function performAutoRefresh() {
        showUpdateNotification("Mise à jour automatique en cours...");
        
        // Simuler un chargement
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    // Refresh manuel
    function forceRefresh() {
        showUpdateNotification("Mise à jour manuelle en cours...");
        
        // Réinitialiser le compte à rebours
        startCountdown();
        
        // Mettre à jour l'heure de dernière mise à jour
        const now = new Date();
        const timeString = now.toLocaleTimeString('fr-FR', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('lastUpdateTime').textContent = timeString;
        
        // Rafraîchir les graphiques
        initCharts();
        
        // Afficher la notification de succès
        setTimeout(() => {
            showUpdateNotification("Tableau de bord mis à jour avec succès !");
        }, 500);
    }

    // Notification de mise à jour
    function showUpdateNotification(message) {
        const notification = document.getElementById('updateNotification');
        const messageElement = notification.querySelector('span');
        
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

    // Création des particules
    function createParticles() {
        const container = document.getElementById('particles');
        const particleCount = 20;
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            const size = Math.random() * 10 + 5;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.top = `${Math.random() * 100}%`;
            
            const duration = Math.random() * 20 + 10;
            particle.style.animationDuration = `${duration}s`;
            particle.style.animationDelay = `${Math.random() * 5}s`;
            particle.style.opacity = Math.random() * 0.3 + 0.1;
            
            container.appendChild(particle);
        }
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        createParticles();
        initCharts();
        startCountdown();
        
        // Animation des items du feed
        const feedItems = document.querySelectorAll('.feed-item');
        feedItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
        });
        
        // Mettre à jour l'heure de dernière mise à jour
        const now = new Date();
        const timeString = now.toLocaleTimeString('fr-FR', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('lastUpdateTime').textContent = timeString;
    });
  </script>
</body>
</html>