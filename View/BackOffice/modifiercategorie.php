<?php
require_once '../Controller/categorieback.php';

$controller = new CategorieBackController();

$categorie = null;
if (isset($_GET['id'])) {
    $categorie = $controller->getCategorie($_GET['id']);
}

if (!$categorie) {
    header("Location: back.php?error=Catégorie non trouvée");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Catégorie - BackOffice</title>
    <link rel="stylesheet" href="back.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Modifier la Catégorie</h1>
            <nav>
                <a href="back.php" class="btn">Retour au BackOffice</a>
            </nav>
        </header>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <section class="section">
            <form action="../Controller/categorieback.php" method="POST" class="form">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $categorie['id'] ?>">
                
                <div class="form-group">
                    <label for="nom">Nom de la catégorie:</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($categorie['nom']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($categorie['description']) ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Modifier la Catégorie</button>
                    <a href="back.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>