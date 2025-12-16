<?php
require_once __DIR__."/../../../Controller/ReclamationController.php";
require_once __DIR__."/../../../Controller/ReponseController.php";

$recCtrl = new ReclamationController();
$repCtrl = new ReponseController();

// Récupérer la réclamation
$id = $_GET['id'] ?? 0;
$reclamation = $recCtrl->getReclamation($id);

if (!$reclamation) {
    header("Location: reclamationList.php");
    exit;
}

// Récupérer les réponses de cette réclamation
$reponses = $repCtrl->getReponsesByReclamationId($id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de la réclamation #<?= $reclamation['id'] ?></title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px; 
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }
        .reclamation-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #2196F3;
        }
        .reclamation-field {
            margin-bottom: 15px;
        }
        .reclamation-label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }
        .reclamation-value {
            color: #666;
            padding: 8px;
            background: white;
            border-radius: 4px;
        }
        .reponses-section {
            margin-top: 30px;
        }
        .reponses-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn { 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            font-size: 14px;
            margin: 5px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn-primary { background: #2196F3; color: white; }
        .btn-primary:hover { background: #1976D2; }
        .btn-success { background: #4CAF50; color: white; }
        .btn-success:hover { background: #45a049; }
        .btn-danger { background: #f44336; color: white; }
        .btn-danger:hover { background: #da190b; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .reponse-card {
            background: white;
            border: 1px solid #ddd;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .reponse-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .reponse-date {
            color: #666;
            font-size: 0.9rem;
        }
        .reponse-content {
            color: #333;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .no-reponses {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Réclamation #<?= $reclamation['id'] ?></h1>
            <div>
                <a href="reclamationList.php" class="btn btn-secondary">← Retour à la liste</a>
                <a href="addReponse.php?id=<?= $reclamation['id'] ?>" class="btn btn-success">+ Ajouter une réponse</a>
            </div>
        </div>

        <!-- Détails de la réclamation -->
        <div class="reclamation-card">
            <h2 style="margin-top: 0; color: #2196F3;">Informations de la réclamation</h2>
            <div class="reclamation-field">
                <span class="reclamation-label">Nom complet :</span>
                <div class="reclamation-value"><?= htmlspecialchars($reclamation['full_name']) ?></div>
            </div>
            <div class="reclamation-field">
                <span class="reclamation-label">Email :</span>
                <div class="reclamation-value"><?= htmlspecialchars($reclamation['email']) ?></div>
            </div>
            <div class="reclamation-field">
                <span class="reclamation-label">Sujet :</span>
                <div class="reclamation-value"><?= htmlspecialchars($reclamation['subject']) ?></div>
            </div>
            <div class="reclamation-field">
                <span class="reclamation-label">Message :</span>
                <div class="reclamation-value" style="white-space: pre-wrap;"><?= htmlspecialchars($reclamation['message']) ?></div>
            </div>
            <?php if (isset($reclamation['created_at'])): ?>
            <div class="reclamation-field">
                <span class="reclamation-label">Date de création :</span>
                <div class="reclamation-value"><?= date('d/m/Y à H:i', strtotime($reclamation['created_at'])) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Section des réponses -->
        <div class="reponses-section">
            <div class="reponses-header">
                <h2>💬 Réponses (<?= count($reponses) ?>)</h2>
                <span class="badge <?= count($reponses) > 0 ? 'badge-success' : 'badge-warning' ?>">
                    <?= count($reponses) > 0 ? 'Répondu' : 'Non répondu' ?>
                </span>
            </div>

            <?php if (empty($reponses)): ?>
                <div class="no-reponses">
                    <p>Aucune réponse pour cette réclamation.</p>
                    <a href="addReponse.php?id=<?= $reclamation['id'] ?>" class="btn btn-success">Ajouter la première réponse</a>
                </div>
            <?php else: ?>
                <?php foreach ($reponses as $reponse): ?>
                    <div class="reponse-card">
                        <div class="reponse-header">
                            <strong>Réponse #<?= $reponse['id'] ?></strong>
                            <div>
                                <span class="reponse-date">
                                    <?= isset($reponse['updated_at']) ? date('d/m/Y à H:i', strtotime($reponse['updated_at'])) : 'Date non disponible' ?>
                                </span>
                                <a href="deleteReponse.php?id=<?= $reponse['id'] ?>&reclamation_id=<?= $reclamation['id'] ?>" 
                                   class="btn btn-danger" 
                                   style="padding: 5px 10px; margin-left: 10px;"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?')">
                                    Supprimer
                                </a>
                            </div>
                        </div>
                        <div class="reponse-content">
                            <?= nl2br(htmlspecialchars($reponse['message'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
