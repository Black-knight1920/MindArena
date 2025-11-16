<?php
// Page d'accueil du backoffice avec design frontoffice
require_once __DIR__."/Controller/DonController.php";
require_once __DIR__."/Controller/OrganisationController.php";

$donCtrl = new DonController();
$orgCtrl = new OrganisationController();

// Récupérer quelques statistiques pour le dashboard
$dons = $donCtrl->listDon()->fetchAll();
$organisations = $orgCtrl->listOrganisations(); // CHANGÉ ICI

$totalDons = 0;
foreach ($dons as $don) {
    $totalDons += $don['montant'];
}

$totalOrganisations = count($organisations);
$moyenneDon = $totalOrganisations > 0 ? $totalDons / $totalOrganisations : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Backoffice - Mind Arena</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
  <style>
     /* ----- Global ----- */
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

    /* ----- Hero Section ----- */
    .hero {
        text-align: center;
        padding: 150px 20px 80px;
        background: linear-gradient(45deg, #501755 0%, #2d1854 100%);
        margin-top: 80px;
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

    /* ----- Dashboard Grid ----- */
    .dashboard {
        padding: 80px 20px;
        background: linear-gradient(45deg,#501755 0%,#2d1854 100%);
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .dashboard-card {
        background: rgba(255,255,255,.08);
        border: 2px solid #b01ba5;
        box-shadow: 0 0 15px rgba(176,27,165,.4);
        border-radius: 16px;
        padding: 2.5rem;
        text-align: center;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(45deg, #b01ba5, #667eea);
    }

    .dashboard-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(176,27,165,.6);
    }

    .card-icon {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        display: block;
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #fff;
    }

    .card-stats {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 1rem;
        background: linear-gradient(45deg, #fff, #b01ba5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .card-description {
        color: rgba(255,255,255,.8);
        margin-bottom: 2rem;
        font-size: 1rem;
    }

    /* ----- Buttons ----- */
    .btn {
        display: inline-block;
        background: #b01ba5;
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        padding: 1rem 2rem;
        border: none;
        text-decoration: none;
        border-radius: 8px;
        box-shadow: 0 0 9px 3px rgba(226,30,228,.24);
        transition: transform 0.3s, box-shadow 0.3s;
        font-size: 1rem;
        cursor: pointer;
        margin: 5px;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 15px 5px rgba(226,30,228,.4);
        background: #d93ee7;
    }

    .btn-don {
        background: #fff;
        color: #081624;
    }

    .btn-don:hover {
        background: #f0f0f0;
    }

    .btn-org {
        background: #4CAF50;
        box-shadow: 0 0 9px 3px rgba(76,175,80,.24);
    }

    .btn-org:hover {
        background: #45a049;
        box-shadow: 0 0 15px 5px rgba(76,175,80,.4);
    }

    .btn-site {
        background: #2196F3;
        box-shadow: 0 0 9px 3px rgba(33,150,243,.24);
    }

    .btn-site:hover {
        background: #1976D2;
        box-shadow: 0 0 15px 5px rgba(33,150,243,.4);
    }

    /* ----- Quick Actions ----- */
    .quick-actions {
        background: #2d1854;
        padding: 80px 20px;
        text-align: center;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .action-card {
        background: rgba(255,255,255,.1);
        padding: 30px;
        border-radius: 15px;
        border: 1px solid #b01ba5;
        transition: transform 0.3s;
    }

    .action-card:hover {
        transform: scale(1.05);
    }

    .action-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        display: block;
    }

    .action-title {
        font-size: 1.3rem;
        color: #b01ba5;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .action-description {
        color: #ccc;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    /* ----- Information Box ----- */
    .info-box {
        background: rgba(255, 193, 7, 0.1);
        border: 2px solid #ffc107;
        border-radius: 10px;
        padding: 20px;
        margin: 20px auto;
        max-width: 800px;
        text-align: center;
    }

    .info-box h3 {
        color: #ffc107;
        margin-bottom: 10px;
        font-size: 1.2rem;
    }

    .info-box p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        font-size: 0.95rem;
    }

    /* ----- Footer ----- */
    footer {
        background: #190d36;
        text-align: center;
        padding: 2rem 1rem;
        font-size: .9rem;
        color: #aaa;
        line-height: 1.8;
    }

    /* ----- Responsive ----- */
    @media (max-width: 768px) {
        header {
            padding: 15px 20px;
            flex-direction: column;
            gap: 15px;
        }
        
        nav a {
            margin: 0 10px;
        }
        
        .hero h2 {
            font-size: 2.5rem;
        }
        
        .hero {
            padding: 120px 20px 60px;
        }
        
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .actions-grid {
            grid-template-columns: 1fr;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            font-size: 0.9rem;
        }
    }



 
  </style>
</head>

<body>

  <header>
    <h1>🚀 Backoffice Mind Arena</h1>
    <nav>
      <a href="#dashboard">Tableau de bord</a>
      <a href="#actions">Actions rapides</a>
      <a href="View/frontoffice/index.php">Site public</a>
    </nav>
  </header>

  <section class="hero" id="dashboard">
    <h2>Administration Mind Arena</h2>
    <p>
      Gérez l'ensemble des dons et organisations depuis votre espace administrateur. 
      Suivez les statistiques et optimisez votre plateforme de dons gaming.
    </p>
  </section>

  <!-- Information sur l'immuabilité des dons -->
  <div class="info-box">
    <h3>⚠️ Information Importante</h3>
    <p>
      <strong>Les dons ne peuvent pas être modifiés</strong> après enregistrement pour garantir l'intégrité des données financières.<br>
      En cas d'erreur, veuillez supprimer le don et en créer un nouveau.
    </p>
  </div>

  <section class="dashboard">
    <div class="dashboard-grid">
      <!-- Carte Statistiques Dons -->
      <div class="dashboard-card">
        <span class="card-icon">💰</span>
        <div class="card-stats"><?= number_format($totalDons, 2) ?> €</div>
        <div class="card-title">Total des Dons</div>
        <div class="card-description">
          Montant total collecté depuis le lancement de la plateforme
        </div>
        <a href="View/backoffice/don/donList.php" class="btn">Voir tous les dons</a>
      </div>

      <!-- Carte Organisations -->
      <div class="dashboard-card">
        <span class="card-icon">🏢</span>
        <div class="card-stats"><?= $totalOrganisations ?></div>
        <div class="card-title">Organisations</div>
        <div class="card-description">
          Nombre total d'associations partenaires sur la plateforme
        </div>
        <a href="View/backoffice/organisation/organisationList.php" class="btn btn-org">Gérer les organisations</a>
      </div>

      <!-- Carte Moyenne -->
      <div class="dashboard-card">
        <span class="card-icon">📊</span>
        <div class="card-stats"><?= number_format($moyenneDon, 2) ?> €</div>
        <div class="card-title">Moyenne par Organisation</div>
        <div class="card-description">
          Don moyen collecté par association partenaire
        </div>
        <a href="View/backoffice/organisation/organisationList.php" class="btn">Voir les statistiques</a>
      </div>
    </div>
  </section>

  <section class="quick-actions" id="actions">
    <h2 style="color: white; margin-bottom: 50px; font-size: 2.5rem;">Actions Rapides</h2>
    
    <div class="actions-grid">
      <!-- Action Ajouter Don -->
      <div class="action-card">
        <span class="action-icon">➕</span>
        <div class="action-title">Ajouter un Don</div>
        <div class="action-description">
          Enregistrer manuellement un nouveau don pour une organisation
        </div>
        <a href="View/backoffice/don/addDon.php" class="btn btn-don">Nouveau Don</a>
      </div>

      <!-- Action Ajouter Organisation -->
      <div class="action-card">
        <span class="action-icon">🏢</span>
        <div class="action-title">Nouvelle Organisation</div>
        <div class="action-description">
          Ajouter une nouvelle association partenaire à la plateforme
        </div>
        <a href="View/backoffice/organisation/addOrganisation.php" class="btn btn-org">Nouvelle Organisation</a>
      </div>

      <!-- Action Voir Site -->
      <div class="action-card">
        <span class="action-icon">🌐</span>
        <div class="action-title">Voir le Site</div>
        <div class="action-description">
          Accéder à la version publique pour tester l'expérience utilisateur
        </div>
        <a href="View/frontoffice/index.php" class="btn btn-site">Visiter le Site</a>
      </div>
    </div>
  </section>

  <footer>
    © 2024 Mind Arena — Backoffice Administratif
    <br><small>Plateforme de dons gaming solidaire - Version Admin</small>
  </footer>

  <script>
    // Animation des cartes au chargement
    document.addEventListener('DOMContentLoaded', function() {
      // Animation des cartes de dashboard
      const dashboardCards = document.querySelectorAll('.dashboard-card');
      dashboardCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(50px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.6s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 200);
      });

      // Animation des cartes d'actions
      const actionCards = document.querySelectorAll('.action-card');
      actionCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateX(-50px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.6s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateX(0)';
        }, index * 200 + 400);
      });

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
    });
  </script>
</body>
</html>