<?php
// Charger le modèle Categorie
require_once __DIR__ . "/../../Model/Categorie.php";

// Initialisation des variables
$message = "";

// Si formulaire soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom"] ?? "");
    $description = trim($_POST["description"] ?? "");

    $categorie = new Categorie();

    // Validation
    if ($nom === "" || strlen($nom) < 2) {
        $message = "Le nom doit contenir au moins 2 caractères.";
    } 
    else if (strlen($description) > 500) {
        $message = "La description ne doit pas dépasser 500 caractères.";
    }
    else if ($categorie->existe($nom)) {
        $message = "Cette catégorie existe déjà.";
    }
    else {
        if ($categorie->ajouter($nom, $description)) {
            $message = "Catégorie ajoutée avec succès !";
        } else {
            $message = "Erreur lors de l'enregistrement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Catégorie</title>
    <link rel="stylesheet" href="front.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Ajouter une Nouvelle Catégorie</h1>
            <nav>
                <a href="ajoutercategorie.php">Retour à la liste</a>
                <a href="../FrontOffice/front.php">Accueil</a>
            </nav>
        </header>

        <?php if (!empty($message)): ?>
            <div class="message error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <main>
            <form method="POST" class="form-container" onsubmit="return validateCategorieForm()">

                <div class="form-group">
                    <label for="nom">Nom de la catégorie *</label>
                    <input type="text" id="nom" name="nom" required>
                    <span class="error-message" id="nom-error"></span>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="Description de la catégorie..."></textarea>
                    <span class="error-message" id="description-error"></span>
                </div>

                <button type="submit" class="btn btn-primary">Créer la Catégorie</button>
                <a href="categoriefront.php" class="btn">Annuler</a>
            </form>
        </main>
    </div>

    <script>
        function validateCategorieForm() {
            let isValid = true;

            const nom = document.getElementById('nom');
            const nomError = document.getElementById('nom-error');

            if (nom.value.trim() === '') {
                nomError.textContent = 'Le nom de la catégorie est obligatoire';
                isValid = false;
            } else if (nom.value.trim().length < 2) {
                nomError.textContent = 'Le nom doit contenir au moins 2 caractères';
                isValid = false;
            } else {
                nomError.textContent = '';
            }

            const description = document.getElementById('description');
            const descriptionError = document.getElementById('description-error');

            if (description.value.trim().length > 500) {
                descriptionError.textContent = 'La description ne doit pas dépasser 500 caractères';
                isValid = false;
            } else {
                descriptionError.textContent = '';
            }

            return isValid;
        }
    </script>
</body>
</html>
