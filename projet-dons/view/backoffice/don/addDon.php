<?php
require_once __DIR__."/../../../Controller/DonController.php";
require_once __DIR__."/../../../Controller/OrganisationController.php";
require_once __DIR__."/../../../Model/Don.php";

$donCtrl = new DonController();
$orgCtrl = new OrganisationController();
$message = '';
$messageType = '';

if ($_POST) {
    // Validation des données avant création de l'objet
    $montant = $_POST['montant'] ?? '';
    $dateDon = $_POST['dateDon'] ?? '';
    $typeDon = $_POST['typeDon'] ?? '';
    $organisationId = $_POST['organisationId'] ?? '';
    
    // Validation basique des champs requis
    if (empty($montant) || empty($dateDon) || empty($typeDon) || empty($organisationId)) {
        $message = " Tous les champs sont obligatoires";
        $messageType = 'error';
    } else {
        // Création de l'objet Don
        $don = new Don(
            null,
            (float)$montant,
            new DateTime($dateDon),
            $typeDon,
            (int)$organisationId
        );
        
        // Validation côté serveur
        $validationErrors = $donCtrl->validateDon($don);
        //Ajout dans la base de données
        if (empty($validationErrors)) {
            if ($donCtrl->addDon($don)) {
                $message = "✅ Don ajouté avec succès!";
                $messageType = 'success';
                header("refresh:2;url=donList.php");
            } else {
                $message = " Erreur lors de l'ajout du don";
                $messageType = 'error';
            }
        } else {
            $message = " Erreurs de validation:<br> " . implode("<br> ", $validationErrors);
            $messageType = 'error';
        }
    }
}

// Récupérer les organisations avec leurs montants depuis la BD
$organisations = $orgCtrl->listOrganisations();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Don</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; background: #f5f5f5ff; }
        .container { 
            max-width: 600px; 
            margin: 20px auto; 
            background: white;
            padding: 30px; 
            border-radius: 10px;
            box-shadow: 0 2px 10px #0000001a; }
        
        .form-group { 
            margin-bottom: 20px; }
        label { 
            display: block;
            margin-bottom: 8px; 
            font-weight: bold; 
            color: #333; }
        /*champs de saisie*/
        input, select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            font-size: 16px; 
            box-sizing: border-box; 
            transition: all 0.3s; }
        input:focus, select:focus { 
            outline: none; 
            border-color: #4CAF50; 
            box-shadow: 0 0 5px #4caf504d; }
        button { 
            background: #4CAF50; 
            color: white; 
            border: none; 
            border-radius: 6px;
            padding: 14px 20px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            width: 30%; 
            transition: background-color 0.3s;
            margin-top: 10px; }
        button:hover { background-color: #2980b9; }
        
        .message { 
            padding: 15px; 
            margin: 20px 0; 
            border-radius: 5px; }
        .success { 
            background: #d4edda; 
            color: #155724; 
            border: 1px solid #261c82ff; }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb; }
        .btn-cancel { background: #6c757d; 
            color: white; 
            text-decoration: none; 
            padding: 12px 20px; 
            border-radius: 5px; 
            display: inline-block; }
        .btn-cancel:hover { background: #5a6268; }
        
        /* Styles de validation */
        .error-field { 
            border-color: #dc3545 !important; 
            box-shadow: 0 0 5px rgba(220,53,69,0.3) !important; }
        .success-field {
            border-color: #28a745 !important;
            box-shadow: 0 0 5px rgba(40,167,69,0.3) !important; }
        .validation-error { 
            color: #dc3545;
            font-size: 0.85rem; 
            margin-top: 5px; display: block; }
        .montant-info { 
            background: #f8f9fa; 
            padding: 10px; 
            border-radius: 5px; 
            margin-top: 5px; 
            font-size: 0.9em; }
        .montant-actuel { color: #666; }
        .montant-futur { 
            color: #4CAF50; 
            font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Ajouter un Don</h1>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>"> 
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="donForm">
            <div class="form-group">
                <label for="montant">Montant (€)</label>
                <input type="number" id="montant" name="montant" placeholder="Ex: 50.00" >
                <span class="validation-error" id="montantError"></span>
            </div>
            
            <div class="form-group">
                <label for="dateDon">Date du don</label>
                <input type="date" id="dateDon" name="dateDon" >
                <span class="validation-error" id="dateDonError"></span>
            </div>
            
            <div class="form-group">
                <label for="typeDon">Type de don</label>
                <select id="typeDon" name="typeDon">
                    <option value="">-- Choisir un type --</option>
                    <option value="Monétaire">Monétaire</option>
                    <option value="Matériel">Matériel</option>
                </select>
                <span class="validation-error" id="typeDonError"></span>
            </div>
            
            <div class="form-group">
                <label for="organisationId">Organisation</label> 
                <select id="organisationId" name="organisationId">
                    <option value="">-- Sélectionner une organisation --</option>
                    <?php foreach ($organisations as $org): ?>
                        <option value="<?= $org['id'] ?>" data-montant="<?= $org['montant_total'] ?? 0 ?>">
                            <?= htmlspecialchars($org['nom']) ?> <!-- protection XSS (script) -->
                            (Total: <?= number_format($org['montant_total'] ?? 0, 2) ?> €)
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="validation-error" id="organisationIdError"></span>
                <div id="montantInfo" class="montant-info" style="display: none;">
                    <div class="montant-actuel">Montant actuel: <strong id="montantActuel">0.00 €</strong></div>
                    <div class="montant-futur">Après ce don: <strong id="montantFutur">0.00 €</strong></div>
                </div>
            </div>
            
            <div>
                <button type="submit">💾 Enregistrer</button>
                <a href="donList.php" class="btn-cancel">❌ Annuler</a>
            </div>
        </form>
    </div>

    <script>
        // Afficher le montant actuel de l'organisation sélectionnée
        document.getElementById('organisationId').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const montantActuel = parseFloat(selectedOption.getAttribute('data-montant')) || 0;
            const nouveauMontant = parseFloat(document.getElementById('montant').value) || 0;
            const totalApresDon = montantActuel + nouveauMontant;
            
            if (this.value) {
                document.getElementById('montantInfo').style.display = 'block';
                document.getElementById('montantActuel').textContent = montantActuel.toFixed(2) + ' €';
                document.getElementById('montantFutur').textContent = totalApresDon.toFixed(2) + ' €';
            } else {
                document.getElementById('montantInfo').style.display = 'none'; //rend l'element invisible
            }
        });

        // Mettre à jour aussi quand le montant change
        document.getElementById('montant').addEventListener('input', function() {
            const organisationSelect = document.getElementById('organisationId');
            if (organisationSelect.value) {
                organisationSelect.dispatchEvent(new Event('change'));
            }
        });

        // Validation côté client
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('donForm');
            const fields = {
                montant: document.getElementById('montant'),
                dateDon: document.getElementById('dateDon'),
                typeDon: document.getElementById('typeDon'),
                organisationId: document.getElementById('organisationId')
            };

            // Validation en temps réel
            Object.keys(fields).forEach(fieldName => {
                fields[fieldName].addEventListener('input', function() {
                    validateField(fieldName);
                });
                
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
                    case 'montant':
                        const montant = parseFloat(value);
                        if (!value) {
                            message = "Le montant est obligatoire";
                            isValid = false;
                        } else if (isNaN(montant)) {
                            message = "Veuillez entrer un montant valide";
                            isValid = false;
                        } else if (montant <= 0) {
                            message = "Le montant doit être supérieur à 0€";
                            isValid = false;
                        } else if (montant > 1000000) {
                            message = "Le montant ne peut pas dépasser 1,000,000€";
                            isValid = false;
                        }
                        break;
                        
                    case 'dateDon':
                        if (!value) {
                            message = "La date est obligatoire";
                            isValid = false;
                        } else {
                            const selectedDate = new Date(value);
                            const today = new Date();
                            if (selectedDate > today) {
                                message = "La date ne peut pas être dans le futur";
                                isValid = false;
                            }
                        }
                        break;
                        
                    case 'typeDon':
                        if (!value) {
                            message = "Veuillez sélectionner un type de don";
                            isValid = false;
                        }
                        break;
                        
                    case 'organisationId':
                        if (!value) {
                            message = "Veuillez sélectionner une organisation";
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