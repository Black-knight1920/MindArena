<?php
require_once '../Controller/jeuxback.php';

$controller = new JeuxBackController();

$jeu = null;
if (isset($_GET['id'])) {
    $jeu = $controller->getJeux($_GET['id']);
}

if (!$jeu) {
    header("Location: back.php?error=Jeu non trouvé");
    exit();
}

$categories = $controller->getAllCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Jeu - BackOffice</title>
    <link rel="stylesheet" href="back.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Modifier le Jeu</h1>
            <nav>
                <a href="back.php" class="btn">Retour au BackOffice</a>
            </nav>
        </header>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <section class="section">
            <form action="../Controller/jeuxback.php" method="POST" enctype="multipart/form-data" class="form">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $jeu['id'] ?>">
                
                <div class="form-group">
                    <label for="titre">Titre du jeu:</label>
                    <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($jeu['titre']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($jeu['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="prix">Prix (€):</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0" value="<?= $jeu['prix'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="categorie_id">Catégorie:</label>
                    <select id="categorie_id" name="categorie_id" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?= $categorie['id'] ?>" <?= $categorie['id'] == $jeu['categorie_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categorie['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image">Image:</label>
                    <?php if ($jeu['image']): ?>
                        <div class="current-image">
                            <img src="../uploads/<?= $jeu['image'] ?>" alt="Image actuelle" class="image-preview">
                            <p>Image actuelle</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small>Laissez vide pour conserver l'image actuelle</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Modifier le Jeu</button>
                    <a href="back.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>