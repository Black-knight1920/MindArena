<?php
require_once __DIR__ . '/../../Controller/categorieback.php';

$controller = new CategorieBackController();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Catégorie - BackOffice</title>
    <link rel="stylesheet" href="back.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Ajouter une Catégorie</h1>
            <nav>
                <a href="back.php" class="btn">Retour au BackOffice</a>
            </nav>
        </header>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <section class="section">
            <form action="../Controller/categorieback.php" method="POST" class="form">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="nom">Nom de la catégorie:</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Ajouter la Catégorie</button>
                    <a href="back.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>