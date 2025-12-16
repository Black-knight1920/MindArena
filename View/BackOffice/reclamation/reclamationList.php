<?php 
require_once __DIR__."/../../../Controller/ReclamationController.php";
require_once __DIR__."/../../../Controller/DonController.php";

$recCtrl = new ReclamationController();

// Récupérer la valeur de la recherche et du tri
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'id'; // Le tri par défaut sera sur 'id'

// Effectuer la requête pour filtrer les réclamations en fonction de la recherche et du tri
$reclamations = $recCtrl->listReclamation($search);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des réclamations</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin: 20px 0; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: center; 
        }
        th { 
            background-color: #081624; 
            color: white; 
        }
        .btn { 
            padding: 8px 16px; 
            text-decoration: none; 
            border-radius: 5px; 
            font-size: 14px;
            margin: 2px; 
        }
        .btn-add { 
            background: #4CAF50; 
            color: white; 
        }
        .btn-edit { 
            background: #2196F3; 
            color: white; 
        }
        .btn-delete { 
            background: #f44336; 
            color: white; 
        }
        .btn-reply { 
            background: #FFC107; 
            color: white; 
        }
        .btn-back { 
            background: #FF9800; 
            color: white; 
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
        }
        .search-bar {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }
        .search-bar input[type="text"] {
            padding: 8px;
            width: 60%;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .search-bar button {
            padding: 8px 16px;
            background-color: #2196F3;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Gestion des réclamations</h1>
            <div>
                <a href="addReclamation.php" class="btn btn-add">+ Nouvelle Reclamation</a>
                <a href="../don/donList.php" class="btn btn-back">Gestion des Dons</a>
                <a href="../../../backoffices.php" class="btn" style="background: #6c757d;">Accueil Admin</a>
            </div>
        </div>

        <!-- Formulaire de recherche -->
        <div class="search-bar">
            <form method="GET" action="reclamationList.php">
                <input type="text" name="search" placeholder="Rechercher par nom..."
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                <button type="submit">🔍 Rechercher</button>
            </form>
        </div>

        <!-- Tableau des réclamations -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Sujet</th>
                    <th>Message</th>
                    <th>Date</th> <!-- Lien de tri -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reclamations)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Aucune réclamation trouvée</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reclamations as $recla): ?>
                        <tr>
                            <td><?= $recla['id'] ?></td>
                            <td><?= htmlspecialchars($recla['full_name']) ?></td>
                            <td><?= htmlspecialchars($recla['email']) ?></td>
                            <td><?= htmlspecialchars($recla['subject']) ?></td>
                            <td><?= htmlspecialchars($recla['message']) ?></td>
                            <td><?= date('d/m/Y à H:i', strtotime($recla['created_at'])) ?></td> <!-- Affichage de la date -->
                            <td>
                                <a href="modifyReclamation.php?id=<?= $recla['id'] ?>" class="btn btn-edit">Modifier</a>
                                <a href="deleteReclamation.php?id=<?= $recla['id'] ?>" class="btn btn-delete" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?')">
                                   Supprimer
                                </a>
                                <a href="addReponse.php?id=<?= $recla['id'] ?>" class="btn btn-reply">Répondre</a>
                                <a href="generatePDF.php?id=<?= $recla['id'] ?>" class="btn" style="background-color: #FF5733;">📄</a> <!-- Bouton PDF -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

