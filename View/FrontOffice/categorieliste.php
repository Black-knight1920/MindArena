<?php
require_once __DIR__ . '/../../Controller/categoriefront.php';
require_once __DIR__ . '/../../Controller/jeuxfront.php';

$categorieController = new CategorieFrontController();
$jeuxController = new JeuxFrontController();

$categories = $categorieController->getAllCategories();
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Game Categories - GameZone</title>
    <meta charset="UTF-8">
    <meta name="description" content="Browse game categories - GameZone">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i,900,900i" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <link rel="stylesheet" href="css/owl.carousel.min.css" />
    <link rel="stylesheet" href="css/magnific-popup.css" />

    <!-- Main Stylesheets -->
    <link rel="stylesheet" href="css/style.css" />
    
    <style>
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        
        .category-card-large {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 40px 30px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .category-card-large::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        
        .category-card-large:hover::before {
            background: rgba(0,0,0,0.1);
        }
        
        .category-card-large:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .category-content {
            position: relative;
            z-index: 2;
        }
        
        .category-content h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: white;
        }
        
        .category-content p {
            font-size: 1rem;
            margin-bottom: 25px;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .game-count {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .category-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
        }
    </style>
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
                <a href="index.php" class="site-logo">
                    <img src="img/logo.png" alt="GameZone">
                </a>
                <nav class="top-nav-area w-100">
                    <div class="user-panel">
                        <a href="">Login</a> / <a href="">Register</a>
                    </div>
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

    <!-- Page Top section -->
    <section class="page-top-section set-bg" data-setbg="img/page-top-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 m-auto text-white">
                    <h2>Game Categories</h2>
                    <p>Explore our diverse collection of games organized by category</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Page Top section end -->

    <!-- Categories section -->
    <section class="blog-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="section-title text-white mb-5">
                        <h2>All Categories</h2>
                        <p>Find your favorite type of games</p>
                    </div>
                    
                    <div class="categories-grid">
                        <?php 
                        $categoryIcons = [
                            'Action' => 'fa-bolt',
                            'Aventure' => 'fa-binoculars',
                            'RPG' => 'fa-user',
                            'Stratégie' => 'fa-chess',
                            'Sport' => 'fa-trophy',
                            'FPS' => 'fa-crosshairs',
                            'Course' => 'fa-car',
                            'Puzzle' => 'fa-puzzle-piece',
                            'Combat' => 'fa-fist-raised',
                            'Simulation' => 'fa-cog'
                        ];
                        
                        foreach ($categories as $categorie): 
                            $jeuxCount = count($jeuxController->getJeuxByCategorie($categorie['id']));
                            $icon = $categoryIcons[$categorie['nom']] ?? 'fa-gamepad';
                        ?>
                        <a href="jeuxliste.php?categorie=<?= $categorie['id'] ?>" class="category-card-large">
                            <div class="category-content">
                                <i class="fa <?= $icon ?> category-icon"></i>
                                <span class="game-count"><?= $jeuxCount ?> game(s)</span>
                                <h2><?= htmlspecialchars($categorie['nom']) ?></h2>
                                <p><?= htmlspecialchars($categorie['description']) ?></p>
                                <div class="category-actions">
                                    <span class="site-btn" style="background: rgba(255,255,255,0.2); border: 2px solid white;">
                                        Browse Games <i class="fa fa-arrow-right"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 sidebar">
                    <div id="stickySidebar">
                        <div class="widget-item">
                            <h4 class="widget-title">Popular Categories</h4>
                            <div class="categories-widget">
                                <ul>
                                    <?php 
                                    $popularCategories = array_slice($categories, 0, 6);
                                    foreach ($popularCategories as $cat): 
                                        $jeuxCount = count($jeuxController->getJeuxByCategorie($cat['id']));
                                    ?>
                                    <li>
                                        <a href="jeuxliste.php?categorie=<?= $cat['id'] ?>">
                                            <?= htmlspecialchars($cat['nom']) ?>
                                            <span class="float-right">(<?= $jeuxCount ?>)</span>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="widget-item">
                            <h4 class="widget-title">Quick Stats</h4>
                            <div class="trending-widget">
                                <div class="tw-item">
                                    <div class="tw-text">
                                        <h6>Total Categories</h6>
                                        <div class="tw-meta"><?= count($categories) ?> categories</div>
                                    </div>
                                </div>
                                <div class="tw-item">
                                    <div class="tw-text">
                                        <h6>Total Games</h6>
                                        <div class="tw-meta"><?= count($jeuxController->getAllJeux()) ?> games</div>
                                    </div>
                                </div>
                                <div class="tw-item">
                                    <div class="tw-text">
                                        <h6>Most Popular</h6>
                                        <div class="tw-meta">
                                            <?= count($categories) > 0 ? htmlspecialchars($categories[0]['nom']) : 'No categories' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="widget-item">
                            <div class="categories-widget">
                                <h4 class="widget-title">Can't Decide?</h4>
                                <div class="text-center">
                                    <p style="color: #ccc; margin-bottom: 20px;">Explore all games at once</p>
                                    <a href="jeuxliste.php" class="site-btn btn-block">View All Games</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Categories section end -->

    <!-- Call to Action section -->
    <section class="intro-video-section set-bg d-flex align-items-end" data-setbg="img/promo-bg.jpg">
        <a href="jeuxliste.php" class="video-play-btn video-popup"><img src="img/icons/solid-right-arrow.png" alt="#"></a>
        <div class="container">
            <div class="video-text">
                <h2>Ready to Play?</h2>
                <p>Choose a category and start playing your favorite games now!</p>
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