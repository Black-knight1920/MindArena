<?php
require_once '../Controller/jeuxback.php';

$controller = new JeuxBackController();

// Vérifier si l'ID est présent dans l'URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Récupérer le jeu pour afficher les informations
    $jeu = $controller->getJeux($id);
    
    if (!$jeu) {
        header("Location: back.php?error=Jeu non trouvé");
        exit();
    }
} else {
    header("Location: back.php?error=ID de jeu manquant");
    exit();
}

// Traitement de la suppression
if ($_POST && isset($_POST['confirm'])) {
    if ($controller->deleteJeux($id)) {
        header("Location: back.php?success=Jeu supprimé avec succès");
    } else {
        header("Location: back.php?error=Erreur lors de la suppression du jeu");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer Jeu - BackOffice</title>
    <link rel="stylesheet" href="back.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Supprimer un Jeu</h1>
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
                    <h3>Informations du jeu :</h3>
                    <p><strong>ID :</strong> <?= $jeu['id'] ?></p>
                    <p><strong>Titre :</strong> <?= htmlspecialchars($jeu['titre']) ?></p>
                    <p><strong>Description :</strong> <?= htmlspecialchars(substr($jeu['description'], 0, 100)) ?>...</p>
                    <p><strong>Prix :</strong> <?= $jeu['prix'] ?> €</p>
                    <p><strong>Catégorie :</strong> <?= htmlspecialchars($jeu['categorie_nom']) ?></p>
                    
                    <?php if ($jeu['image']): ?>
                        <p><strong>Image :</strong></p>
                        <img src="../uploads/<?= $jeu['image'] ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>" class="image-preview-large">
                    <?php else: ?>
                        <p><strong>Image :</strong> Aucune image</p>
                    <?php endif; ?>
                </div>

                <div class="warning-message">
                    <h3>⚠️ Attention</h3>
                    <p>Vous êtes sur le point de supprimer ce jeu définitivement.</p>
                    <p>Cette action est irréversible et supprimera toutes les données associées à ce jeu.</p>
                </div>

                <form action="supprimerjeux.php?id=<?= $id ?>" method="POST" class="confirmation-form">
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