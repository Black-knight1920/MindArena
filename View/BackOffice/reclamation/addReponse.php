<?php
require_once __DIR__."/../../../Controller/ReclamationController.php";
require_once __DIR__."/../../../Controller/ReponseController.php";
require_once __DIR__."/../../../Model/Reponse.php";

$recCtrl = new ReclamationController();
$repCtrl = new ReponseController();
$message = '';
$messageType = '';

// Récupérer l'ID de la réclamation
$reclamation_id = $_GET['id'] ?? 0;
$reclamation = $recCtrl->getReclamation($reclamation_id);

if (!$reclamation) {
    header("Location: reclamationList.php");
    exit;
}

if ($_POST) {
    try {
        // Créer une nouvelle instance de Reponse avec les données soumises
        $reponse = new Reponse(
            null,  // ID est NULL car il sera auto-incrémenté
            $reclamation_id,
            trim($_POST['response']),
            null  // La date de réponse sera gérée automatiquement par la base de données
        );
        
        // Validation côté serveur
        $validationErrors = $repCtrl->validateReponse($reponse);
        
        if (empty($validationErrors)) {
            // Ajouter la réponse dans la base de données
            if ($repCtrl->addReponse($reponse)) {
                $message = "✅ Réponse ajoutée avec succès!";
                $messageType = 'success';
                header("refresh:2;url=viewReclamation.php?id=" . $reclamation_id);
                exit;
            } else {
                $message = "❌ Erreur lors de l'ajout de la réponse.";
                $messageType = 'error';
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
    <title>Ajouter une réponse</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 800px; 
            margin: 20px auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .reclamation-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #2196F3;
        }
        .reclamation-info h3 {
            margin-top: 0;
            color: #2196F3;
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: bold; 
            color: #333; 
        }
        textarea { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            font-size: 16px; 
            box-sizing: border-box; 
            transition: all 0.3s;
            font-family: Arial, sans-serif;
            min-height: 150px;
            resize: vertical;
        }
        textarea:focus { 
            outline: none; 
            border-color: #4CAF50; 
            box-shadow: 0 0 5px rgba(76,175,80,0.3); 
        }
        button { 
            background: #4CAF50; 
            color: white; 
            padding: 12px 30px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px;
            margin-right: 10px;
            transition: background 0.3s;
        }
        button:hover { 
            background: #45a049; 
        }
        .message { 
            padding: 15px; 
            margin: 20px 0; 
            border-radius: 5px; 
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb; 
        }
        .btn-cancel { 
            background: #6c757d; 
            color: white; 
            text-decoration: none; 
            padding: 12px 20px; 
            border-radius: 5px; 
            display: inline-block;
            transition: background 0.3s;
        }
        .btn-cancel:hover { 
            background: #5a6268; 
        }
        .error-field { 
            border-color: #dc3545 !important; 
            box-shadow: 0 0 5px rgba(220,53,69,0.3) !important; 
        }
        .success-field { 
            border-color: #28a745 !important; 
            box-shadow: 0 0 5px rgba(40,167,69,0.3) !important; 
        }
        .validation-error { 
            color: #dc3545; 
            font-size: 0.85rem; 
            margin-top: 5px; 
            display: block; 
        }
        .char-count { 
            font-size: 0.8rem; 
            color: #666; 
            margin-top: 5px; 
        }
        .char-count.warning { 
            color: #ff9800; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Ajouter une réponse</h1>
        
        <!-- Informations de la réclamation -->
        <div class="reclamation-info">
            <h3>📋 Réclamation #<?= $reclamation['id'] ?></h3>
            <p><strong>De :</strong> <?= htmlspecialchars($reclamation['full_name']) ?> (<?= htmlspecialchars($reclamation['email']) ?>)</p>
            <p><strong>Sujet :</strong> <?= htmlspecialchars($reclamation['subject']) ?></p>
            <p><strong>Message :</strong> <?= htmlspecialchars(substr($reclamation['message'], 0, 100)) ?><?= strlen($reclamation['message']) > 100 ? '...' : '' ?></p>
        </div>

        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="reponseForm">
            <div class="form-group">
                <label for="response">Votre réponse *</label>
                <textarea id="response" name="response" 
                          placeholder="Rédigez votre réponse à cette réclamation..."
                          maxlength="2000"
                          required><?= isset($_POST['response']) ? htmlspecialchars($_POST['response']) : '' ?></textarea>
                <span class="validation-error" id="responseError"></span>
                <div class="char-count" id="responseCount">0/2000 caractères</div>
            </div>
            
            <div>
                <button type="submit">💾 Enregistrer la réponse</button>
                <a href="viewReclamation.php?id=<?= $reclamation_id ?>" class="btn-cancel">❌ Annuler</a>
            </div>
        </form>
    </div>

    <script>
        // Validation côté client
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reponseForm');
            const responseField = document.getElementById('response');
            const responseError = document.getElementById('responseError');
            const responseCount = document.getElementById('responseCount');

            // Compteur de caractères
            responseField.addEventListener('input', function() {
                const length = this.value.length;
                responseCount.textContent = `${length}/2000 caractères`;
                if (length > 1600) {
                    responseCount.classList.add('warning');
                } else {
                    responseCount.classList.remove('warning');
                }
                validateResponse();
            });

            // Validation en temps réel
            responseField.addEventListener('blur', validateResponse);

            // Validation à la soumission
            form.addEventListener('submit', function(e) {
                if (!validateResponse()) {
                    e.preventDefault();
                    alert('Veuillez corriger les erreurs dans le formulaire.');
                }
            });

            function validateResponse() {
                const value = responseField.value.trim();
                
                // Réinitialiser
                responseField.classList.remove('error-field', 'success-field');
                responseError.textContent = '';
                
                let isValid = true;
                let message = '';
                
                if (!value) {
                    message = "Le texte de la réponse est obligatoire";
                    isValid = false;
                } else if (value.length < 5) {
                    message = "La réponse doit contenir au moins 5 caractères";
                    isValid = false;
                } else if (value.length > 2000) {
                    message = "La réponse ne peut pas dépasser 2000 caractères";
                    isValid = false;
                }
                
                if (!isValid) {
                    responseField.classList.add('error-field');
                    responseError.textContent = message;
                } else {
                    responseField.classList.add('success-field');
                }
                
                return isValid;
            }

            // Initialiser le compteur
            if (responseField.value) {
                responseField.dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>