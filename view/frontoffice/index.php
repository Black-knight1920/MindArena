<?php
require_once __DIR__."/../../Controller/OrganisationController.php";

$orgCtrl = new OrganisationController();
$organisations = $orgCtrl->listOrganisations();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mind Arena - Accueil</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .org-card {
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
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
    }
  </style>
</head>

<body>

  <header>
    <h1>🎮 Mind Arena Magazine</h1>
    <nav>
      <a href="#accueil">Accueil</a>
      <a href="addDon.php">Faire un don</a>
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
    <a href="addDon.php" class="btn-don">🎁 Faire un don</a>
  </section>

  <section id="organisations" style="background: #2d1854; padding: 80px 20px; text-align: center;">
    <h2 style="color: white; margin-bottom: 50px; font-size: 2.5rem;">Nos Associations Partenaires</h2>
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; max-width: 1200px; margin: 0 auto;">
      <?php foreach ($organisations as $org): ?>
        <div class="org-card" 
             style="background: rgba(255,255,255,0.1); padding: 25px; border-radius: 15px; width: 320px; border: 1px solid #b01ba5; position: relative;">
          
          <?php if (!empty($org['website_url'])): ?>
            <div class="org-link-indicator" title="Site web disponible" 
                 onclick="window.open('<?= htmlspecialchars($org['website_url']) ?>', '_blank')">
                 🔗
            </div>
          <?php endif; ?>
          
          <h3 style="color: #b01ba5; margin-bottom: 15px; font-size: 1.3rem;">
            <?= htmlspecialchars($org['nom']) ?>
          </h3>
          <p style="color: #ccc; font-size: 0.95em; margin-bottom: 15px; min-height: 60px;">
            <?= htmlspecialchars(substr($org['description'], 0, 100)) ?>...
          </p>
          <div style="color: #4cff4c; font-weight: bold; font-size: 1.1rem; margin-bottom: 15px;">
            <?= number_format($org['montant_total'] ?? 0, 2) ?> € collectés
          </div>
          
          <div class="buttons-container">
            <!-- BOUTON DE DON QUI REDIRIGE VERS addDon.php -->
            <a href="addDon.php?orgId=<?= $org['id'] ?>" class="btn-don-org">
              🎁 Faire un don
            </a>
            
            <!-- BOUTON DÉTAILS QUI OUVRE LE SITE DE L'ORGANISATION -->
            <?php if (!empty($org['website_url'])): ?>
              <button class="btn-details" onclick="window.open('<?= htmlspecialchars($org['website_url']) ?>', '_blank')">
                📋 Voir les détails
              </button>
            <?php else: ?>
              <button class="btn-details" style="opacity: 0.5; cursor: not-allowed;" disabled>
                📋 Détails non disponibles
              </button>
            <?php endif; ?>
          </div>
          
          <?php if (!empty($org['website_url'])): ?>
            <div class="visit-text">
              Cliquez 🔗 pour visiter le site
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
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
      const orgCards = document.querySelectorAll('#organisations > div > div');
      orgCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.6s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 200);
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