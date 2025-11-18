<?php
// Page d'accueil du backoffice avec design Material-UI
require_once __DIR__."/Controller/DonController.php";
require_once __DIR__."/Controller/OrganisationController.php";

$donCtrl = new DonController();
$orgCtrl = new OrganisationController();

// Récupérer quelques statistiques pour le dashboard
$dons = $donCtrl->listDon()->fetchAll();
$organisations = $orgCtrl->listOrganisations();

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
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <style>
    
    :root {
      
      --primary-50: #E9C2FF;
      --primary-100: #E0B5FF;
      --primary-200: #D5A7FF;
      --primary-300: #CA9AFF;
      --primary-400: #BF8CFF;
      --primary-500: #CB3CFF;
      --primary-600: #A324CC;
      --primary-700: #7D1A99;
      --primary-800: #552266;
      --primary-900: #3B004D;

      --secondary-500: #00C2FF;
      --secondary-600: #1A9FB3;
      
      --info-50: #F7FAFC;
      --info-100: #D9E1FA;
      --info-200: #D1DBF9;
      --info-300: #AEB9E1;
      --info-400: #7E89AC;
      --info-500: #4A5568;
      --info-600: #343B4F;
      --info-700: #2D3748;
      --info-800: #1A202C;
      --info-900: #171923;
      
      --success-500: #14CA74;
      --warning-500: #FDB52A;
      --error-500: #FF5A65;
      
      --text-primary: #FFFFFF;
      --text-secondary: #D1DBF9;
      --text-disabled: #7E89AC;

      
      --sidebar-width: 280px;
      --header-height: 70px;
      --border-radius: 8px;
      --border-radius-lg: 16px;
    }

    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Work Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--info-900);
      color: var(--text-primary);
      line-height: 1.6;
      font-weight: 400;
    }

    
    .backoffice-container {
      display: flex;
      min-height: 100vh;
    }

    
    .backoffice-sidebar {
      width: var(--sidebar-width);
      background: var(--info-800);
      color: var(--text-primary);
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      transition: all 0.3s ease;
      border-right: 1px solid var(--info-700);
      z-index: 1000;
    }

    .sidebar-header {
      padding: 24px 20px;
      border-bottom: 1px solid var(--info-700);
      text-align: center;
    }

    .sidebar-header h1 {
      font-size: 1.5rem;
      font-weight: 700;
      margin: 0 0 4px 0;
      background: linear-gradient(135deg, var(--primary-500), var(--secondary-500));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .sidebar-header .subtitle {
      color: var(--text-secondary);
      font-size: 0.85rem;
      opacity: 0.8;
    }

    
    .sidebar-nav {
      padding: 24px 0;
    }

    .nav-section {
      padding: 16px 24px 8px;
      color: var(--info-300);
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .nav-item {
      margin: 2px 0;
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 12px 24px;
      color: var(--text-secondary);
      text-decoration: none;
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
      font-weight: 500;
    }

    .nav-link:hover {
      background: rgba(255, 255, 255, 0.05);
      color: var(--text-primary);
      border-left-color: var(--primary-500);
    }

    .nav-link.active {
      background: rgba(203, 60, 255, 0.1);
      color: var(--text-primary);
      border-left-color: var(--primary-500);
    }

    .nav-icon {
      margin-right: 16px;
      font-size: 1.2rem;
      width: 20px;
      text-align: center;
    }

    
    .backoffice-main {
      flex: 1;
      margin-left: var(--sidebar-width);
      transition: all 0.3s ease;
      background: var(--info-900);
    }

    /* Header */
    .main-header {
      background: var(--info-800);
      padding: 0 32px;
      height: var(--header-height);
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--info-700);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .header-title h2 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-secondary);
      font-weight: 500;
    }

    /* Content Area */
    .content-wrapper {
      padding: 32px;
      min-height: calc(100vh - var(--header-height));
    }

    

    /* Cartes de statistiques */
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
      margin-bottom: 40px;
    }

    .stat-card {
      background: var(--info-800);
      border-radius: var(--border-radius-lg);
      padding: 32px;
      text-align: center;
      border: 1px solid var(--info-700);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-500), var(--secondary-500));
    }

    .stat-card:hover {
      transform: translateY(-8px);
      border-color: var(--primary-500);
    }

    .stat-icon {
      font-size: 3rem;
      margin-bottom: 20px;
      display: block;
      background: linear-gradient(135deg, var(--primary-500), var(--secondary-500));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .stat-value {
      font-size: 2.5rem;
      font-weight: 900;
      margin-bottom: 8px;
      background: linear-gradient(135deg, var(--primary-500), var(--secondary-500));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .stat-title {
      font-size: 1.25rem;
      font-weight: 700;
      margin-bottom: 8px;
      color: var(--text-primary);
    }

    .stat-description {
      color: var(--text-secondary);
      margin-bottom: 24px;
      font-size: 1rem;
    }

    /* Boutons (Style MUI Button) */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      border: none;
      border-radius: var(--border-radius);
      font-weight: 500;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 0.95rem;
      letter-spacing: 0.5px;
      text-transform: initial;
      font-family: inherit;
    }

    .btn:disabled {
      color: var(--text-disabled);
      cursor: not-allowed;
      opacity: 0.6;
    }

    .btn-primary {
      background: linear-gradient(128.49deg, var(--primary-500) 19.86%, var(--primary-600) 68.34%);
      color: var(--text-primary);
    }

    .btn-primary:hover:not(:disabled) {
      background: linear-gradient(128.49deg, var(--primary-600) 19.86%, var(--primary-700) 68.34%);
      transform: translateY(-2px);
    }

    .btn-secondary {
      background: var(--info-600);
      color: var(--text-primary);
    }

    .btn-secondary:hover:not(:disabled) {
      background: var(--info-500);
    }

    /* Actions rapides */
    .quick-actions {
      background: var(--info-800);
      padding: 40px 0;
    }

    .actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .action-card {
      background: rgba(255, 255, 255, 0.05);
      padding: 32px;
      border-radius: var(--border-radius-lg);
      border: 1px solid var(--info-700);
      transition: all 0.3s ease;
      text-align: center;
    }

    .action-card:hover {
      transform: translateY(-4px);
      border-color: var(--primary-500);
      background: rgba(255, 255, 255, 0.08);
    }

    .action-icon {
      font-size: 2.5rem;
      margin-bottom: 16px;
      display: block;
      color: var(--primary-500);
    }

    .action-title {
      font-size: 1.3rem;
      color: var(--text-primary);
      margin-bottom: 12px;
      font-weight: 700;
    }

    .action-description {
      color: var(--text-secondary);
      margin-bottom: 20px;
      font-size: 0.95rem;
      line-height: 1.5;
    }

    /* Information Box avec opacité contrôlée */
.info-box {
  background: linear-gradient(135deg, 
    rgba(183, 42, 253, 0.85), 
    rgba(148, 19, 131, 0.85));
  border-radius: var(--border-radius);
  padding: 24px;
  margin: 32px auto;
  max-width: 800px;
  text-align: center;
  border: 1px solid rgba(186, 42, 253, 0.5);
  opacity: 0.95;
  transition: all 0.3s ease;
}

.info-box:hover {
  opacity: 1;
  transform: translateY(-2px);
  box-shadow: 0 12px 40px 0 rgba(253, 42, 253, 0.3);
}

.info-box h3 {
  color: var(--text-primary);
  margin-bottom: 12px;
  font-size: 1.3rem;
  font-weight: 700;
  opacity: 0.95;
}

.info-box p {
  color: var(--text-primary);
  margin: 0;
  font-size: 1rem;
  line-height: 1.6;
  opacity: 0.9;
}
    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .backoffice-sidebar {
        transform: translateX(-100%);
      }
      
      .backoffice-sidebar.active {
        transform: translateX(0);
      }
      
      .backoffice-main {
        margin-left: 0;
      }
      
      .content-wrapper {
        padding: 20px;
      }
      
      .dashboard-grid {
        grid-template-columns: 1fr;
      }
      
      .actions-grid {
        grid-template-columns: 1fr;
      }
      
      .main-header {
        padding: 0 20px;
      }
      
      .stat-card {
        padding: 24px;
      }
    }
  </style>
</head>

<body>
  <div class="backoffice-container">
    <!-- Sidebar -->
    <nav class="backoffice-sidebar" id="sidebar">
      <div class="sidebar-header">
        <h1>🚀 Mind Arena</h1>
        <div class="subtitle">Backoffice Admin</div>
      </div>
      
      <div class="sidebar-nav">
        <div class="nav-section">Navigation Principale</div>
        
        <div class="nav-item">
          <a href="#dashboard" class="nav-link active">
            <span class="nav-icon">📊</span>
            Tableau de bord
          </a>
        </div>
        
        <div class="nav-section">Gestion des Dons</div>
        
        <div class="nav-item">
          <a href="View/backoffice/don/donList.php" class="nav-link">
            <span class="nav-icon">💰</span>
            Liste des dons
          </a>
        </div>
        <div class="nav-item">
          <a href="View/backoffice/don/addDon.php" class="nav-link">
            <span class="nav-icon">➕</span>
            Nouveau don
          </a>
        </div>
        
        <div class="nav-section">Gestion des Organisations</div>
        
        <div class="nav-item">
          <a href="View/backoffice/organisation/organisationList.php" class="nav-link">
            <span class="nav-icon">🏢</span>
            Organisations
          </a>
        </div>
        <div class="nav-item">
          <a href="View/backoffice/organisation/addOrganisation.php" class="nav-link">
            <span class="nav-icon">➕</span>
            Nouvelle organisation
          </a>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="backoffice-main">
      <!-- Header -->
      <header class="main-header">
        <div class="header-title">
          <h2>Administration Mind Arena</h2>
        </div>
        <div class="header-actions">
          <div class="user-info">
            <span>👤 Administrateur</span>
          </div>
          <a href="View/frontoffice/index.php" class="btn btn-secondary">
            🌐 Voir le site
          </a>
        </div>
      </header>

      <!-- Content Area -->
      <main class="content-wrapper">
        <!-- Information sur l'immuabilité des dons -->
        <div class="info-box">
          <h3>⚠️ Information Importante</h3>
          <p>
            <strong>Les dons ne peuvent pas être modifiés</strong> après enregistrement pour garantir l'intégrité des données financières.<br>
            En cas d'erreur, veuillez supprimer le don et en créer un nouveau.
          </p>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
          <!-- Carte Statistiques Dons -->
          <div class="stat-card">
            <span class="stat-icon">💰</span>
            <div class="stat-value"><?= number_format($totalDons, 2) ?> €</div>
            <div class="stat-title">Total des Dons</div>
            <div class="stat-description">
              Montant total collecté depuis le lancement
            </div>
            <a href="View/backoffice/don/donList.php" class="btn btn-primary">Voir tous les dons</a>
          </div>

          <!-- Carte Organisations -->
          <div class="stat-card">
            <span class="stat-icon">🏢</span>
            <div class="stat-value"><?= $totalOrganisations ?></div>
            <div class="stat-title">Organisations</div>
            <div class="stat-description">
              Nombre total d'associations partenaires
            </div>
            <a href="View/backoffice/organisation/organisationList.php" class="btn btn-primary">Gérer les organisations</a>
          </div>

          <!-- Carte Moyenne -->
          <div class="stat-card">
            <span class="stat-icon">📊</span>
            <div class="stat-value"><?= number_format($moyenneDon, 2) ?> €</div>
            <div class="stat-title">Moyenne par Organisation</div>
            <div class="stat-description">
              Don moyen collecté par association
            </div>
            <a href="View/backoffice/organisation/organisationList.php" class="btn btn-primary">Voir les statistiques</a>
          </div>
        </div>

        <!-- Actions Rapides -->
        <section class="quick-actions">
          <h2 style="color: var(--text-primary); margin-bottom: 40px; font-size: 2rem; font-weight: 700; text-align: center;">
            Actions Rapides
          </h2>
          
          <div class="actions-grid">
            <!-- Action Ajouter Don -->
            <div class="action-card">
              <span class="action-icon">➕</span>
              <div class="action-title">Ajouter un Don</div>
              <div class="action-description">
                Enregistrer manuellement un nouveau don pour une organisation
              </div>
              <a href="View/backoffice/don/addDon.php" class="btn btn-primary">Nouveau Don</a>
            </div>

            <!-- Action Ajouter Organisation -->
            <div class="action-card">
              <span class="action-icon">🏢</span>
              <div class="action-title">Nouvelle Organisation</div>
              <div class="action-description">
                Ajouter une nouvelle association partenaire à la plateforme
              </div>
              <a href="View/backoffice/organisation/addOrganisation.php" class="btn btn-primary">Nouvelle Organisation</a>
            </div>

            <!-- Action Voir Site -->
            <div class="action-card">
              <span class="action-icon">🌐</span>
              <div class="action-title">Voir le Site</div>
              <div class="action-description">
                Accéder à la version publique pour tester l'expérience utilisateur
              </div>
              <a href="View/frontoffice/index.php" class="btn btn-secondary">Visiter le Site</a>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script>
    // Animation des cartes au chargement
    document.addEventListener('DOMContentLoaded', function() {
      // Animation des cartes de dashboard
      const statCards = document.querySelectorAll('.stat-card');
      statCards.forEach((card, index) => {
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