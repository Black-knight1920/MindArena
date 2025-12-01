<?php
require_once __DIR__."/../../Controller/DonController.php";
require_once __DIR__."/../../Model/DonateurClasse.php";

$donCtrl = new DonController();
$classement = $donCtrl->getClassementDonateurs(20);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Classement des Donateurs - Mind Arena</title>
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

    .classement-table {
        width: 100%;
        background: rgba(255,255,255,0.1);
        border-radius: 15px;
        overflow: hidden;
        margin: 30px 0;
        border: 1px solid #b01ba5;
    }

    .classement-table th {
        background: #b01ba5;
        color: white;
        padding: 20px;
        text-align: left;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .classement-table td {
        padding: 18px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-size: 1rem;
    }

    .classement-table tr:hover {
        background: rgba(255,255,255,0.05);
    }

    .rang-1 { 
        background: rgba(255, 215, 0, 0.3);
        font-weight: bold;
    }

    .rang-2 { 
        background: rgba(192, 192, 192, 0.25);
    }

    .rang-3 { 
        background: rgba(205, 127, 50, 0.25);
    }

    .classe-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: rgba(255,255,255,0.1);
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        border: 1px solid #b01ba5;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #4cff4c;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #ccc;
        font-size: 0.9rem;
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
        padding: 40px;
        color: #ccc;
        font-style: italic;
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
        
        .hero {
            padding: 120px 20px 60px;
        }
        
        .hero h2 {
            font-size: 2.5rem;
        }
        
        .classement-table {
            display: block;
            overflow-x: auto;
        }
        
        .classement-table th,
        .classement-table td {
            padding: 12px 15px;
            font-size: 0.9rem;
        }
    }
  </style>
</head>

<body>

  <header>
    <h1>🎮 Mind Arena Magazine</h1>
    <nav>
      <a href="index.php">Accueil</a>
      <a href="classementDonateurs.php" style="color: #b01ba5;">Classement</a>
      <a href="index.php#organisations">Associations</a>
      <a href="../../backoffice.php">Espace Admin</a>
    </nav>
  </header>

  <section class="hero">
    <h2>🏆 Classement des Donateurs</h2>
    <p>
      Découvrez les donateurs les plus généreux de notre communauté et motivez-vous à monter dans le classement !
    </p>
  </section>

  <section class="classement-section">
    <!-- Statistiques -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-number"><?= count($classement) ?></div>
            <div class="stat-label">Donateurs Actifs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= number_format(array_sum(array_column($classement, 'total_dons')), 2) ?>€</div>
            <div class="stat-label">Total Collecté</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">
                <?php 
                $legendes = array_filter($classement, function($d) { 
                    return $d['classe'] === 'Légende'; 
                });
                echo count($legendes);
                ?>
            </div>
            <div class="stat-label">Légendes</div>
        </div>
    </div>

    <!-- Tableau de classement complet -->
    <table class="classement-table">
        <thead>
            <tr>
                <th width="80">Rang</th>
                <th>Donateur</th>
                <th width="150">Total des dons</th>
                <th width="150">Classe</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($classement)): ?>
                <tr>
                    <td colspan="4" class="empty-classement">
                        Aucun donateur pour le moment. Soyez le premier !
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($classement as $index => $donateur): 
                    $infosClasse = DonateurClasse::getInfosClasse($donateur['classe']);
                    $medaille = '';
                    if ($index === 0) $medaille = '🥇';
                    elseif ($index === 1) $medaille = '🥈';
                    elseif ($index === 2) $medaille = '🥉';
                    
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
                ?>
                <tr class="rang-<?= $index + 1 ?>">
                    <td>
                        <strong>
                            <?= $medaille ?> #<?= $index + 1 ?>
                        </strong>
                    </td>
                    <td>
                        <strong>
                            <?= $nomComplet ?>
                        </strong>
                    </td>
                    <td><strong style="color: #4cff4c;"><?= number_format($donateur['total_dons'], 2) ?> €</strong></td>
                    <td>
                        <span class="classe-badge" style="color: <?= $infosClasse['couleur'] ?>; border: 2px solid <?= $infosClasse['couleur'] ?>;">
                            <?= $infosClasse['badge'] ?> <?= $donateur['classe'] ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Appel à l'action -->
    <div style="text-align: center; margin-top: 40px;">
        <h3 style="color: #b01ba5; margin-bottom: 20px;">Rejoignez le classement !</h3>
        <p style="color: #ccc; margin-bottom: 25px;">
            Faites un don et progressez dans les différentes classes pour apparaître ici.
        </p>
        <p style="color: #b01ba5; font-weight: bold;">
            🎯 Accédez aux pages des associations pour faire un don !
        </p>
    </div>

    <!-- Légende des classes -->
    <div style="background: rgba(255,255,255,0.05); padding: 25px; border-radius: 10px; margin-top: 40px;">
        <h4 style="color: #b01ba5; margin-bottom: 15px; text-align: center;">🎯 Système de Classes</h4>
        <div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 15px;">
            <?php foreach (DonateurClasse::CLASSES as $nomClasse => $infos): ?>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; margin-bottom: 5px;"><?= $infos['badge'] ?></div>
                    <div style="color: <?= $infos['couleur'] ?>; font-weight: bold;"><?= $nomClasse ?></div>
                    <div style="color: #ccc; font-size: 0.8rem;"><?= number_format($infos['seuil']) ?>€+</div>
                </div>
            <?php endforeach; ?>
        </div>
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

      // Animation des lignes du tableau
      const tableRows = document.querySelectorAll('.classement-table tbody tr');
      tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-30px)';
        
        setTimeout(() => {
          row.style.transition = 'all 0.6s ease';
          row.style.opacity = '1';
          row.style.transform = 'translateX(0)';
        }, index * 100);
      });
    });
  </script>
</body>
</html>