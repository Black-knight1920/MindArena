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

// Récupérer les jeux de la même catégorie
$jeuxSimilaires = $jeuxController->getJeuxByCategorie($jeu['categorie_id']);
// Exclure le jeu actuel
$jeuxSimilaires = array_filter($jeuxSimilaires, function($j) use ($id) {
    return $j['id'] != $id;
});
// Limiter à 4 jeux
$jeuxSimilaires = array_slice($jeuxSimilaires, 0, 4);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($jeu['titre']) ?> - GameStore</title>
    <link rel="stylesheet" href="front.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <h1><a href="index.php" style="color: white; text-decoration: none;">GameStore</a></h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="jeuxliste.php">Tous les jeux</a></li>
                <li><a href="categorieliste.php">Catégories</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <!-- Fil d'Ariane -->
        <nav class="breadcrumb">
            <a href="index.php">Accueil</a> > 
            <a href="jeuxliste.php">Jeux</a> > 
            <a href="jeuxliste.php?categorie=<?= $jeu['categorie_id'] ?>"><?= htmlspecialchars($jeu['categorie_nom']) ?></a> > 
            <span><?= htmlspecialchars($jeu['titre']) ?></span>
        </nav>

        <!-- Détail du jeu -->
        <section class="game-detail">
            <div class="game-detail-content">
                <div class="game-image">
                    <?php if ($jeu['image']): ?>
                        <img src="../../uploads/<?= $jeu['image'] ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                    <?php else: ?>
                        <div class="no-image-large">Image non disponible</div>
                    <?php endif; ?>
                </div>
                
                <div class="game-info-detail">
                    <h1><?= htmlspecialchars($jeu['titre']) ?></h1>
                    <p class="categorie-badge"><?= htmlspecialchars($jeu['categorie_nom']) ?></p>
                    
                    <div class="price-detail">
                        <?php if ($jeu['prix_promotion']): ?>
                            <span class="old-price"><?= $jeu['prix'] ?> €</span>
                            <span class="promo-price-large"><?= $jeu['prix_promotion'] ?> €</span>
                            <span class="discount-badge">-<?= round(($jeu['prix'] - $jeu['prix_promotion']) / $jeu['prix'] * 100) ?>%</span>
                        <?php else: ?>
                            <span class="normal-price-large"><?= $jeu['prix'] ?> €</span>
                        <?php endif; ?>
                    </div>

                    <div class="game-actions">
                        <button class="btn btn-primary btn-large">Jouer maintenant</button>
                        <button class="btn btn-secondary">Ajouter aux favoris</button>
                    </div>

                    <div class="game-description-full">
                        <h3>Description</h3>
                        <p><?= nl2br(htmlspecialchars($jeu['description'])) ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Jeux similaires -->
        <?php if (!empty($jeuxSimilaires)): ?>
        <section class="similar-games">
            <h2>Jeux similaires</h2>
            <div class="games-grid">
                <?php foreach ($jeuxSimilaires as $jeuSimilaire): ?>
                <div class="game-card">
                    <?php if ($jeuSimilaire['image']): ?>
                    <img src="../../uploads/<?= $jeuSimilaire['image'] ?>" alt="<?= htmlspecialchars($jeuSimilaire['titre']) ?>">
                    <?php else: ?>
                    <div class="no-image">Image non disponible</div>
                    <?php endif; ?>
                    <div class="game-info">
                        <h3><?= htmlspecialchars($jeuSimilaire['titre']) ?></h3>
                        <p class="categorie"><?= htmlspecialchars($jeuSimilaire['categorie_nom']) ?></p>
                        <div class="price">
                            <?php if ($jeuSimilaire['prix_promotion']): ?>
                                <span class="promo-price"><?= $jeuSimilaire['prix_promotion'] ?> €</span>
                                <span class="old-price"><?= $jeuSimilaire['prix'] ?> €</span>
                            <?php else: ?>
                                <span class="normal-price"><?= $jeuSimilaire['prix'] ?> €</span>
                            <?php endif; ?>
                        </div>
                        <a href="jeuxdetail.php?id=<?= $jeuSimilaire['id'] ?>" class="btn">Voir le jeu</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 GameStore. Tous droits réservés.</p>
    </footer>
</body>
</html>