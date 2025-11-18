<?php
require_once __DIR__ . '/../../Controller/jeuxback.php';
require_once __DIR__ . '/../../Controller/categorieback.php';

$jeuxController = new JeuxBackController();
$categorieController = new CategorieBackController();

$categories = $categorieController->getAllCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Jeu - BackOffice</title>
    <link rel="stylesheet" href="back.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Ajouter un Jeu</h1>
            <nav>
                <a href="back.php" class="btn">Retour au BackOffice</a>
            </nav>
        </header>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <section class="section">
            <form action="../Controller/jeuxback.php" method="POST" enctype="multipart/form-data" class="form">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="titre">Titre du jeu:</label>
                    <input type="text" id="titre" name="titre" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="prix">Prix (€):</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="categorie_id">Catégorie:</label>
                    <select id="categorie_id" name="categorie_id" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?= $categorie['id'] ?>"><?= htmlspecialchars($categorie['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Ajouter le Jeu</button>
                    <a href="back.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>