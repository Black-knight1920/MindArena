<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer une Catégorie</title>
    <link rel="stylesheet" href="front.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Supprimer une Catégorie</h1>
            <nav>
                <a href="categoriefront.php">Retour à la liste</a>
                <a href="../FrontOffice/front.php">Accueil</a>
            </nav>
        </header>

        <main>
            <?php
            // Inclure les classes nécessaires
            include_once '../config.php';
            include_once '../Model/categorie.php';
            include_once '../Model/jeux.php';

            $database = new Database();
            $db = $database->getConnection();

            $categorie = new Categorie($db);
            $jeu = new Jeu($db);

            // Récupérer l'ID de la catégorie à supprimer
            $categorie_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID non trouvé.');
            
            // Lire les informations de la catégorie
            $categorie->id = $categorie_id;
            $categorie->readOne();

            // Vérifier s'il y a des jeux associés à cette catégorie
            $jeux_associes = $jeu->readByCategorie($categorie_id);
            $nombre_jeux = $jeux_associes->rowCount();
            ?>

            <div class="confirmation-container">
                <div class="confirmation-card">
                    <h2>Confirmation de suppression</h2>
                    
                    <div class="warning-message">
                        <h3>⚠️ Attention !</h3>
                        <p>Vous êtes sur le point de supprimer la catégorie :</p>
                        <div class="item-to-delete">
                            <strong><?php echo htmlspecialchars($categorie->nom); ?></strong>
                            <?php if($categorie->description): ?>
                                <p><?php echo htmlspecialchars($categorie->description); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if($nombre_jeux > 0): ?>
                            <div class="alert alert-danger">
                                <h4>❌ Impossible de supprimer cette catégorie</h4>
                                <p>Cette catégorie contient <strong><?php echo $nombre_jeux; ?> jeu(x)</strong> associé(s).</p>
                                <p>Vous devez d'abord supprimer ou déplacer ces jeux avant de pouvoir supprimer la catégorie.</p>
                                
                                <div class="associated-items">
                                    <h5>Jeux associés :</h5>
                                    <ul>
                                        <?php while ($jeu_associe = $jeux_associes->fetch(PDO::FETCH_ASSOC)): ?>
                                            <li><?php echo htmlspecialchars($jeu_associe['titre']); ?> (ID: <?php echo $jeu_associe['id']; ?>)</li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="actions">
                                <a href="categoriefront.php" class="btn">Retour à la liste</a>
                                <a href="jeuxfront.php" class="btn btn-primary">Gérer les jeux</a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <p><strong>Cette action est irréversible !</strong></p>
                                <p>Êtes-vous sûr de vouloir supprimer définitivement cette catégorie ?</p>
                            </div>

                            <div class="actions">
                                <a href="categoriefront.php" class="btn">Annuler</a>
                                <a href="categoriefront.php?action=delete&id=<?php echo $categorie_id; ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Êtes-vous ABSOLUMENT sûr de vouloir supprimer cette catégorie ?')">
                                   Confirmer la suppression
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .confirmation-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .confirmation-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #e74c3c;
        }

        .confirmation-card h2 {
            color: #e74c3c;
            margin-bottom: 1rem;
            text-align: center;
        }

        .warning-message {
            text-align: center;
        }

        .warning-message h3 {
            color: #e74c3c;
            margin-bottom: 1rem;
        }

        .item-to-delete {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
            border-left: 3px solid #3498db;
        }

        .item-to-delete strong {
            font-size: 1.2rem;
            color: #2c3e50;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .associated-items {
            text-align: left;
            margin-top: 1rem;
        }

        .associated-items h5 {
            margin-bottom: 0.5rem;
            color: #721c24;
        }

        .associated-items ul {
            list-style: none;
            padding: 0;
        }

        .associated-items li {
            padding: 0.3rem 0;
            border-bottom: 1px solid #f5c6cb;
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .actions {
                flex-direction: column;
            }
            
            .actions .btn {
                text-align: center;
            }
        }
    </style>
</body>
</html>