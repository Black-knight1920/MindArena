<?php
require_once __DIR__ . '/../../Controller/jeuxfront.php';
require_once __DIR__ . '/../../Controller/categoriefront.php';

$jeuxController = new JeuxFrontController();
$categorieController = new CategorieFrontController();

// Récupérer l'ID du jeu
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: jeuxliste.php");
    exit();
}

// Récupérer le jeu
$jeu = $jeuxController->getJeu($id);
if (!$jeu) {
    header("Location: jeuxliste.php?error=Jeu non trouvé");
    exit();
}

// Charger les liens des jeux
$liens_jeux = [];
$fichier_liens = __DIR__ . '/../../config/liens_jeux.php';
if (file_exists($fichier_liens)) {
    $liens_jeux = include $fichier_liens;
}

$lien_jeu = $liens_jeux[$jeu['id']] ?? null;

// Récupérer les jeux similaires
$jeuxSimilaires = $jeuxController->getJeuxByCategorie($jeu['categorie_id']);
$jeuxSimilaires = array_filter($jeuxSimilaires, function($j) use ($id) {
    return $j['id'] != $id;
});
$jeuxSimilaires = array_slice($jeuxSimilaires, 0, 3);
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Play <?= htmlspecialchars($jeu['titre']) ?> - GameZone</title>
    <meta charset="UTF-8">
    <meta name="description" content="Play <?= htmlspecialchars($jeu['titre']) ?> online - GameZone">
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
        .game-player-section {
            background: #1a1a1a;
            padding: 50px 0;
        }
        
        .game-container {
            background: #000;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        
        .game-frame {
            width: 100%;
            height: 600px;
            border: none;
        }
        
        .game-controls {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }
        
        .btn-control {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-control:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .game-placeholder {
            color: white;
            padding: 60px 40px;
            text-align: center;
            height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .game-placeholder h3 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: white;
        }
        
        .game-info-sidebar {
            background: #2a2a2a;
            padding: 30px;
            border-radius: 10px;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .game-stats-detail {
            margin: 25px 0;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #444;
            font-size: 0.95rem;
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
        
        .game-features {
            margin: 25px 0;
        }
        
        .game-features ul {
            list-style: none;
            padding: 0;
        }
        
        .game-features li {
            padding: 8px 0;
            color: #ccc;
        }
        
        .game-features li:before {
            content: "✓ ";
            color: #ff205f;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .game-actions-sidebar {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .btn-full {
            background: #ff205f;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-full:hover {
            background: #ff4070;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #ff205f;
            color: #ff205f;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-outline:hover {
            background: #ff205f;
            color: white;
        }
        
        .game-instructions {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #ff205f;
        }
        
        .game-instructions h4 {
            color: #ff205f;
            margin-bottom: 10px;
        }
        
        .game-instructions p {
            color: #ccc;
            margin: 0;
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
                    <h2>Play <?= htmlspecialchars($jeu['titre']) ?></h2>
                    <p>Get ready for an amazing gaming experience</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Page Top section end -->

    <!-- Game Player section -->
    <section class="game-player-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="game-container">
                        <div class="game-controls">
                            <button class="btn-control" onclick="fullscreen()"><i class="fa fa-expand"></i> Fullscreen</button>
                            <button class="btn-control" onclick="reloadGame()"><i class="fa fa-refresh"></i> Reload</button>
                            <button class="btn-control" onclick="shareGame()"><i class="fa fa-share"></i> Share</button>
                        </div>
                        
                        <?php if ($lien_jeu): ?>
                            <iframe src="<?= htmlspecialchars($lien_jeu) ?>" 
                                    class="game-frame"
                                    frameborder="0" 
                                    allowfullscreen
                                    allow="autoplay; gamepad">
                            </iframe>
                        <?php else: ?>
                            <div class="game-placeholder">
                                <?php if ($jeu['image']): ?>
                                    <img src="../../uploads/<?= $jeu['image'] ?>" 
                                         alt="<?= htmlspecialchars($jeu['titre']) ?>" 
                                         style="max-width: 200px; margin-bottom: 30px; border-radius: 10px; border: 3px solid white;">
                                <?php endif; ?>
                                
                                <h3>🎮 <?= htmlspecialchars($jeu['titre']) ?></h3>
                                <p style="font-size: 1.2rem; margin-bottom: 30px; max-width: 500px;">
                                    Get ready to play an amazing <?= htmlspecialchars($jeu['categorie_nom']) ?> game!
                                </p>
                                
                                <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 30px; max-width: 500px;">
                                    <h4 style="color: #ff205f; margin-bottom: 15px;">💡 Coming Soon!</h4>
                                    <p>This game will be available for online play very soon. Stay tuned for updates!</p>
                                </div>
                                
                                <div class="demo-buttons" style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
                                    <button class="site-btn" onclick="startDemo()" style="padding: 12px 25px;">
                                        <i class="fa fa-play"></i> Try Demo
                                    </button>
                                    <a href="jeuxliste.php?categorie=<?= $jeu['categorie_id'] ?>" class="site-btn" style="background: rgba(255,255,255,0.2); padding: 12px 25px;">
                                        More <?= htmlspecialchars($jeu['categorie_nom']) ?> Games
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($lien_jeu): ?>
                    <div class="game-instructions">
                        <h4><i class="fa fa-gamepad"></i> How to Play:</h4>
                        <p>Use your mouse and keyboard to control the game. The game loads from an external source and should work on most modern browsers. Make sure to enable JavaScript if prompted.</p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <div class="game-info-sidebar">
                        <h3 class="text-white">About This Game</h3>
                        <div class="top-meta mb-3">
                            Category: <a href="jeuxliste.php?categorie=<?= $jeu['categorie_id'] ?>" style="color: #ff205f;">
                                <?= htmlspecialchars($jeu['categorie_nom']) ?>
                            </a>
                        </div>
                        
                        <div class="game-stats-detail">
                            <div class="stat-item">
                                <strong>Popularity:</strong>
                                <span style="color: #ff205f;">Very High</span>
                            </div>
                            <div class="stat-item">
                                <strong>Difficulty:</strong>
                                <span>Intermediate</span>
                            </div>
                            <div class="stat-item">
                                <strong>Players Online:</strong>
                                <span>1,254</span>
                            </div>
                            <div class="stat-item">
                                <strong>Rating:</strong>
                                <span>⭐ 4.5/5</span>
                            </div>
                            <?php if ($jeu['prix'] > 0): ?>
                            <div class="stat-item">
                                <strong>Price:</strong>
                                <span style="color: #27ae60;"><?= $jeu['prix'] ?> €</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="game-description">
                            <h5 style="color: #fff; margin-bottom: 15px;">Description</h5>
                            <p style="color: #ccc; line-height: 1.6;"><?= nl2br(htmlspecialchars($jeu['description'])) ?></p>
                        </div>
                        
                        <div class="game-features">
                            <h5 style="color: #fff; margin-bottom: 15px;">Features</h5>
                            <ul>
                                <li>Playable directly in browser</li>
                                <li>Instant loading</li>
                                <li>Mobile and desktop compatible</li>
                                <li>Simple and intuitive controls</li>
                                <li>Regular updates</li>
                            </ul>
                        </div>
                        
                        <div class="game-actions-sidebar">
                            <a href="jeuxliste.php?categorie=<?= $jeu['categorie_id'] ?>" class="btn-full">
                                <i class="fa fa-th-large"></i> More <?= htmlspecialchars($jeu['categorie_nom']) ?> Games
                            </a>
                            <button class="btn-outline" onclick="shareGame()">
                                <i class="fa fa-share"></i> Share This Game
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Similar Games -->
            <?php if (!empty($jeuxSimilaires)): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-white mb-4" style="font-size: 2rem;">Similar Games You Might Like</h3>
                    <div class="games-grid">
                        <?php foreach ($jeuxSimilaires as $jeuSimilaire): ?>
                        <div class="game-card">
                            <div class="blog-thumb">
                                <?php if ($jeuSimilaire['image']): ?>
                                    <img src="../../uploads/<?= $jeuSimilaire['image'] ?>" alt="<?= htmlspecialchars($jeuSimilaire['titre']) ?>">
                                <?php else: ?>
                                    <img src="img/blog/1.jpg" alt="<?= htmlspecialchars($jeuSimilaire['titre']) ?>">
                                <?php endif; ?>
                                <div class="play-overlay">
                                    <a href="jouer.php?id=<?= $jeuSimilaire['id'] ?>" class="play-btn">
                                        <i class="fa fa-play"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="blog-text">
                                <span class="categorie"><?= htmlspecialchars($jeuSimilaire['categorie_nom']) ?></span>
                                <h4><?= htmlspecialchars($jeuSimilaire['titre']) ?></h4>
                                <p class="game-description"><?= htmlspecialchars(substr($jeuSimilaire['description'], 0, 80)) ?>...</p>
                                <div class="game-stats">
                                    <span class="players">👥 1.1k</span>
                                    <span class="rating">⭐ 4.3</span>
                                </div>
                                <a href="jouer.php?id=<?= $jeuSimilaire['id'] ?>" class="btn-play">Play Now</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <!-- Game Player section end -->

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

    <script>
        function startDemo() {
            const demoHTML = `
                <div style="background: #000; color: #fff; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; font-family: Arial, sans-serif; padding: 20px;">
                    <h1 style="color: #ff205f; margin-bottom: 30px; text-align: center;">🎮 DEMO - <?= htmlspecialchars($jeu['titre']) ?></h1>
                    <div style="background: #333; padding: 30px; border-radius: 15px; text-align: center; max-width: 500px;">
                        <p style="margin-bottom: 20px; font-size: 1.1rem;">🚀 Game demo is loading...</p>
                        <p style="margin-bottom: 25px; color: #FFD700; font-size: 1rem;">Use arrow keys or ZQSD to move around</p>
                        <div style="display: grid; grid-template-columns: repeat(3, 70px); gap: 15px; justify-content: center; margin-bottom: 30px;">
                            <div></div>
                            <button style="background: #ff205f; border: none; padding: 20px; border-radius: 10px; cursor: pointer; color: white; font-size: 18px;" 
                                    onmousedown="move('up')" onmouseup="stopMove()">↑</button>
                            <div></div>
                            <button style="background: #ff205f; border: none; padding: 20px; border-radius: 10px; cursor: pointer; color: white; font-size: 18px;" 
                                    onmousedown="move('left')" onmouseup="stopMove()">←</button>
                            <button style="background: #27ae60; border: none; padding: 20px; border-radius: 10px; cursor: pointer; color: white; font-size: 18px;" 
                                    onclick="action()">●</button>
                            <button style="background: #ff205f; border: none; padding: 20px; border-radius: 10px; cursor: pointer; color: white; font-size: 18px;" 
                                    onmousedown="move('right')" onmouseup="stopMove()">→</button>
                            <div></div>
                            <button style="background: #ff205f; border: none; padding: 20px; border-radius: 10px; cursor: pointer; color: white; font-size: 18px;" 
                                    onmousedown="move('down')" onmouseup="stopMove()">↓</button>
                            <div></div>
                        </div>
                        <p style="color: #888; font-size: 0.9rem;">This is a demonstration. The full game will be available soon!</p>
                    </div>
                </div>
            `;
            
            document.querySelector('.game-placeholder').innerHTML = demoHTML;
        }
        
        function move(direction) {
            console.log(`Moving: ${direction}`);
        }
        
        function stopMove() {
            console.log('Movement stopped');
        }
        
        function action() {
            alert('Action performed! 🎯');
        }
        
        function fullscreen() {
            const gameContainer = document.querySelector('.game-container');
            if (gameContainer.requestFullscreen) {
                gameContainer.requestFullscreen();
            }
        }
        
        function reloadGame() {
            const iframe = document.querySelector('.game-frame');
            if (iframe) {
                iframe.src = iframe.src;
            }
        }
        
        function shareGame() {
            const gameTitle = '<?= htmlspecialchars($jeu['titre']) ?>';
            const gameUrl = window.location.href;
            
            if (navigator.share) {
                navigator.share({
                    title: gameTitle,
                    text: `Check out ${gameTitle} on GameZone!`,
                    url: gameUrl
                });
            } else {
                navigator.clipboard.writeText(gameUrl).then(() => {
                    alert('Game link copied to clipboard! 📋\n' + gameUrl);
                });
            }
        }
        
        // Keyboard controls for demo
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowUp' || e.key.toLowerCase() === 'z') move('up');
            if (e.key === 'ArrowLeft' || e.key.toLowerCase() === 'q') move('left');
            if (e.key === 'ArrowRight' || e.key.toLowerCase() === 'd') move('right');
            if (e.key === 'ArrowDown' || e.key.toLowerCase() === 's') move('down');
            if (e.key === ' ') action();
        });
        
        document.addEventListener('keyup', (e) => {
            if (['ArrowUp', 'ArrowLeft', 'ArrowRight', 'ArrowDown', 'z', 'q', 's', 'd'].includes(e.key.toLowerCase())) {
                stopMove();
            }
        });
    </script>
</body>
</html>