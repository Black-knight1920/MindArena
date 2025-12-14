<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Catégorie</title>
    <link rel="stylesheet" href="front.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Modifier la Catégorie</h1>
            <nav>
                <a href="categoriefront.php">Retour à la liste</a>
                <a href="../FrontOffice/front.php">Accueil</a>
            </nav>
        </header>

        <?php if($message): ?>
            <div class="message error"><?php echo $message; ?></div>
        <?php endif; ?>

        <main>
            <form method="POST" class="form-container" onsubmit="return validateCategorieForm()">
                <input type="hidden" name="id" value="<?php echo $categorie->id; ?>">
                
                <div class="form-group">
                    <label for="nom">Nom de la catégorie *</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($categorie->nom); ?>" required>
                    <span class="error-message" id="nom-error"></span>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($categorie->description); ?></textarea>
                    <span class="error-message" id="description-error"></span>
                </div>

                <button type="submit" class="btn btn-primary">Modifier la Catégorie</button>
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

        document.addEventListener('DOMContentLoaded', function() {
            const nom = document.getElementById('nom');
            const description = document.getElementById('description');
            
            if (nom) {
                nom.addEventListener('blur', validateNom);
            }
            if (description) {
                description.addEventListener('blur', validateDescription);
            }
        });

        function validateNom() {
            const nom = document.getElementById('nom');
            const nomError = document.getElementById('nom-error');
            
            if (nom.value.trim() === '') {
                nomError.textContent = 'Le nom de la catégorie est obligatoire';
            } else if (nom.value.trim().length < 2) {
                nomError.textContent = 'Le nom doit contenir au moins 2 caractères';
            } else {
                nomError.textContent = '';
            }
        }

        function validateDescription() {
            const description = document.getElementById('description');
            const descriptionError = document.getElementById('description-error');
            
            if (description.value.trim().length > 500) {
                descriptionError.textContent = 'La description ne doit pas dépasser 500 caractères';
            } else {
                descriptionError.textContent = '';
            }
        }
    </script>
</body>
</html>