<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un Jeu - BackOffice</title>
    <link rel="stylesheet" href="back.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>BackOffice - Supprimer un Jeu</h1>
            <nav class="admin-nav">
                <a href="jeuxback.php">Jeux</a>
                <a href="categorieback.php">Catégories</a>
                <a href="../FrontOffice/front.php">Site Public</a>
                <a href="logout.php">Déconnexion</a>
            </nav>
        </header>

        <div class="admin-main">
            <aside class="admin-sidebar">
                <h3>Navigation</h3>
                <ul>
                    <li><a href="jeuxback.php">Liste des Jeux</a></li>
                    <li><a href="jeuxback.php?action=create">Nouveau Jeu</a></li>
                    <li><a href="categorieback.php">Gestion des Catégories</a></li>
                </ul>
            </aside>

            <main class="admin-content">
                <?php
                // Inclure les classes nécessaires
                include_once '../config.php';
                include_once '../Model/jeux.php';
                include_once '../Model/categorie.php';

                $database = new Database();
                $db = $database->getConnection();

                $jeu = new Jeu($db);
                $categorie = new Categorie($db);

                // Récupérer l'ID du jeu à supprimer
                $jeu_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID non trouvé.');
                
                // Lire les informations du jeu
                $jeu->id = $jeu_id;
                $jeu->readOne();
                ?>

                <div class="admin-confirmation">
                    <div class="confirmation-header">
                        <span class="confirmation-icon">🗑️</span>
                        <h2>Confirmation de suppression</h2>
                    </div>
                    
                    <div class="confirmation-content">
                        <div class="item-details">
                            <h3>Jeu à supprimer :</h3>
                            <div class="detail-card">
                                <div class="detail-header">
                                    <strong><?php echo htmlspecialchars($jeu->titre); ?></strong>
                                    <span class="badge badge-<?php echo strtolower($jeu->difficulte); ?>">
                                        <?php echo $jeu->difficulte; ?>
                                    </span>
                                </div>
                                <div class="detail-meta">
                                    <span><strong>Type:</strong> <?php echo htmlspecialchars($jeu->type); ?></span>
                                    <span><strong>Thème:</strong> <?php echo htmlspecialchars($jeu->theme); ?></span>
                                    <span><strong>Catégorie:</strong> <?php echo htmlspecialchars($jeu->categorie_nom); ?></span>
                                </div>
                                <?php if($jeu->description): ?>
                                    <div class="detail-description">
                                        <strong>Description:</strong>
                                        <p><?php echo htmlspecialchars($jeu->description); ?></p>
                                    </div>
                                <?php endif; ?>
                                <div class="detail-id">
                                    <strong>ID:</strong> <?php echo $jeu->id; ?>
                                </div>
                            </div>
                        </div>

                        <div class="warning-section">
                            <div class="admin-alert admin-alert-danger">
                                <div class="alert-header">
                                    <span class="alert-icon">⚠️</span>
                                    <h4>Action irréversible</h4>
                                </div>
                                <p>Vous êtes sur le point de supprimer définitivement ce jeu de la base de données.</p>
                                <ul>
                                    <li>Cette action ne peut pas être annulée</li>
                                    <li>Toutes les données du jeu seront perdues</li>
                                    <li>Les statistiques associées seront supprimées</li>
                                </ul>
                            </div>

                            <div class="impact-analysis">
                                <h5>Impact de cette suppression :</h5>
                                <div class="impact-items">
                                    <div class="impact-item">
                                        <span class="impact-icon">📊</span>
                                        <div class="impact-text">
                                            <strong>Données statistiques</strong>
                                            <span>Toutes les données de ce jeu seront définitivement effacées</span>
                                        </div>
                                    </div>
                                    <div class="impact-item">
                                        <span class="impact-icon">🔗</span>
                                        <div class="impact-text">
                                            <strong>Liens associés</strong>
                                            <span>Les éventuelles relations avec d'autres données seront rompues</span>
                                        </div>
                                    </div>
                                    <div class="impact-item">
                                        <span class="impact-icon">📝</span>
                                        <div class="impact-text">
                                            <strong>Historique</strong>
                                            <span>L'historique de ce jeu ne sera plus accessible</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="confirmation-actions">
                            <div class="safe-actions">
                                <h5>Alternatives à la suppression :</h5>
                                <div class="alternative-buttons">
                                    <a href="jeuxback.php?action=update&id=<?php echo $jeu_id; ?>" class="btn btn-primary">
                                        <span class="btn-icon">✏️</span>
                                        Modifier le jeu
                                    </a>
                                    <a href="jeuxback.php" class="btn btn-secondary">
                                        <span class="btn-icon">📋</span>
                                        Retour à la liste
                                    </a>
                                </div>
                            </div>

                            <div class="danger-actions">
                                <h5>Confirmer la suppression :</h5>
                                <div class="danger-buttons">
                                    <a href="jeuxback.php" class="btn">Annuler</a>
                                    <a href="jeuxback.php?action=delete&id=<?php echo $jeu_id; ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirmFinalSuppression()">
                                       <span class="btn-icon">🗑️</span>
                                       Supprimer définitivement
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function confirmFinalSuppression() {
            const jeuTitre = "<?php echo addslashes($jeu->titre); ?>";
            const confirmationMessage = `CONFIRMATION FINALE DE SUPPRESSION\n\n` +
                                      `Êtes-vous ABSOLUMENT SÛR de vouloir supprimer le jeu :\n` +
                                      `"${jeuTitre}" ?\n\n` +
                                      `⚠️  CETTE ACTION EST DÉFINITIVE ET IRRÉVERSIBLE !\n` +
                                      `Toutes les données seront perdues définitivement.\n\n` +
                                      `Tapez "SUPPRIMER" pour confirmer :`;
            
            const userInput = prompt(confirmationMessage);
            return userInput === "SUPPRIMER";
        }

        // Protection contre les clics accidentels
        document.addEventListener('DOMContentLoaded', function() {
            const dangerBtn = document.querySelector('.btn-danger');
            if (dangerBtn) {
                let clickCount = 0;
                const originalHref = dangerBtn.href;
                
                dangerBtn.addEventListener('click', function(e) {
                    clickCount++;
                    if (clickCount === 1) {
                        e.preventDefault();
                        dangerBtn.innerHTML = '<span class="btn-icon">⚠️</span> Cliquez à nouveau pour confirmer';
                        dangerBtn.style.background = '#f39c12';
                        
                        // Réinitialiser après 3 secondes
                        setTimeout(() => {
                            clickCount = 0;
                            dangerBtn.innerHTML = '<span class="btn-icon">🗑️</span> Supprimer définitivement';
                            dangerBtn.style.background = '';
                        }, 3000);
                    }
                });
            }
        });
    </script>

    <style>
        .admin-confirmation {
            max-width: 800px;
            margin: 0 auto;
        }

        .confirmation-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e74c3c;
        }

        .confirmation-icon {
            font-size: 3rem;
            display: block;
            margin-bottom: 1rem;
        }

        .confirmation-header h2 {
            color: #e74c3c;
            margin: 0;
        }

        .confirmation-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .item-details {
            padding: 2rem;
            border-bottom: 1px solid #ecf0f1;
        }

        .item-details h3 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }

        .detail-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 4px;
            border-left: 4px solid #3498db;
        }

        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .detail-header strong {
            font-size: 1.4rem;
            color: #2c3e50;
            flex: 1;
            margin-right: 1rem;
        }

        .detail-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-meta span {
            color: #5d6d7e;
        }

        .detail-meta strong {
            color: #2c3e50;
        }

        .detail-description {
            margin-bottom: 1rem;
        }

        .detail-description strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 0.5rem;
        }

        .detail-description p {
            color: #5d6d7e;
            line-height: 1.5;
            margin: 0;
            padding: 0.75rem;
            background: white;
            border-radius: 4px;
            border-left: 3px solid #ecf0f1;
        }

        .detail-id {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-align: right;
        }

        .warning-section {
            padding: 2rem;
            background: #fef9f9;
        }

        .admin-alert {
            padding: 1.5rem;
            border-radius: 4px;
            margin-bottom: 2rem;
        }

        .admin-alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .alert-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .alert-header h4 {
            margin: 0;
            color: #721c24;
        }

        .admin-alert ul {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }

        .admin-alert li {
            margin-bottom: 0.5rem;
        }

        .impact-analysis {
            margin-top: 2rem;
        }

        .impact-analysis h5 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .impact-items {
            display: grid;
            gap: 1rem;
        }

        .impact-item {
            display: flex;
            align-items: flex-start;
            padding: 1rem;
            background: white;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
        }

        .impact-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .impact-text {
            flex: 1;
        }

        .impact-text strong {
            display: block;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .impact-text span {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .confirmation-actions {
            padding: 2rem;
            background: #f8f9fa;
            border-top: 1px solid #ecf0f1;
        }

        .safe-actions,
        .danger-actions {
            margin-bottom: 2rem;
        }

        .safe-actions:last-child,
        .danger-actions:last-child {
            margin-bottom: 0;
        }

        .safe-actions h5,
        .danger-actions h5 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .alternative-buttons,
        .danger-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .danger-actions {
            padding-top: 1.5rem;
            border-top: 2px solid #e74c3c;
        }

        .danger-actions h5 {
            color: #e74c3c;
        }

        @media (max-width: 768px) {
            .detail-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .detail-header strong {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }

            .detail-meta {
                grid-template-columns: 1fr;
            }

            .alternative-buttons,
            .danger-buttons {
                flex-direction: column;
            }

            .impact-item {
                flex-direction: column;
                text-align: center;
            }

            .impact-icon {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</body>
</html>