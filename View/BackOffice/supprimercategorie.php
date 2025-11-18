<?php
require_once '../Controller/categorieback.php';

$controller = new CategorieBackController();

// Vérifier si l'ID est présent dans l'URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Récupérer la catégorie pour afficher les informations
    $categorie = $controller->getCategorie($id);
    
    if (!$categorie) {
        header("Location: back.php?error=Catégorie non trouvée");
        exit();
    }
} else {
    header("Location: back.php?error=ID de catégorie manquant");
    exit();
}

// Traitement de la suppression
if ($_POST && isset($_POST['confirm'])) {
    if ($controller->deleteCategorie($id)) {
        header("Location: back.php?success=Catégorie supprimée avec succès");
    } else {
        header("Location: back.php?error=Erreur lors de la suppression de la catégorie");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer Catégorie - BackOffice</title>
    <link rel="stylesheet" href="back.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Supprimer une Catégorie</h1>
            <nav>
                <a href="back.php" class="btn">Retour au BackOffice</a>
            </nav>
        </header>

        <section class="section">
            <div class="confirmation-box">
                <h2>Confirmation de suppression</h2>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>

                <div class="item-info">
                    <h3>Informations de la catégorie :</h3>
                    <p><strong>ID :</strong> <?= $categorie['id'] ?></p>
                    <p><strong>Nom :</strong> <?= htmlspecialchars($categorie['nom']) ?></p>
                    <p><strong>Description :</strong> <?= htmlspecialchars($categorie['description']) ?></p>
                </div>

                <div class="warning-message">
                    <h3>⚠️ Attention</h3>
                    <p>Vous êtes sur le point de supprimer cette catégorie. Cette action est irréversible.</p>
                    <p>Les jeux associés à cette catégorie ne seront pas supprimés, mais leur catégorie sera définie sur "NULL".</p>
                </div>

                <form action="supprimercategorie.php?id=<?= $id ?>" method="POST" class="confirmation-form">
                    <div class="form-actions">
                        <button type="submit" name="confirm" value="1" class="btn btn-delete">Confirmer la suppression</button>
                        <a href="back.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <script src="back.js"></script>
</body>
</html>