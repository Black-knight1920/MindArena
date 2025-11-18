<?php
require_once __DIR__ . "/../../Controller/categorieback.php";
require_once __DIR__ . "/../../Controller/jeuxback.php";

$categorieController = new CategorieBackController();
$jeuxController = new JeuxBackController();

$categories = $categorieController->getAllCategories();
$jeux = $jeuxController->getAllJeux();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BackOffice - Gestion des Jeux</title>
    <link rel="stylesheet" href="back.css">

</head>
<body>
    <div class="container">
        <header>
            <h1>BackOffice - Gestion des Jeux</h1>
            <nav>
                <a href="ajoutercategorie.php" class="btn">Ajouter Catégorie</a>
                <a href="ajouterjeux.php" class="btn">Ajouter Jeu</a>
                <a href="http://localhost/crud-gestion%20des%20jeux/View/FrontOffice/front.php" class="btn">Voir FrontOffice</a>
            </nav>
        </header>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <section class="section">
            <h2>Gestion des Catégories</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $categorie): ?>
                        <tr>
                            <td><?= $categorie['id'] ?></td>
                            <td><?= htmlspecialchars($categorie['nom']) ?></td>
                            <td><?= htmlspecialchars($categorie['description']) ?></td>
                            <td class="actions">
                                <a href="modifiercategorie.php?id=<?= $categorie['id'] ?>" class="btn-edit">Modifier</a>
                                <form action="../../Controller/categorieback.php" method="POST" class="inline-form">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $categorie['id'] ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="section">
            <h2>Gestion des Jeux</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Prix</th>
                            <th>Catégorie</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jeux as $jeu): ?>
                        <tr>
                            <td><?= $jeu['id'] ?></td>
                            <td>
                                <?php if ($jeu['image']): ?>
                                    <img src="../../uploads/<?= $jeu['image'] ?>" alt="<?= htmlspecialchars($jeu['titre']) ?>" class="image-preview">
                                <?php else: ?>
                                    <span class="no-image">Aucune image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($jeu['titre']) ?></td>
                            <td><?= htmlspecialchars(substr($jeu['description'], 0, 50)) ?>...</td>
                            <td><?= $jeu['prix'] ?> €</td>
                            <td><?= htmlspecialchars($jeu['categorie_nom']) ?></td>
                            <td class="actions">
                                <a href="modifierjeux.php?id=<?= $jeu['id'] ?>" class="btn-edit">Modifier</a>
                                <form action="../../Controller/jeuxback.php" method="POST" class="inline-form">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $jeu['id'] ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
