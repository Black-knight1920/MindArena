<?php
require_once __DIR__."/../../../Controller/ReclamationController.php";
require_once __DIR__."/../../../Model/Reclamation.php";

$recCtrl = new ReclamationController();
$message = '';
$messageType = '';

// Récupérer la reclamation à modifier
$id = $_GET['id'] ?? 0;
$reclamationData = $recCtrl->getReclamation($id);

if (!$reclamationData) {
    header("Location: reclamationList.php");
    exit;
}

if ($_POST) {
    try {
        $updatedOrg = new Reclamation(
            $id,
            trim($_POST['nom']),
            trim($_POST['email']),
            trim($_POST['subject']),
            trim($_POST['message'])
        );
        
        // Validation côté serveur
        $validationErrors = $recCtrl->validateReclamation($updatedOrg);
        
        if (empty($validationErrors)) {
            if ($recCtrl->updateReclamation($id, $updatedOrg)) {
                $message = "✅ Reclamation modifiée avec succès!";
                $messageType = 'success';
                header("refresh:2;url=reclamationList.php");
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
    <title>Modifier la réclamation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box; transition: all 0.3s; }
        textarea { height: 120px; resize: vertical; }
        input:focus, textarea:focus { outline: none; border-color: #2196F3; box-shadow: 0 0 5px rgba(33,150,243,0.3); }
        button { background: #2196F3; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-right: 10px; transition: background 0.3s; }
        button:hover { background: #1976D2; }
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
        <h1>✏️ Modifier la reclamation #<?= $reclamationData['id'] ?></h1>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="orgForm">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" 
                       value="<?= htmlspecialchars($reclamationData['full_name'] ?? '') ?>"
                       placeholder=""
                       maxlength="50">
                <span class="validation-error" id="nomError"></span>
                <div class="char-count" id="nomCount"><?= strlen($reclamationData['full_name'] ?? '') ?>/50 caractères</div>
            </div>

            
            <div class="form-group">
                <label for="email">email</label>
                <input type="text" id="email" name="email" 
                       value="<?= htmlspecialchars($reclamationData['email'] ?? '') ?>"
                       placeholder=""
                       maxlength="50">
                <span class="validation-error" id="emailError"></span>
                <div class="char-count" id="emailCount"><?= strlen($reclamationData['email'] ?? '') ?>/50 caractères</div>
            </div>

            
            <div class="form-group">
                <label for="subject">sujet</label>
                <input type="text" id="subject" name="subject" 
                       value="<?= htmlspecialchars($reclamationData['subject'] ?? '') ?>"
                       placeholder=""
                       maxlength="50">
                <span class="validation-error" id="sujetError"></span>
                <div class="char-count" id="sujetCount"><?= strlen($reclamationData['subject'] ?? '') ?>/50 caractères</div>
            </div>
            
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" 
                          placeholder=""
                          maxlength="1000"><?= htmlspecialchars($reclamationData['message'] ?? '') ?></textarea>
                <span class="validation-error" id="messageError"></span>
                <div class="char-count" id="messageCount"><?= strlen($reclamationData['message'] ?? '') ?>/1000 caractères</div>
            </div>
            
            <div>
                <button type="submit">💾 Mettre à jour</button>
                <a href="reclamationList.php" class="btn-cancel">❌ Annuler</a>
            </div>
        </form>
    </div>

    <script>
        // Validation côté client pour la modification
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('orgForm');
            const fields = {
                nom: document.getElementById('nom'),
                email: document.getElementById('email'),
                subject: document.getElementById('subject'),
                message: document.getElementById('message')
            };

            const counters = {
                nom: document.getElementById('nomCount'),
                email: document.getElementById('emailCount'),
                subject: document.getElementById('subjectCount'),
                message: document.getElementById('messageCount')
            };

            // Compteur de caractères en temps réel
            fields.nom.addEventListener('input', function() {
                updateCharCount(this, counters.nom, 100);
                validateField('nom');
            });

            fields.message.addEventListener('input', function() {
                updateCharCount(this, counters.message, 500);
                validateField('message');
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
                            message = "Le nom est obligatoire";
                            isValid = false;
                        } else if (value.length < 2) {
                            message = "Le nom doit contenir au moins 2 caractères";
                            isValid = false;
                        } else if (value.length > 50) {
                            message = "Le nom ne peut pas dépasser 50 caractères";
                            isValid = false;
                        }
                        break;
                        
                    case 'message':
                        if (!value) {
                            message = "Le message est obligatoire";
                            isValid = false;
                        } else if (value.length < 10) {
                            message = "Le message doit contenir au moins 10 caractères";
                            isValid = false;
                        } else if (value.length > 1000) {
                            message = "Le message ne peut pas dépasser 1000 caractères";
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