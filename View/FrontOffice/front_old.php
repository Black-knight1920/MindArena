<?php
// --- Initialisations pour éviter les warnings si le contrôleur n'a pas défini ces variables ---
if (!isset($message)) {
    $message = null;
}
if (!isset($jeux_list) || !is_array($jeux_list)) {
    $jeux_list = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Jeux - FrontOffice</title>
    <link rel="stylesheet" href="front.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🎮 Gestion des Jeux</h1>
            <nav>
                <a href="front.php" class="active">🏠 Accueil</a>
                <a href="ajouterjeux.php?action=create">➕ Ajouter un Jeu</a>
                <a href="ajoutercategorie.php?action=create">📂 Catégories</a>
            </nav>
        </header>

        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <main>
            <div class="page-header">
                <h2>📋 Liste des Jeux</h2>

                <!-- ❌ BOUTON AJOUT RETIRÉ ICI -->
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="🔍 Rechercher un jeu...">
                    </div>
                </div>
            </div>

            <?php if (count($jeux_list) > 0): ?>
                <div class="stats-bar">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($jeux_list); ?></span>
                        <span class="stat-label">jeux au total</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count(array_unique(array_column($jeux_list, 'categorie_id'))); ?></span>
                        <span class="stat-label">catégories</span>
                    </div>
                </div>

                <div class="jeux-grid" id="jeuxGrid">
                    <?php foreach ($jeux_list as $jeu): ?>
                        <?php
                        $titre = htmlspecialchars($jeu['titre'] ?? 'Titre non défini', ENT_QUOTES, 'UTF-8');
                        $type = htmlspecialchars($jeu['type'] ?? '-', ENT_QUOTES, 'UTF-8');
                        $theme = htmlspecialchars($jeu['theme'] ?? '-', ENT_QUOTES, 'UTF-8');
                        $categorie_nom = htmlspecialchars($jeu['categorie_nom'] ?? '-', ENT_QUOTES, 'UTF-8');
                        $difficulte = htmlspecialchars($jeu['difficulte'] ?? 'inconnu', ENT_QUOTES, 'UTF-8');
                        $description = htmlspecialchars($jeu['description'] ?? '', ENT_QUOTES, 'UTF-8');
                        $id = htmlspecialchars($jeu['id'] ?? '', ENT_QUOTES, 'UTF-8');
                        $date_creation = $jeu['date_creation'] ?? null;
                        $date_affiche = $date_creation ? date('d/m/Y', strtotime($date_creation)) : '-';
                        ?>
                        <div class="jeu-card"
                             data-titre="<?php echo strtolower($titre); ?>"
                             data-type="<?php echo strtolower($type); ?>"
                             data-theme="<?php echo strtolower($theme); ?>">

                            <div class="jeu-header">
                                <h3><?php echo $titre; ?></h3>
                                <span class="difficulty-badge difficulty-<?php echo strtolower($difficulte); ?>">
                                    <?php echo $difficulte; ?>
                                </span>
                            </div>

                            <div class="jeu-meta">
                                <span class="meta-item"><strong>Type:</strong> <?php echo $type; ?></span>
                                <span class="meta-item"><strong>Thème:</strong> <?php echo $theme; ?></span>
                                <span class="meta-item"><strong>Catégorie:</strong> <?php echo $categorie_nom; ?></span>
                            </div>

                            <?php if (!empty($description)): ?>
                                <div class="jeu-description">
                                    <p><?php echo $description; ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="jeu-actions">
                                <a href="jeuxfront.php?action=read&id=<?php echo $id; ?>" class="btn btn-sm">👁️ Voir</a>
                                <a href="jeuxfront.php?action=update&id=<?php echo $id; ?>" class="btn btn-sm btn-secondary">✏️ Modifier</a>
                                <a href="supprimerjeux.php?id=<?php echo $id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?');">🗑️ Supprimer</a>
                            </div>

                            <div class="jeu-footer">
                                <small>ID: <?php echo $id ?: '-'; ?> • Créé le: <?php echo $date_affiche; ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="no-data">
                    <div class="no-data-icon">🎮</div>
                    <h3>Aucun jeu trouvé</h3>
                    <p>Les jeux seront affichés ici dès qu’ils seront ajoutés.</p>

                    <!-- ❌ BOUTON AJOUTÉ RETIRÉ ICI -->
                </div>
            <?php endif; ?>
        </main>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const jeuxGrid = document.getElementById('jeuxGrid');
    const jeuCards = jeuxGrid ? jeuxGrid.getElementsByClassName('jeu-card') : [];

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            for (let card of jeuCards) {
                const titre = card.getAttribute('data-titre') || '';
                const type = card.getAttribute('data-type') || '';
                const theme = card.getAttribute('data-theme') || '';
                if (titre.includes(searchTerm) || type.includes(searchTerm) || theme.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            }
        });
    }
});
</script>

</body>
</html>
