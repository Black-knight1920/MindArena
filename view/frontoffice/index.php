<?php
require_once __DIR__."/../../Controller/OrganisationController.php";
require_once __DIR__."/../../Controller/DonController.php";
require_once __DIR__."/../../Model/DonateurClasse.php";

$orgCtrl = new OrganisationController();
$donCtrl = new DonController();
$organisations = $orgCtrl->listOrganisations();

// Récupérer le vrai classement des donateurs
$classement = $donCtrl->getClassementDonateurs(3); // Top 3 seulement pour l'accueil

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
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mind Arena - Accueil</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
    }

    .org-card {
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .org-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(176, 27, 165, 0.3);
    }
    .org-link-indicator {
      position: absolute;
      top: 10px;
      right: 10px;
      background: #4cff4c;
      color: #000;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: bold;
      cursor: pointer;
      z-index: 2;
    }
    .visit-text {
      color: #b01ba5;
      font-size: 0.9em;
      margin-top: 8px;
      font-weight: bold;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .org-card:hover .visit-text {
      opacity: 1;
    }
    .btn-don-org {
      background: #b01ba5;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s ease;
      margin-top: 15px;
      font-size: 1em;
      text-decoration: none;
      display: block;
      text-align: center;
    }
    .btn-don-org:hover {
      background: #d93ee7;
      transform: translateY(-2px);
    }
    .btn-details {
      background: transparent;
      color: #b01ba5;
      border: 2px solid #b01ba5;
      padding: 10px 24px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s ease;
      margin-top: 8px;
      font-size: 0.9em;
      text-decoration: none;
      display: block;
      text-align: center;
    }
    .btn-details:hover {
      background: #b01ba5;
      color: white;
      transform: translateY(-2px);
    }
    .buttons-container {
      margin-top: 15px;
      margin-top: auto;
      padding-top: 15px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Styles pour les images d'organisations */
    .org-image-container {
      width: 100%;
      height: 180px;
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 15px;
      position: relative;
      background: rgba(0, 0, 0, 0.2);
    }
    
    .org-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .org-card:hover .org-image {
      transform: scale(1.05);
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
    }
    
    /* Barre de progression améliorée */
    .progress-section {
      margin: 12px 0;
      background: rgba(0, 0, 0, 0.2);
      padding: 12px;
      border-radius: 10px;
      border: 1px solid rgba(176, 27, 165, 0.2);
    }
    
    .progress-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }
    
    .progress-label {
      color: #ccc;
      font-size: 0.9rem;
      font-weight: 500;
    }
    
    .progress-percentage {
      color: #4cff4c;
      font-weight: bold;
      font-size: 1.1rem;
      background: rgba(76, 255, 76, 0.1);
      padding: 3px 8px;
      border-radius: 20px;
    }
    
    .progress-bar-container {
      width: 100%;
      height: 10px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 5px;
      overflow: hidden;
      margin: 8px 0;
    }
    
    .progress-bar-fill {
      height: 100%;
      background: linear-gradient(90deg, #4cff4c, #b01ba5);
      border-radius: 5px;
      transition: width 0.5s ease;
    }
    
    .progress-details {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 8px;
    }
    
    .progress-current {
      color: #4cff4c;
      font-weight: bold;
      font-size: 1rem;
    }
    
    .progress-goal {
      color: #b01ba5;
      font-weight: bold;
      font-size: 0.9rem;
      text-align: right;
    }
    
    .progress-remaining {
      color: #ff9900;
      font-size: 0.8rem;
      margin-top: 4px;
      text-align: center;
      font-style: italic;
    }
    
    /* Indicateur de niveau */
    .progress-level {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      background: rgba(16, 185, 129, 0.1);
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      color: #10b981;
      font-weight: 600;
      margin-top: 5px;
    }
    
    .org-stats {
      background: rgba(255, 255, 255, 0.05);
      padding: 10px;
      border-radius: 8px;
      margin-top: 10px;
      font-size: 0.9rem;
    }
    
    .stat-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
    }
    
    .stat-item:last-child {
      margin-bottom: 0;
    }
    
    .stat-label {
      color: #ccc;
    }
    
    .stat-value {
      color: #4cff4c;
      font-weight: bold;
    }
    
    .org-content {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .org-description {
      flex: 1;
      margin-bottom: 15px;
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

    /* Nouveau style pour la navigation */
    nav {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    nav a {
        color: #fff;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    nav a:hover { 
        color: #b01ba5; 
    }

    /* Style spécifique pour le lien Live Stats */
    nav a[href="stats-live.php"] {
        color: #b01ba5 !important;
        font-weight: bold;
    }

    nav a[href="stats-live.php"]:hover {
        color: #d93ee7 !important;
    }

    /* ----- Hero Section ----- */
    .hero {
        text-align: center;
        padding: 150px 20px 80px;
        background: linear-gradient(45deg, #501755 0%, #2d1854 100%);
        margin-top: 80px;
        width: 100%;
        left: 0;
    }

    .hero h2 {
        font-size: 3.5rem;
        font-weight: 900;
        margin: 0 0 20px 0;
        background: linear-gradient(45deg, #fff, #b01ba5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero p {
        max-width: 600px;
        margin: 1rem auto 2rem;
        font-size: 1.2rem;
        color: rgba(255,255,255,.8);
    }

    .btn-don {
        display: inline-block;
        background: #fff;
        color: #081624;
        font-weight: 700;
        text-transform: uppercase;
        padding: 1rem 2rem;
        border: none;
        text-decoration: none;
        border-radius: 8px;
        box-shadow: 0 0 9px 3px rgba(226,30,228,.24);
        transition: transform 0.3s, box-shadow 0.3s;
        font-size: 1.1rem;
    }

    .btn-don:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 15px 5px rgba(226,30,228,.4);
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

    /* Classement Section */
    .classement-section {
        background: #2d1854;
        padding: 80px 20px;
        text-align: center;
        width: 100%;
        left: 0;
    }

    .classement-preview {
        background: rgba(255,255,255,0.1);
        padding: 25px;
        border-radius: 15px;
        border: 1px solid #b01ba5;
        max-width: 600px;
        margin: 30px auto;
        text-align: center;
    }

    .classement-list {
        text-align: left;
        margin: 20px 0;
    }

    .classement-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .classement-item:last-child {
        border-bottom: none;
    }

    .rang {
        font-weight: bold;
        color: #b01ba5;
        margin-right: 15px;
        min-width: 40px;
    }

    .donateur-info {
        flex-grow: 1;
        text-align: left;
    }

    .donateur-nom {
        font-weight: bold;
    }

    .donateur-montant {
        color: #4cff4c;
        font-weight: bold;
        min-width: 100px;
        text-align: right;
    }

    .anonyme {
        color: #ccc;
        font-style: italic;
    }

    .btn-classement {
        background: #b01ba5;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        margin-top: 15px;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-classement:hover {
        background: #d93ee7;
        transform: translateY(-2px);
    }

    .empty-classement {
        text-align: center;
        padding: 20px;
        color: #ccc;
        font-style: italic;
    }
    
    /* Section organisations */
    #organisations {
        background: #2d1854; 
        padding: 80px 20px; 
        text-align: center; 
        width: 100%; 
        left: 0;
    }
    
    .organisations-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .organisation-card {
        background: rgba(255,255,255,0.1); 
        padding: 25px; 
        border-radius: 15px; 
        width: 350px; 
        border: 1px solid #b01ba5; 
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    /* Style pour les titres */
    h2.section-title {
        color: white; 
        margin-bottom: 50px; 
        font-size: 2.5rem;
    }
    
    .organisation-title {
        color: #b01ba5; 
        margin-bottom: 15px; 
        font-size: 1.3rem;
        min-height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    
    .organisation-description {
        color: #ccc; 
        font-size: 0.95em; 
        margin-bottom: 15px; 
        min-height: 80px;
        line-height: 1.4;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .organisation-card {
            width: 100%;
            max-width: 400px;
        }
        
        .hero h2 {
            font-size: 2.5rem;
        }
        
        header {
            padding: 15px 20px;
            flex-direction: column;
            gap: 15px;
        }
        
        nav {
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }
        
        .progress-details {
            flex-direction: column;
            gap: 8px;
            align-items: flex-start;
        }
        
        .progress-goal {
            text-align: left;
        }
    }
  </style>
</head>

<body>

  <header>
    <h1>🎮 Mind Arena Magazine</h1>
    <!-- Nouvelle navigation insérée ici -->
    <nav>
        <a href="index.php">Accueil</a>
        <a href="classementDonateurs.php">Classement</a>
        <a href="stats-live.php" style="color: #b01ba5;">
            <i class="bi bi-graph-up"></i> Live Stats
        </a>
        <a href="../../backoffice.php">Admin</a>
    </nav>
  </header>

  <section class="hero" id="accueil">
    <h2>Game For Good !</h2>
    <p>
      Bienvenue sur le portail Mind Arena ! Jouez, gagnez de l'XP et convertissez-le en dons pour des associations caritatives. 
      Votre gaming a du sens !
    </p>
  </section>

  <section id="classement" class="classement-section">
    <h2 class="section-title">🏆 Classement des Donateurs</h2>
    <p style="color: #ccc; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
      Découvrez les donateurs les plus généreux de notre communauté et motivez-vous à monter dans le classement !
    </p>
    
    <div class="classement-preview">
        <h3 style="color: #b01ba5; margin-bottom: 20px;">Top Donateurs</h3>
        <div class="classement-list">
            <?php if (empty($classement)): ?>
                <div class="empty-classement">
                    Aucun donateur pour le moment. Soyez le premier !
                </div>
            <?php else: ?>
                <?php foreach ($classement as $index => $donateur): 
                    // Gestion des noms manquants ou incomplets
                    $prenom = trim($donateur['prenom'] ?? '');
                    $nom = trim($donateur['nom'] ?? '');
                    
                    $nomComplet = '';
                    if (!empty($prenom) && !empty($nom)) {
                        $nomComplet = htmlspecialchars($prenom . ' ' . $nom);
                    } elseif (!empty($prenom)) {
                        $nomComplet = htmlspecialchars($prenom);
                    } elseif (!empty($nom)) {
                        $nomComplet = htmlspecialchars($nom);
                    } else {
                        $nomComplet = '<span class="anonyme">Donateur anonyme</span>';
                    }

                    // Médaille selon le rang
                    $medaille = '';
                    if ($index === 0) $medaille = '🥇';
                    elseif ($index === 1) $medaille = '🥈';
                    elseif ($index === 2) $medaille = '🥉';
                ?>
                <div class="classement-item">
                    <span class="rang"><?= $medaille ?> #<?= $index + 1 ?></span>
                    <div class="donateur-info">
                        <span class="donateur-nom"><?= $nomComplet ?></span>
                    </div>
                    <span class="donateur-montant"><?= number_format($donateur['total_dons'], 2) ?> €</span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="classementDonateurs.php" class="btn-classement">
            📊 Voir le classement complet
        </a>
    </div>
  </section>

  <section id="organisations">
    <h2 class="section-title">Nos Associations Partenaires</h2>
    <div class="organisations-container">
      <?php 
      $index = 0;
      foreach ($organisations as $org): 
        $montantTotal = $org['montant_total'] ?? 0;
        
        // Utiliser l'objectif spécifique à l'organisation ou un défaut
        $orgId = $org['id'] ?? 0;
        $objectif = $objectifsParOrganisation[$orgId] ?? 5000; // 5000€ par défaut si non défini
        
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
        <div class="organisation-card">
          
          <?php if (!empty($org['website_url'])): ?>
            <div class="org-link-indicator" title="Site web disponible" 
                 onclick="window.open('<?= htmlspecialchars($org['website_url']) ?>', '_blank')">
                 🔗
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
            <h3 class="organisation-title">
              <?= htmlspecialchars($org['nom']) ?>
            </h3>
            
            <div class="org-description">
              <p class="organisation-description">
                <?= htmlspecialchars(substr($org['description'], 0, 100)) ?>...
              </p>
            </div>
            
            <!-- Barre de progression avec mêmes objectifs que organisationDons.php -->
            <div class="progress-section">
              <div class="progress-header">
                <span class="progress-label">Progression</span>
                <span class="progress-percentage"><?= number_format($pourcentage, 1) ?>%</span>
              </div>
              
              <div class="progress-bar-container">
                <div class="progress-bar-fill" 
                     data-percentage="<?= $pourcentage ?>" 
                     style="width: <?= $pourcentage ?>%"></div>
              </div>
              
              <div class="progress-details">
                <span class="progress-current"><?= number_format($montantTotal, 2) ?> €</span>
                <span class="progress-goal">Objectif : <?= number_format($objectif, 2) ?> €</span>
              </div>
              
              <?php if ($montantRestant > 0): ?>
                <div class="progress-remaining">
                  <i class="bi bi-arrow-up-right"></i> Il reste <?= number_format($montantRestant, 2) ?> € à collecter
                </div>
              <?php else: ?>
                <div class="progress-remaining" style="color: #4cff4c;">
                  <i class="bi bi-trophy-fill"></i> Objectif atteint !
                </div>
              <?php endif; ?>
              
              <!-- Niveau de progression -->
              <div style="margin-top: 8px;">
                <span class="progress-level">
                  <?php if ($pourcentage >= 100): ?>
                    <i class="bi bi-trophy-fill" style="color: gold;"></i> Objectif atteint
                  <?php elseif ($pourcentage >= 75): ?>
                    <i class="bi bi-star-fill" style="color: gold;"></i> Niveau Expert
                  <?php elseif ($pourcentage >= 50): ?>
                    <i class="bi bi-star-half" style="color: gold;"></i> Niveau Avancé
                  <?php elseif ($pourcentage >= 25): ?>
                    <i class="bi bi-arrow-up-right" style="color: #ff9900;"></i> Niveau Intermédiaire
                  <?php else: ?>
                    <i class="bi bi-arrow-clockwise" style="color: #ccc;"></i> Niveau Débutant
                  <?php endif; ?>
                </span>
              </div>
            </div>
            
            <!-- Statistiques additionnelles -->
            <div class="org-stats">
              <div class="stat-item">
                <span class="stat-label">Dons reçus :</span>
                <span class="stat-value">
                  <?php
                  // Pour obtenir le nombre de dons, vous devriez appeler une méthode du contrôleur
                  // Pour l'instant, utilisons un placeholder
                  ?>
                  <?= isset($org['nb_dons']) ? $org['nb_dons'] : '?' ?>
                </span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Objectif :</span>
                <span class="stat-value"><?= number_format($objectif, 0) ?> €</span>
              </div>
            </div>
            
            <div class="buttons-container">
              <!-- BOUTON DE DON -->
              <a href="addDon.php?orgId=<?= $org['id'] ?>" class="btn-don-org">
                🎁 Faire un don
              </a>
              
              <!-- BOUTON DÉTAILS QUI OUVRE LE SITE DE L'ORGANISATION -->
              <?php if (!empty($org['website_url'])): ?>
                <button class="btn-details" onclick="window.open('<?= htmlspecialchars($org['website_url']) ?>', '_blank')">
                  <i class="bi bi-globe"></i> Visiter le site
                </button>
              <?php else: ?>
                <button class="btn-details" style="opacity: 0.5; cursor: not-allowed;" disabled>
                  <i class="bi bi-globe"></i> Site non disponible
                </button>
              <?php endif; ?>
            </div>
            
            <?php if (!empty($org['website_url'])): ?>
              <div class="visit-text">
                Cliquez 🔗 pour visiter le site
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php 
        $index++;
      endforeach; ?>
    </div>
  </section>

  <footer>
    © 2024 Mind Arena — Plateforme de dons gaming solidaire
    <br><small>Jouez utile, donnez intelligemment</small>
  </footer>

  <script>
    // Animation au scroll
    document.addEventListener('DOMContentLoaded', function() {
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

      // Animation des cartes d'organisations
      const orgCards = document.querySelectorAll('.organisation-card');
      orgCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.6s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 200);
      });

      // Animation des barres de progression au chargement
      const progressBars = document.querySelectorAll('.progress-bar-fill');
      progressBars.forEach(bar => {
        const percentage = bar.getAttribute('data-percentage') || 0;
        bar.style.width = '0';
        setTimeout(() => {
          bar.style.transition = 'width 1.5s cubic-bezier(0.4, 0, 0.2, 1)';
          bar.style.width = percentage + '%';
        }, 500 + Math.random() * 300);
      });

      // Animation des boutons au hover
      document.querySelectorAll('.btn-details').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
          if (!this.disabled) {
            this.style.boxShadow = '0 5px 15px rgba(176, 27, 165, 0.4)';
          }
        });
        
        btn.addEventListener('mouseleave', function() {
          this.style.boxShadow = 'none';
        });
      });
      
      // Préchargement des images
      const images = document.querySelectorAll('.org-image');
      images.forEach(img => {
        const tempImg = new Image();
        tempImg.src = img.src;
      });
    });

    // Fonction pour ouvrir le site avec confirmation
    function openOrganizationSite(url, orgName) {
      if (confirm(`Voulez-vous visiter le site de ${orgName} ?`)) {
        window.open(url, '_blank');
      }
    }
  </script>
</body>
</html>