<?php
// Vérifier que les variables existent et sont des tableaux
$jeux_list = isset($jeux_list) && is_array($jeux_list) ? $jeux_list : [];
$categories_list = isset($categories_list) && is_array($categories_list) ? $categories_list : [];

// Calculer les statistiques
$total_jeux = count($jeux_list);
$total_categories = count($categories_list);

// Compter les jeux avec URL
$jeux_avec_url = count(array_filter($jeux_list, function($jeu) { 
    return !empty($jeu['lien_url']); 
}));

// Calcul du prix moyen avec vérification
$prix_total = 0;
$prix_count = 0;
foreach ($jeux_list as $jeu) {
    if (isset($jeu['prix']) && is_numeric($jeu['prix'])) {
        $prix_total += $jeu['prix'];
        $prix_count++;
    }
}
$prix_moyen = $prix_count > 0 ? round($prix_total / $prix_count, 2) : 0;

// Calculer les statistiques par catégorie
$categories_count = [];
foreach ($jeux_list as $jeu) {
    $cat_nom = $jeu['categorie_nom'] ?? 'Non catégorisé';
    if (!isset($categories_count[$cat_nom])) {
        $categories_count[$cat_nom] = 0;
    }
    $categories_count[$cat_nom]++;
}

// Trouver le maximum pour l'échelle du diagramme
$max_count = !empty($categories_count) ? max($categories_count) : 0;
?>

<div class="row">
    <!-- Statistiques -->
    <div class="col-lg-3 col-sm-6">
        <div class="card custom-card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-joystick text-white fs-6"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0 fw-bolder text-white"><?php echo $total_jeux; ?></h4>
                        <span class="text-white-50">Jeux Total</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-sm-6">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success-subtle p-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-category text-success fs-6"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0 fw-bolder"><?php echo $total_categories; ?></h4>
                        <span class="text-muted">Catégories</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-sm-6">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info-subtle p-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-link text-info fs-6"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0 fw-bolder"><?php echo $jeux_avec_url; ?></h4>
                        <span class="text-muted">Avec URL</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-sm-6">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning-subtle p-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-trending-up text-warning fs-6"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0 fw-bolder"><?php echo $prix_moyen; ?>€</h4>
                        <span class="text-muted">Prix Moyen</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Colonne principale avec le diagramme -->
    <div class="col-lg-8">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title">Statistiques par Catégorie</h4>
                        <p class="card-subtitle">Répartition des jeux par catégorie</p>
                    </div>
                    <?php if (!empty($categories_count)): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ti ti-sort-descending"></i> Trier
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="sortBars('name')">Par nom</a></li>
                            <li><a class="dropdown-item" href="#" onclick="sortBars('value')">Par nombre</a></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($categories_count)): ?>
                <div id="barChartContainer">
                    <?php 
                    // Trier par nombre décroissant pour l'affichage initial
                    arsort($categories_count);
                    foreach ($categories_count as $cat_nom => $count): 
                        $percentage = $max_count > 0 ? ($count / $max_count * 100) : 0;
                        // Couleur basée sur l'index
                        $color_index = array_search($cat_nom, array_keys($categories_count));
                        $colors = ['#667eea', '#764ba2', '#f56565', '#ed8936', '#48bb78', '#4299e1'];
                        $bar_color = $colors[$color_index % count($colors)];
                    ?>
                    <div class="bar-item mb-3" data-value="<?= $count ?>" data-name="<?= htmlspecialchars($cat_nom) ?>">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="categorie-name fw-medium"><?= htmlspecialchars($cat_nom) ?></span>
                            <span class="badge" style="background: <?= $bar_color ?>; color: white;">
                                <?= $count ?> jeu<?= $count > 1 ? 'x' : '' ?>
                            </span>
                        </div>
                        <div class="bar-background">
                            <div class="bar-fill" style="width: <?= $percentage ?>%; background: <?= $bar_color ?>;"></div>
                            <div class="bar-label d-flex justify-content-between px-2">
                                <small>0</small>
                                <small><?= $max_count ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="ti ti-chart-bar fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Aucune donnée disponible pour les catégories</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Jeux récents -->
        <div class="card custom-card mt-4">
            <div class="card-body">
                <div class="d-md-flex align-items-center">
                    <div>
                        <h4 class="card-title">Derniers Jeux Ajoutés</h4>
                        <p class="card-subtitle">Les 5 jeux les plus récents</p>
                    </div>
                    <div class="ms-auto">
                        <a href="admin.php?section=jeux" class="btn btn-primary">Voir tous les jeux</a>
                    </div>
                </div>
                
                <?php if ($total_jeux > 0): ?>
                <div class="table-responsive mt-4">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Jeu</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recent_jeux = array_slice($jeux_list, 0, 5);
                            foreach ($recent_jeux as $jeu): 
                                // Gestion sécurisée des données
                                $titre = htmlspecialchars($jeu['titre'] ?? 'Titre inconnu');
                                $type = htmlspecialchars($jeu['type'] ?? 'Non spécifié');
                                $categorie_nom = htmlspecialchars($jeu['categorie_nom'] ?? 'Non catégorisé');
                                $prix = isset($jeu['prix']) ? number_format($jeu['prix'], 2, ',', ' ') . ' €' : '0,00 €';
                                
                                // Gestion sécurisée de la date
                                $date_creation = $jeu['date_creation'] ?? null;
                                if ($date_creation && strtotime($date_creation)) {
                                    $date_affichee = date('d/m/Y', strtotime($date_creation));
                                } else {
                                    $date_affichee = '-';
                                }
                                
                                // Gestion de l'image
                                $image = $jeu['image'] ?? null;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($image)): ?>
                                            <img src="../uploads/<?php echo $image; ?>" class="jeu-image me-3" alt="<?php echo $titre; ?>">
                                        <?php else: ?>
                                            <div class="jeu-image bg-light d-flex align-items-center justify-content-center me-3">
                                                <i class="ti ti-joystick text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo $titre; ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo $type; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $categorie_nom; ?></span>
                                </td>
                                <td><?php echo $prix; ?></td>
                                <td><?php echo $date_affichee; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="ti ti-joystick fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Aucun jeu disponible</p>
                    <a href="ajouterjeux.php" class="btn btn-primary">Ajouter un jeu</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar avec détails -->
    <div class="col-lg-4">
        <div class="card custom-card">
            <div class="card-body">
                <h4 class="card-title">Actions Rapides</h4>
                <div class="d-grid gap-2 mt-3">
                    <a href="ajouterjeux.php" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>Ajouter un Jeu
                    </a>
                    <a href="ajoutercategorie.php" class="btn btn-outline-primary">
                        <i class="ti ti-category me-2"></i>Nouvelle Catégorie
                    </a>
                    <a href="../FrontOffice/front.php" target="_blank" class="btn btn-outline-secondary">
                        <i class="ti ti-eye me-2"></i>Voir le Front Office
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Rapport PDF version imprimable -->
        <div class="card custom-card mt-4">
            <div class="card-body">
                <h4 class="card-title">📄 Rapport PDF version imprimable</h4>
                <p class="text-muted mb-3">Générez un rapport complet pour impression</p>
                
                <div class="text-center p-3" style="background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 10px; color: white;">
                    <i class="ti ti-file-type-pdf fs-1 mb-3"></i>
                    <h5>Rapport Complet</h5>
                    <p class="small mb-3" style="opacity: 0.9;">Statistiques, listes et tableaux formatés</p>
                    <a href="generer_pdf.php" target="_blank" class="btn btn-light btn-sm">
                        <i class="ti ti-eye me-1"></i>Voir le rapport
                    </a>
                    <br>
                    <small class="mt-2 d-block" style="opacity: 0.8;">
                        <i class="ti ti-printer me-1"></i>Utilisez Ctrl+P pour imprimer
                    </small>
                </div>
                
                <div class="mt-3">
                    <p class="small text-muted">
                        <i class="ti ti-info-circle me-1"></i>
                        Le rapport inclut:
                    </p>
                    <ul class="small text-muted mb-0">
                        <li>Statistiques globales</li>
                        <li>Liste complète des jeux</li>
                        <li>Répartition par catégorie</li>
                        <li>Top 5 des catégories</li>
                        <li>Informations de contact</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Détails des catégories -->
        <div class="card custom-card mt-4">
            <div class="card-body">
                <h4 class="card-title mb-4">Détails des Catégories</h4>
                
                <?php if (!empty($categories_count)): ?>
                <div class="categories-details">
                    <?php 
                    $sorted_categories = $categories_count;
                    arsort($sorted_categories);
                    foreach ($sorted_categories as $cat_nom => $count): 
                    ?>
                    <div class="categorie-detail mb-3 p-3 rounded" style="background: rgba(var(--primary-rgb, 102, 126, 234), 0.05);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="text-primary"><?= htmlspecialchars($cat_nom) ?></strong>
                            <span class="badge bg-primary">
                                <?= $count ?> jeu<?= $count > 1 ? 'x' : '' ?>
                            </span>
                        </div>
                        <?php if ($count > 0): ?>
                            <?php 
                            // Trouver des exemples de jeux dans cette catégorie
                            $jeux_categorie = array_filter($jeux_list, function($jeu) use ($cat_nom) {
                                return ($jeu['categorie_nom'] ?? '') == $cat_nom;
                            });
                            $exemples = array_slice($jeux_categorie, 0, 2);
                            ?>
                            <div class="text-muted small">
                                <i class="ti ti-point me-1"></i>
                                <?php if (!empty($exemples)): ?>
                                    Ex: 
                                    <?php foreach ($exemples as $index => $exemple): ?>
                                        <?php if ($index > 0): ?>, <?php endif; ?>
                                        <?= htmlspecialchars(substr($exemple['titre'], 0, 15)) . (strlen($exemple['titre']) > 15 ? '...' : '') ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    Aucun jeu détaillé
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="ti ti-category fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Aucune catégorie disponible</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles pour le diagramme en barres */
.bar-item {
    cursor: pointer;
    transition: all 0.3s ease;
}

.bar-item:hover {
    transform: translateX(5px);
}

.bar-background {
    height: 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    overflow: hidden;
    position: relative;
}

body.light .bar-background {
    background: rgba(0, 0, 0, 0.05);
}

.bar-fill {
    height: 100%;
    border-radius: 15px;
    position: relative;
    transition: width 1s ease-out;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.bar-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, rgba(255,255,255,0.1), rgba(255,255,255,0.4), rgba(255,255,255,0.1));
    animation: shimmer 2s infinite;
}

.bar-label {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    font-size: 10px;
    color: rgba(255,255,255,0.5);
    pointer-events: none;
}

body.light .bar-label {
    color: rgba(0,0,0,0.5);
}

.categorie-name {
    font-size: 14px;
    font-weight: 500;
    color: var(--text);
}

.categorie-count {
    font-size: 12px;
    font-weight: 600;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.categorie-detail {
    transition: all 0.3s ease;
    background: rgba(139, 92, 246, 0.05) !important;
    border: 1px solid rgba(139, 92, 246, 0.1);
}

.categorie-detail:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
    background: rgba(139, 92, 246, 0.1) !important;
}

body.light .categorie-detail {
    background: rgba(139, 92, 246, 0.03) !important;
    border-color: rgba(139, 92, 246, 0.2);
}

/* Style pour les images de jeux */
.jeu-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--border-subtle);
}

.bg-light {
    background-color: rgba(255, 255, 255, 0.05) !important;
}

body.light .bg-light {
    background-color: rgba(0, 0, 0, 0.05) !important;
}

.text-muted {
    color: var(--text-muted) !important;
}

/* Corrections pour les badges */
.badge.bg-info {
    background-color: rgba(59, 130, 246, 0.15) !important;
    color: #3b82f6 !important;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.badge.bg-primary {
    background-color: rgba(139, 92, 246, 0.15) !important;
    color: var(--primary) !important;
    border: 1px solid rgba(139, 92, 246, 0.3);
}

/* Corrections pour les classes Bootstrap subtiles */
.bg-success-subtle {
    background-color: rgba(34, 197, 94, 0.1) !important;
}
.bg-info-subtle {
    background-color: rgba(59, 130, 246, 0.1) !important;
}
.bg-warning-subtle {
    background-color: rgba(245, 158, 11, 0.1) !important;
}
</style>

<script>
// Fonction pour trier les barres
function sortBars(type) {
    const container = document.getElementById('barChartContainer');
    const bars = Array.from(container.querySelectorAll('.bar-item'));
    
    bars.sort((a, b) => {
        if (type === 'name') {
            return a.dataset.name.localeCompare(b.dataset.name);
        } else if (type === 'value') {
            return parseInt(b.dataset.value) - parseInt(a.dataset.value);
        }
        return 0;
    });
    
    // Réorganiser les barres dans le DOM
    bars.forEach(bar => container.appendChild(bar));
    
    // Mettre à jour les couleurs après réorganisation
    updateBarColors();
}

// Mettre à jour les couleurs des barres après tri
function updateBarColors() {
    const bars = document.querySelectorAll('.bar-item');
    const colors = ['#667eea', '#764ba2', '#f56565', '#ed8936', '#48bb78', '#4299e1'];
    
    bars.forEach((bar, index) => {
        const barFill = bar.querySelector('.bar-fill');
        const badge = bar.querySelector('.badge');
        const color = colors[index % colors.length];
        
        if (barFill) barFill.style.background = color;
        if (badge) {
            badge.style.background = color;
            badge.style.color = 'white';
        }
    });
}

// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
    const bars = document.querySelectorAll('.bar-fill');
    bars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});
</script>