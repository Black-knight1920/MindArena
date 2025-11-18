<?php
include_once(__DIR__ . '/../../Controller/CoursController.php');
include_once(__DIR__ . '/../../Controller/ChapitreController.php');
$coursC = new CoursController();
$chapitreC = new ChapitreController();
$list = $coursC->listCours();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Cours - MindArena</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="assets/images/logos/favicon.png" rel="shortcut icon" type="image/png" />
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
  <link rel="stylesheet" href="assets/libs/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="assets/css/styles.min.css">

  <style>
  /* === BASE THEME === */
  body {
    background: linear-gradient(45deg,#501755,#2d1854);
    color:#fff;
    font-family:'Roboto',sans-serif;
    scroll-behavior:smooth;
    overflow-x:hidden;
    padding-top:120px;
  }
  section { padding:80px 0; }

  /* === HEADER === */
  .header-section {
    position:fixed;
    top:0;left:0;width:100%;
    background:#081624;
    z-index:999;
    box-shadow:0 0 15px rgba(0,0,0,0.6);
  }
  .header-bar-warp {
    padding:15px 40px;
    display:flex;
    align-items:center;
    justify-content:space-between;
  }
  .mindarena-logo {
    display:flex;
    align-items:center;
    gap:15px;
  }
  .mindarena-logo img {
    max-height:80px;
    filter:drop-shadow(0 0 6px rgba(176,27,165,0.8));
    animation:neonPulse 3s infinite ease-in-out;
  }
  .esprit-logo {
    max-height:40px;
    opacity:0.9;
  }
  .main-menu { list-style:none; margin:0; padding:0; display:flex; align-items:center; }
  .main-menu li { display:inline-block; margin-left:20px; }
  .main-menu a {
    color:#fff; font-weight:600;
    text-transform:uppercase;
    transition:all 0.3s;
    text-decoration:none;
  }
  .main-menu a:hover { color:#b01ba5; text-shadow:0 0 8px #b01ba5; }
  .main-menu a.active { color:#b01ba5; text-shadow:0 0 8px #b01ba5; }
  @keyframes neonPulse {
    0%{filter:drop-shadow(0 0 4px #b01ba5);}
    50%{filter:drop-shadow(0 0 14px #ff3ee7);}
    100%{filter:drop-shadow(0 0 4px #b01ba5);}
  }

  /* === HERO === */
  .hero {
    min-height:50vh;
    background:linear-gradient(135deg, rgba(80,23,85,0.9), rgba(45,24,84,0.9));
    display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    text-align:center;
    position:relative;
  }
  .hero h1 { 
    font-size:60px;
    text-shadow:0 0 10px #b01ba5;
    margin-bottom:20px;
  }
  .hero p { 
    color:#ddd;
    margin-top:15px;
    font-size:18px;
  }

  /* === COURS CARDS === */
  .cours-card {
    background:rgba(255,255,255,0.05);
    border-radius:8px;
    overflow:hidden;
    transition:all 0.3s ease;
    padding-bottom:15px;
    height:100%;
    margin-bottom:30px;
  }
  .cours-card:hover { 
    transform:translateY(-6px); 
    box-shadow:0 8px 25px rgba(0,0,0,0.4);
    background:rgba(255,255,255,0.08);
  }
  .cours-card .cours-header {
    background:linear-gradient(135deg,#b01ba5,#501755);
    padding:25px 20px;
    text-align:center;
    position:relative;
  }
  .cours-badge {
    position:absolute;
    top:15px;
    right:15px;
    padding:6px 12px;
    border-radius:20px;
    font-size:11px;
    font-weight:600;
    text-transform:uppercase;
  }
  .badge-success { background:#28a745; color:white; }
  .badge-warning { background:#ffc107; color:#333; }
  .badge-info { background:#17a2b8; color:white; }
  .badge-danger { background:#dc3545; color:white; }
  .cours-card h3 { 
    color:#fff;
    margin:0;
    font-size:22px;
    font-weight:700;
  }
  .cours-card .cours-content {
    padding:25px;
  }
  .cours-card .cours-description {
    color:#ddd;
    margin-bottom:20px;
    line-height:1.8;
    min-height:60px;
  }
  .cours-card .cours-info {
    display:flex;
    align-items:center;
    gap:10px;
    margin:12px 0;
    padding:10px;
    background:rgba(255,255,255,0.05);
    border-radius:6px;
    transition:all 0.3s;
  }
  .cours-card .cours-info:hover {
    background:rgba(176,27,165,0.2);
    transform:translateX(5px);
  }
  .cours-card .cours-info i {
    color:#b01ba5;
    font-size:16px;
    width:20px;
  }
  .cours-card .cours-info span {
    color:#fff;
    font-size:14px;
  }

  .no-cours {
    text-align:center;
    padding:60px 20px;
    color:#ddd;
  }
  .no-cours i {
    font-size:64px;
    color:#b01ba5;
    margin-bottom:20px;
  }

  /* === FOOTER === */
  .footer-section { 
    background:#081624;
    text-align:center;
    padding:30px 0;
    margin-top:60px;
  }
  .footer-section img { 
    max-height:60px;
    margin-bottom:10px;
  }
  .footer-section p { 
    color:#777;
  }

  /* === ANIMATIONS === */
  [data-animate] { 
    opacity:0; 
    transform:translateY(30px); 
    transition:all 0.8s ease; 
  }
  [data-animate].visible { 
    opacity:1; 
    transform:translateY(0); 
  }

  /* === RESPONSIVE === */
  @media (max-width: 768px) {
    .hero h1 { font-size:42px; }
    .main-menu { flex-direction:column; gap:10px; }
    .header-bar-warp { flex-direction:column; padding:10px; }
  }
  </style>
</head>
<body>

<!-- HEADER -->
<header class="header-section">
  <div class="header-warp">
    <div class="header-bar-warp">
      <a href="index.html" class="site-logo mindarena-logo">
        <img src="images/mindarena-logo.svg" alt="MindArena">
        <img src="images/esprit-logo.svg" alt="Esprit" class="esprit-logo">
      </a>
      <nav class="top-nav-area w-100">
        <ul class="main-menu primary-menu">
          <li><a href="index.html">Home</a></li>
          <li><a href="coursList.php" class="active">Cours</a></li>
          <li><a href="index.html#about">À propos</a></li>
          <li><a href="index.html#contact">Contact</a></li>
          <li><a href="../BackOffice/index.html">Dashboard</a></li>
        </ul>
      </nav>
    </div>
  </div>
</header>

<!-- HERO -->
<section class="hero">
  <div data-animate>
    <h1>📚 Nos Cours</h1>
    <p>Découvrez notre collection complète de cours interactifs couvrant divers domaines et niveaux</p>
  </div>
</section>

<!-- COURS SECTION -->
<section id="cours">
  <div class="container" data-animate>
    <?php if(empty($list)): ?>
      <div class="no-cours">
        <i class="fa fa-graduation-cap"></i>
        <h3>Aucun cours disponible</h3>
        <p>Les cours seront bientôt disponibles</p>
      </div>
    <?php else: ?>
      <div class="row">
        <?php
        foreach($list as $cours) {
          $niveauClass = '';
          switch($cours['niveau']) {
            case 'Débutant': $niveauClass = 'success'; break;
            case 'Intermédiaire': $niveauClass = 'warning'; break;
            case 'Avancé': $niveauClass = 'info'; break;
            case 'Expert': $niveauClass = 'danger'; break;
            default: $niveauClass = 'info';
          }
          $chapitres = $chapitreC->getChapitresByCoursId($cours['id']);
        ?>
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="cours-card">
            <div class="cours-header">
              <span class="cours-badge badge-<?php echo $niveauClass; ?>">
                <?php echo htmlspecialchars($cours['niveau']); ?>
              </span>
              <h3><?php echo htmlspecialchars($cours['titre']); ?></h3>
            </div>
            <div class="cours-content">
              <p class="cours-description">
                <?php echo htmlspecialchars(strlen($cours['description']) > 100 ? substr($cours['description'], 0, 100) . '...' : $cours['description']); ?>
              </p>
              <div class="cours-info">
                <i class="fa fa-clock-o"></i>
                <span><?php echo htmlspecialchars($cours['duree']); ?> heures</span>
              </div>
              <div class="cours-info">
                <i class="fa fa-user"></i>
                <span><?php echo htmlspecialchars($cours['formateur']); ?></span>
              </div>
              <?php if(!empty($chapitres)): ?>
              <div class="cours-info" style="margin-top:15px;padding-top:15px;border-top:1px solid rgba(255,255,255,0.1);">
                <i class="fa fa-book"></i>
                <span><strong><?php echo count($chapitres); ?> chapitre(s):</strong></span>
              </div>
              <div style="margin-top:10px;padding-left:30px;">
                <?php foreach($chapitres as $chapitre): ?>
                <div style="margin-bottom:8px;color:#ddd;font-size:13px;">
                  <i class="fa fa-circle" style="font-size:6px;margin-right:8px;"></i>
                  <?php echo htmlspecialchars($chapitre['titre']); ?>
                </div>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php
        }
        ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer-section">
  <div class="container">
    <div class="mindarena-logo" style="justify-content:center;margin-bottom:15px;">
      <img src="images/mindarena-logo.svg" alt="MindArena">
      <img src="images/esprit-logo.svg" alt="Esprit" class="esprit-logo">
    </div>
    <p>© 2025 MindArena - Esprit. Tous droits réservés.</p>
  </div>
</footer>

<!-- ==== SCRIPTS ==== -->
<script src="assets/libs/jquery/dist/jquery.min.js"></script>
<script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* === ANIMATE ON SCROLL === */
const animatedElements = document.querySelectorAll('[data-animate]');
const observer = new IntersectionObserver(entries=>{
  entries.forEach(e=>{
    if(e.isIntersecting){
      e.target.classList.add('visible');
    }
  });
},{threshold:0.2});
animatedElements.forEach(el=>observer.observe(el));
</script>

</body>
</html>
