<?php
require_once __DIR__."/../../../Controller/OrganisationController.php";
require_once __DIR__."/../../../Model/Organisation.php";

$orgCtrl = new OrganisationController();
$message = '';
$messageType = '';

if ($_POST) {
    try {
        $organisation = new Organisation(
            null,
            trim($_POST['nom']),
            trim($_POST['description'])
        );
        
        // Validation côté serveur
        $validationErrors = $orgCtrl->validateOrganisation($organisation);
        
        if (empty($validationErrors)) {
            if ($orgCtrl->addOrganisation($organisation)) {
                $message = "✅ Organisation ajoutée avec succès!";
                $messageType = 'success';
                header("refresh:2;url=organisationList.php");
            }
        } else {
            $message = "❌ Erreurs de validation:<br>• " . implode("<br>• ", $validationErrors);
            $messageType = 'error';
        }
        
    } catch (Exception $e) {
        $message = "❌ Erreur: " . $e->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Organisation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box; transition: all 0.3s; }
        textarea { height: 120px; resize: vertical; }
        input:focus, textarea:focus { outline: none; border-color: #4CAF50; box-shadow: 0 0 5px rgba(76,175,80,0.3); }
        button { background: #4CAF50; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-right: 10px; transition: background 0.3s; }
        button:hover { background: #45a049; }
        .message { padding: 15px; margin: 20px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .btn-cancel { background: #6c757d; color: white; text-decoration: none; padding: 12px 20px; border-radius: 5px; display: inline-block; transition: background 0.3s; }
        .btn-cancel:hover { background: #5a6268; }
        
        /* Styles de validation */
        .error-field { border-color: #dc3545 !important; box-shadow: 0 0 5px rgba(220,53,69,0.3) !important; }
        .success-field { border-color: #28a745 !important; box-shadow: 0 0 5px rgba(40,167,69,0.3) !important; }
        .validation-error { color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: block; }
        .char-count { font-size: 0.8rem; color: #666; margin-top: 5px; }
        .char-count.warning { color: #ff9800; }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Ajouter une Organisation</h1>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="orgForm">
            <div class="form-group">
                <label for="nom">Nom de l'organisation</label>
                <input type="text" id="nom" name="nom" 
                       placeholder="Ex: Médecins Sans Frontières"
                       maxlength="100">
                <span class="validation-error" id="nomError"></span>
                <div class="char-count" id="nomCount">0/100 caractères</div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" 
                          placeholder="Décrivez l'organisation, sa mission, ses objectifs..."
                          maxlength="500"></textarea>
                <span class="validation-error" id="descriptionError"></span>
                <div class="char-count" id="descriptionCount">0/500 caractères</div>
            </div>
            
            <div>
                <button type="submit">💾 Enregistrer</button>
                <a href="organisationList.php" class="btn-cancel">❌ Annuler</a>
            </div>
        </form>
    </div>

    <script>
        // Validation côté client pour les organisations
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('orgForm');
            const fields = {
                nom: document.getElementById('nom'),
                description: document.getElementById('description')
            };

            const counters = {
                nom: document.getElementById('nomCount'),
                description: document.getElementById('descriptionCount')
            };

            // Compteur de caractères en temps réel
            fields.nom.addEventListener('input', function() {
                updateCharCount(this, counters.nom, 100);
                validateField('nom');
            });

            fields.description.addEventListener('input', function() {
                updateCharCount(this, counters.description, 500);
                validateField('description');
            });

            // Validation en temps réel pour tous les champs
            Object.keys(fields).forEach(fieldName => {
                fields[fieldName].addEventListener('blur', function() {
                    validateField(fieldName);
                });
            });

            // Validation à la soumission
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                Object.keys(fields).forEach(fieldName => {
                    if (!validateField(fieldName)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Veuillez corriger les erreurs dans le formulaire.');
                }
            });

            function updateCharCount(field, counter, maxLength) {
                const length = field.value.length;
                counter.textContent = `${length}/${maxLength} caractères`;
                if (length > maxLength * 0.8) {
                    counter.classList.add('warning');
                } else {
                    counter.classList.remove('warning');
                }
            }

            function validateField(fieldName) {
                const field = fields[fieldName];
                const errorElement = document.getElementById(fieldName + 'Error');
                const value = field.value.trim();
                
                // Réinitialiser
                field.classList.remove('error-field', 'success-field');
                errorElement.textContent = '';
                
                let isValid = true;
                let message = '';
                
                switch(fieldName) {
                    case 'nom':
                        if (!value) {
                            message = "Le nom de l'organisation est obligatoire";
                            isValid = false;
                        } else if (value.length < 2) {
                            message = "Le nom doit contenir au moins 2 caractères";
                            isValid = false;
                        } else if (value.length > 100) {
                            message = "Le nom ne peut pas dépasser 100 caractères";
                            isValid = false;
                        }
                        break;
                        
                    case 'description':
                        if (!value) {
                            message = "La description est obligatoire";
                            isValid = false;
                        } else if (value.length < 10) {
                            message = "La description doit contenir au moins 10 caractères";
                            isValid = false;
                        } else if (value.length > 500) {
                            message = "La description ne peut pas dépasser 500 caractères";
                            isValid = false;
                        }
                        break;
                }
                
                if (!isValid) {
                    field.classList.add('error-field');
                    errorElement.textContent = message;
                } else {
                    field.classList.add('success-field');
                }
                
                return isValid;
            }
        });
    </script>
</body>
</html>