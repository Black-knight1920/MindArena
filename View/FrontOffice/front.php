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
                <div class="header-actions">
                    <a href="ajouterjeux.php?action=create" class="btn btn-primary">➕ Ajouter un jeu</a>
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
                        // Valeurs sécurisées et valeurs par défaut
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
                    <p>Commencez par ajouter votre premier jeu à la collection.</p>
                    <a href="jeuxfront.php?action=create" class="btn btn-primary">Ajouter un jeu</a>
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

    <style>
        /* --- Ton CSS d'origine (inchangé) --- */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-header h2 { color: #2c3e50; margin: 0; }
        .header-actions { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .search-box input { padding: 0.5rem 1rem; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9rem; min-width: 250px; }
        .stats-bar { display: flex; gap: 2rem; margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #3498db; }
        .stat-item { text-align: center; }
        .stat-number { display: block; font-size: 1.5rem; font-weight: bold; color: #2c3e50; }
        .stat-label { font-size: 0.9rem; color: #7f8c8d; }
        .jeux-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }
        .jeu-card { background: white; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border: 1px solid #e9ecef; transition: transform 0.3s, box-shadow 0.3s; }
        .jeu-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
        .jeu-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
        .jeu-header h3 { margin: 0; color: #2c3e50; font-size: 1.2rem; flex: 1; margin-right: 1rem; }
        .difficulty-badge { padding: 0.3rem 0.8rem; border-radius: 12px; font-size: 0.8rem; font-weight: 600; white-space: nowrap; }
        .difficulty-facile { background: #d4edda; color: #155724; }
        .difficulty-moyen { background: #fff3cd; color: #856404; }
        .difficulty-difficile { background: #f8d7da; color: #721c24; }
        .difficulty-expert { background: #d1ecf1; color: #0c5460; }
        .jeu-meta { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem; }
        .meta-item { font-size: 0.9rem; color: #5d6d7e; }
        .meta-item strong { color: #2c3e50; }
        .jeu-description { margin-bottom: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 4px; border-left: 3px solid #3498db; }
        .jeu-description p { margin: 0; color: #5d6d7e; line-height: 1.5; font-size: 0.9rem; }
        .jeu-actions { display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.8rem; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; transition: background 0.3s; }
        .btn { background: #3498db; color: white; }
        .btn:hover { background: #2980b9; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn-secondary:hover { background: #7f8c8d; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-primary { background: #2ecc71; color: white; }
        .btn-primary:hover { background: #27ae60; }
        .jeu-footer { border-top: 1px solid #ecf0f1; padding-top: 0.5rem; text-align: center; }
        .jeu-footer small { color: #7f8c8d; font-size: 0.8rem; }
        .no-data { text-align: center; padding: 3rem; color: #6c757d; }
        .no-data-icon { font-size: 4rem; margin-bottom: 1rem; }
        .no-data h3 { margin-bottom: 1rem; color: #2c3e50; }
        .no-data p { margin-bottom: 2rem; font-size: 1.1rem; }
        @media (max-width: 768px) {
            .page-header { flex-direction: column; align-items: stretch; }
            .header-actions { justify-content: space-between; }
            .search-box input { min-width: auto; width: 100%; }
            .jeux-grid { grid-template-columns: 1fr; }
            .jeu-header { flex-direction: column; align-items: flex-start; }
            .jeu-header h3 { margin-right: 0; margin-bottom: 0.5rem; }
            .jeu-actions { justify-content: center; }
            .stats-bar { flex-direction: column; gap: 1rem; }
        }
    </style>
</body>
</html>
