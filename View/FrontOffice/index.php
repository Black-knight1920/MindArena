<?php
require_once __DIR__ . '/../../Controller/jeuxfront.php';
require_once __DIR__ . '/../../Controller/categoriefront.php';

$jeuxController = new JeuxFrontController();
$categorieController = new CategorieFrontController();

// Récupérer les données
$jeux = $jeuxController->getAllJeux();
$categories = $categorieController->getAllCategories();
$jeuxRecents = array_slice($jeux, 0, 6);
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>GameZone - Gaming Platform</title>
    <meta charset="UTF-8">
    <meta name="description" content="GameZone - Play Free Online Games">
    <meta name="keywords" content="games, gaming, online games, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i,900,900i" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <link rel="stylesheet" href="css/owl.carousel.min.css" />
    <link rel="stylesheet" href="css/magnific-popup.css" />
    <link rel="stylesheet" href="css/animate.css" />

    <!-- Main Stylesheets -->
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Header section -->
    <header class="header-section">
        <div class="header-warp">
            <div class="header-social d-flex justify-content-end">
                <p>Follow us:</p>
                <a href="#"><i class="fa fa-pinterest"></i></a>
                <a href="#"><i class="fa fa-facebook"></i></a>
                <a href="#"><i class="fa fa-twitter"></i></a>
                <a href="#"><i class="fa fa-dribbble"></i></a>
                <a href="#"><i class="fa fa-behance"></i></a>
            </div>
            <div class="header-bar-warp d-flex">
                <!-- site logo -->
                <a href="index.php" class="site-logo">
                    <img src="img/logo.png" alt="GameZone">
                </a>
                <nav class="top-nav-area w-100">
                    <div class="user-panel">
                        <a href="">Login</a> / <a href="">Register</a>
                    </div>
                    <!-- Menu -->
                    <ul class="main-menu primary-menu">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="jeuxliste.php">Games</a></li>
                        <li><a href="categorieliste.php">Categories</a></li>
                        <li><a href="jouer.php">Play Now</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <!-- Header section end -->

    <!-- Hero section -->
    <section class="hero-section overflow-hidden">
        <div class="hero-slider owl-carousel">
            <div class="hero-item set-bg d-flex align-items-center justify-content-center text-center" data-setbg="img/slider-bg-1.jpg">
                <div class="container">
                    <h2>Game On!</h2>
                    <p>Discover our amazing collection of free online games. Play now and join the fun!</p>
                    <a href="jeuxliste.php" class="site-btn">Play Now <img src="img/icons/double-arrow.png" alt="#"/></a>
                </div>
            </div>
            <div class="hero-item set-bg d-flex align-items-center justify-content-center text-center" data-setbg="img/slider-bg-2.jpg">
                <div class="container">
                    <h2>New Games Added!</h2>
                    <p>Fresh games added every week. Never run out of exciting challenges!</p>
                    <a href="jeuxliste.php" class="site-btn">Explore Games <img src="img/icons/double-arrow.png" alt="#"/></a>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero section end-->

    <!-- Featured Games section -->
    <section class="intro-section">
        <div class="container">
            <div class="row">
                <?php foreach (array_slice($jeuxRecents, 0, 3) as $jeu): ?>
                <div class="col-md-4">
                    <div class="intro-text-box text-box text-white">
                        <div class="top-meta">Featured / in <a href="jeuxliste.php?categorie=<?= $jeu['categorie_id'] ?>"><?= htmlspecialchars($jeu['categorie_nom']) ?></a></div>
                        <h3><?= htmlspecialchars($jeu['titre']) ?></h3>
                        <p><?= htmlspecialchars(substr($jeu['description'], 0, 150)) ?>...</p>
                        <a href="jouer.php?id=<?= $jeu['id'] ?>" class="read-more">Play Now <img src="img/icons/double-arrow.png" alt="#"/></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- Featured Games section end -->

    <!-- Latest Games section -->
    <section class="blog-section spad">
        <div class="container">
            <div class="row">
                <div class="col-xl-9 col-lg-8 col-md-7">
                    <div class="section-title text-white">
                        <h2>Latest Games</h2>
                    </div>
                    
                    <div class="games-grid">
                        <?php foreach ($jeuxRecents as $jeu): ?>
                        <div class="game-card">
                            <div class="blog-thumb">
                                <?php if ($jeu['image']): ?>
                                    <img src="../../uploads/<?= $jeu['image'] ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                                <?php else: ?>
                                    <img src="img/blog/1.jpg" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                                <?php endif; ?>
                                <div class="play-overlay">
                                    <a href="jouer.php?id=<?= $jeu['id'] ?>" class="play-btn">
                                        <i class="fa fa-play"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="blog-text">
                                <span class="categorie"><?= htmlspecialchars($jeu['categorie_nom']) ?></span>
                                <h3><?= htmlspecialchars($jeu['titre']) ?></h3>
                                <p class="game-description"><?= htmlspecialchars(substr($jeu['description'], 0, 100)) ?>...</p>
                                <div class="game-stats">
                                    <span class="players">👥 1.5k players</span>
                                    <span class="rating">⭐ 4.3/5</span>
                                </div>
                                <a href="jouer.php?id=<?= $jeu['id'] ?>" class="btn-play">Play Now</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-4 col-md-5 sidebar">
                    <div id="stickySidebar">
                        <div class="widget-item">
                            <h4 class="widget-title">Popular Games</h4>
                            <div class="trending-widget">
                                <?php foreach (array_slice($jeuxRecents, 0, 4) as $jeu): ?>
                                <div class="tw-item">
                                    <div class="tw-thumb">
                                        <?php if ($jeu['image']): ?>
                                            <img src="../../uploads/<?= $jeu['image'] ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                                        <?php else: ?>
                                            <img src="img/blog-widget/1.jpg" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="tw-text">
                                        <h5><?= htmlspecialchars($jeu['titre']) ?></h5>
                                        <div class="tw-meta">in <a href="jeuxliste.php?categorie=<?= $jeu['categorie_id'] ?>"><?= htmlspecialchars($jeu['categorie_nom']) ?></a></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="widget-item">
                            <div class="categories-widget">
                                <h4 class="widget-title">Categories</h4>
                                <ul>
                                    <?php foreach ($categories as $categorie): ?>
                                    <li><a href="jeuxliste.php?categorie=<?= $categorie['id'] ?>"><?= htmlspecialchars($categorie['nom']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Latest Games section end -->

    <!-- Call to Action section -->
    <section class="intro-video-section set-bg d-flex align-items-end" data-setbg="img/promo-bg.jpg">
        <a href="jeuxliste.php" class="video-play-btn video-popup"><img src="img/icons/solid-right-arrow.png" alt="#"></a>
        <div class="container">
            <div class="video-text">
                <h2>Ready to Play?</h2>
                <p>Join thousands of players enjoying our free online games collection.</p>
            </div>
        </div>
    </section>
    <!-- Call to Action section end -->

    <!-- Footer section -->
    <footer class="footer-section">
        <div class="container">
            <div class="footer-left-pic">
                <img src="img/footer-left-pic.png" alt="">
            </div>
            <div class="footer-right-pic">
                <img src="img/footer-right-pic.png" alt="">
            </div>
            <a href="index.php" class="footer-logo">
                <img src="img/logo.png" alt="GameZone">
            </a>
            <ul class="main-menu footer-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="jeuxliste.php">Games</a></li>
                <li><a href="categorieliste.php">Categories</a></li>
                <li><a href="jouer.php">Play Now</a></li>
            </ul>
            <div class="footer-social d-flex justify-content-center">
                <a href="#"><i class="fa fa-pinterest"></i></a>
                <a href="#"><i class="fa fa-facebook"></i></a>
                <a href="#"><i class="fa fa-twitter"></i></a>
                <a href="#"><i class="fa fa-dribbble"></i></a>
                <a href="#"><i class="fa fa-behance"></i></a>
            </div>
            <div class="copyright"><a href="">GameZone</a> 2024 @ All rights reserved</div>
        </div>
    </footer>
    <!-- Footer section end -->

    <!--====== Javascripts & Jquery ======-->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.slicknav.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.sticky-sidebar.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/main.js"></script>

</body>
</html>