<?php
require_once __DIR__ . '/../../Controller/jeuxfront.php';
require_once __DIR__ . '/../../Controller/categoriefront.php';

$jeuxController = new JeuxFrontController();
$categorieController = new CategorieFrontController();

// Récupérer les paramètres
$categorie_id = $_GET['categorie'] ?? null;
$search = $_GET['search'] ?? '';

// Récupérer les données
if ($categorie_id) {
    $jeux = $jeuxController->getJeuxByCategorie($categorie_id);
    $categorie = $categorieController->getCategorie($categorie_id);
} else if ($search) {
    $allJeux = $jeuxController->getAllJeux();
    $jeux = array_filter($allJeux, function($jeu) use ($search) {
        return stripos($jeu['titre'], $search) !== false || 
               stripos($jeu['description'], $search) !== false;
    });
} else {
    $jeux = $jeuxController->getAllJeux();
}

$categories = $categorieController->getAllCategories();
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>All Games - GameZone</title>
    <meta charset="UTF-8">
    <meta name="description" content="Browse all games - GameZone">
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
        /* Styles personnalisés pour améliorer la présentation */
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .game-card {
            background: rgba(30, 30, 40, 0.9);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            height: 100%;
        }
        
        .game-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }
        
        .blog-thumb {
            position: relative;
            overflow: hidden;
            height: 220px;
        }
        
        .blog-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .game-card:hover .blog-thumb img {
            transform: scale(1.05);
        }
        
        .play-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .game-card:hover .play-overlay {
            opacity: 1;
        }
        
        .play-btn {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ff3366, #ff6633);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .play-btn:hover {
            transform: scale(1.1);
            background: linear-gradient(135deg, #ff6633, #ff3366);
        }
        
        .blog-text {
            padding: 25px;
            color: white;
        }
        
        .categorie {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        
        .blog-text h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
            color: white;
            line-height: 1.3;
        }
        
        .game-description {
            color: #b0b0c0;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .game-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        
        .price {
            font-size: 22px;
            font-weight: 700;
            color: #ff3366;
        }
        
        .promo-price {
            font-size: 18px;
            font-weight: 600;
            color: #33cc99;
            text-decoration: line-through;
            opacity: 0.8;
        }
        
        .btn-play {
            display: inline-block;
            background: linear-gradient(135deg, #33cc99, #00cc66);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }
        
        .btn-play:hover {
            background: linear-gradient(135deg, #00cc66, #33cc99);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(51, 204, 153, 0.4);
        }
        
        /* Styles pour la barre de recherche */
        .search-widget .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 15px 20px;
            border-radius: 30px;
            font-size: 16px;
        }
        
        .search-widget .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #ff3366;
            box-shadow: 0 0 15px rgba(255, 51, 102, 0.3);
            color: white;
        }
        
        .search-widget .site-btn {
            background: linear-gradient(135deg, #ff3366, #ff6633);
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .search-widget .site-btn:hover {
            background: linear-gradient(135deg, #ff6633, #ff3366);
            transform: translateY(-2px);
        }
        
        /* Styles pour les filtres de catégories */
        .blog-filter {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .blog-filter span {
            font-size: 16px;
            font-weight: 600;
            color: white;
            margin-right: 20px;
        }
        
        .blog-filter a {
            display: inline-block;
            padding: 8px 20px;
            margin: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: #b0b0c0;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .blog-filter a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-2px);
        }
        
        .blog-filter a.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        /* Styles pour la sidebar */
        .widget-item {
            background: rgba(30, 30, 40, 0.9);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .widget-title {
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .categories-widget ul {
            list-style: none;
            padding: 0;
        }
        
        .categories-widget li {
            margin-bottom: 12px;
        }
        
        .categories-widget a {
            color: #b0b0c0;
            text-decoration: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .categories-widget a:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            transform: translateX(5px);
        }
        
        .categories-widget span {
            background: linear-gradient(135deg, #ff3366, #ff6633);
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Styles pour les jeux récents */
        .tw-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .tw-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .tw-thumb {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            overflow: hidden;
            margin-right: 15px;
        }
        
        .tw-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .tw-text h5 {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .tw-meta {
            color: #b0b0c0;
            font-size: 12px;
        }
        
        .tw-meta a {
            color: #ff3366;
            text-decoration: none;
        }
        
        .tw-meta a:hover {
            text-decoration: underline;
        }
        
        /* Styles pour mobile */
        @media (max-width: 768px) {
            .games-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .blog-filter {
                text-align: center;
            }
            
            .blog-filter a {
                margin: 5px;
                display: inline-block;
            }
            
            .search-widget .form-control {
                margin-bottom: 10px;
            }
            
            .search-widget .input-group-append {
                width: 100%;
            }
            
            .search-widget .site-btn {
                width: 100%;
            }
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
                    <?php if ($categorie_id && isset($categorie)): ?>
                        <h2>Games - <?= htmlspecialchars($categorie['nom']) ?></h2>
                        <p class="lead"><?= htmlspecialchars($categorie['description']) ?></p>
                    <?php elseif ($search): ?>
                        <h2>Search Results for "<?= htmlspecialchars($search) ?>"</h2>
                        <p class="lead"><?= count($jeux) ?> game(s) found</p>
                    <?php else: ?>
                        <h2>All Games</h2>
                        <p class="lead">Discover our complete collection of amazing games</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- Page Top section end -->

    <!-- Games section -->
    <section class="blog-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Search Bar -->
                    <div class="search-widget mb-5">
                        <form method="GET" class="search-form">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search games..." value="<?= htmlspecialchars($search) ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="site-btn">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Category Filter -->
                    <div class="blog-filter mb-5">
                        <span>Filter by:</span>
                        <a href="jeuxliste.php" class="<?= !$categorie_id ? 'active' : '' ?>">All</a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="jeuxliste.php?categorie=<?= $cat['id'] ?>" class="<?= $categorie_id == $cat['id'] ? 'active' : '' ?>">
                                <?= htmlspecialchars($cat['nom']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Games List -->
                    <?php if (empty($jeux)): ?>
                        <div class="text-center text-white py-5">
                            <h3>No games found</h3>
                            <p>Try adjusting your search or filter criteria.</p>
                            <a href="jeuxliste.php" class="site-btn">View All Games</a>
                        </div>
                    <?php else: ?>
                        <div class="games-grid">
                            <?php foreach ($jeux as $jeu): ?>
                            <div class="game-card">
                                <div class="blog-thumb">
                                    <?php if ($jeu['image'] && file_exists(__DIR__ . '/../../uploads/' . $jeu['image'])): ?>
                                        <img src="../../uploads/<?= htmlspecialchars($jeu['image']) ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                                    <?php elseif ($jeu['image'] && strpos($jeu['image'], 'assets/images/jeux/') !== false): ?>
                                        <img src="../../<?= htmlspecialchars($jeu['image']) ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                                    <?php else: ?>
                                        <img src="img/default-game.jpg" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                                    <?php endif; ?>
                                    
                                    <div class="play-overlay">
                                        <?php if (!empty($jeu['lien_url'])): ?>
                                            <a href="<?= htmlspecialchars($jeu['lien_url']) ?>" target="_blank" class="play-btn">
                                                <i class="fa fa-play"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="jouer.php?id=<?= $jeu['id'] ?>" class="play-btn">
                                                <i class="fa fa-play"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="blog-text">
                                    <span class="categorie"><?= htmlspecialchars($jeu['categorie_nom']) ?></span>
                                    <h3><?= htmlspecialchars($jeu['titre']) ?></h3>
                                    <p class="game-description"><?= htmlspecialchars($jeu['description']) ?></p>
                                    <div class="game-stats">
                                        <span class="price">$<?= number_format($jeu['prix'], 2) ?></span>
                                        <?php if ($jeu['prix_promotion']): ?>
                                            <span class="promo-price">$<?= number_format($jeu['prix_promotion'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($jeu['lien_url'])): ?>
                                        <a href="<?= htmlspecialchars($jeu['lien_url']) ?>" target="_blank" class="btn-play">Play Now</a>
                                    <?php else: ?>
                                        <a href="jouer.php?id=<?= $jeu['id'] ?>" class="btn-play">View Details</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4 col-md-6 sidebar">
                    <div id="stickySidebar">
                        <div class="widget-item">
                            <h4 class="widget-title">Popular Categories</h4>
                            <div class="categories-widget">
                                <ul>
                                    <?php foreach ($categories as $cat): ?>
                                    <li>
                                        <a href="jeuxliste.php?categorie=<?= $cat['id'] ?>">
                                            <?= htmlspecialchars($cat['nom']) ?>
                                            <span>(<?= count($jeuxController->getJeuxByCategorie($cat['id'])) ?>)</span>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="widget-item">
                            <h4 class="widget-title">Recently Added</h4>
                            <div class="trending-widget">
                                <?php 
                                $recentGames = array_slice($jeuxController->getAllJeux(), 0, 3);
                                foreach ($recentGames as $jeu): 
                                ?>
                                <div class="tw-item">
                                    <div class="tw-thumb">
                                        <?php if ($jeu['image'] && file_exists(__DIR__ . '/../../uploads/' . $jeu['image'])): ?>
                                            <img src="../../uploads/<?= htmlspecialchars($jeu['image']) ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                                        <?php elseif ($jeu['image'] && strpos($jeu['image'], 'assets/images/jeux/') !== false): ?>
                                            <img src="../../<?= htmlspecialchars($jeu['image']) ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>">
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
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Games section end -->

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