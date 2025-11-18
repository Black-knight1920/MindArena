<?php
// Utiliser le contrôleur FrontOffice, pas BackOffice
if (file_exists('../Controller/jeuxfront.php') && file_exists('../Controller/categoriefront.php')) {
    require_once '../Controller/jeuxfront.php';
    require_once '../Controller/categoriefront.php';

    $jeuxController = new JeuxFrontController();
    $categorieController = new CategorieFrontController();
    $categories = $categorieController->getAllCategories();
} else {
    $categories = [];
}

$message = "";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Jeu - FrontOffice</title>
    <link rel="stylesheet" href="front.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>🎮 Ajouter un Jeu</h1>
            <nav class="main-nav">
                <a href="ajouterjeux.php?action=create" class="nav-link">Retour à l'accueil</a>
            </nav>
        </header>

        <?= $message ?>

        <section class="section">
            <div class="no-data">
                <h3>Fonctionnalité d'administration</h3>
                <p>L'ajout de jeux est réservé à l'administration.</p>
                <p>Veuillez contacter un administrateur pour ajouter de nouveaux jeux.</p>
                <a href="front.php" class="btn btn-primary">Retour à la boutique</a>
            </div>
        </section>
    </div>
</body>
</html>