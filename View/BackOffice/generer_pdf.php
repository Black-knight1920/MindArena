<?php
require_once __DIR__ . '/../../Controller/jeuxback.php';
require_once __DIR__ . '/../../Controller/categorieback.php';

$jeuxController = new JeuxBackController();
$categorieController = new CategorieBackController();

$jeux_list = $jeuxController->getAllJeux();
$categories_list = $categorieController->getAllCategories();

// Récupérer les statistiques par catégorie
$categoriesStats = [];
if (method_exists($jeuxController, 'getCategoriesStats')) {
    $categoriesStats = $jeuxController->getCategoriesStats();
} else {
    // Calcul manuel si la méthode n'existe pas
    $categoriesCount = [];
    foreach ($jeux_list as $jeu) {
        $cat_nom = $jeu['categorie_nom'] ?? 'Non catégorisé';
        if (!isset($categoriesCount[$cat_nom])) {
            $categoriesCount[$cat_nom] = 0;
        }
        $categoriesCount[$cat_nom]++;
    }
    
    foreach ($categoriesCount as $nom => $count) {
        $categoriesStats[] = [
            'categorie_nom' => $nom,
            'nombre_jeux' => $count
        ];
    }
}

// Calculs des statistiques
$total_jeux = count($jeux_list);
$total_categories = count($categories_list);
$jeux_avec_url = count(array_filter($jeux_list, function($jeu) { 
    return !empty($jeu['lien_url']); 
}));

// Calcul du prix moyen
$prix_total = 0;
$prix_count = 0;
foreach ($jeux_list as $jeu) {
    if (isset($jeu['prix']) && is_numeric($jeu['prix'])) {
        $prix_total += $jeu['prix'];
        $prix_count++;
    }
}
$prix_moyen = $prix_count > 0 ? number_format($prix_total / $prix_count, 2) : 0;

// Génération du contenu HTML
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport GameZone</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #4CAF50; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #2c3e50; margin: 0; }
        .header p { color: #7f8c8d; }
        .section { margin-bottom: 30px; }
        .section-title { background: #4CAF50; color: white; padding: 10px; border-radius: 5px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; padding: 15px; text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #4CAF50; }
        .stat-label { color: #666; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #34495e; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .badge { background: #3498db; color: white; padding: 3px 8px; border-radius: 10px; font-size: 12px; }
        .footer { text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; color: #7f8c8d; font-size: 12px; }
        .print-btn { background: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 20px 0; }
        .print-btn:hover { background: #45a049; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 RAPPORT GAMEZONE</h1>
        <p>Date de génération: ' . date('d/m/Y H:i') . '</p>
        <button class="print-btn" onclick="window.print()">🖨️ Imprimer ce rapport</button>
    </div>

    <div class="section">
        <div class="section-title">📈 STATISTIQUES GLOBALES</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">' . $total_jeux . '</div>
                <div class="stat-label">Jeux total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">' . $total_categories . '</div>
                <div class="stat-label">Catégories</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">' . $jeux_avec_url . '</div>
                <div class="stat-label">Jeux avec URL</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">' . $prix_moyen . ' €</div>
                <div class="stat-label">Prix moyen</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">📊 STATISTIQUES PAR CATÉGORIE</div>
        <table>
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Nombre de jeux</th>
                    <th>Pourcentage</th>
                </tr>
            </thead>
            <tbody>';

// Calcul pourcentage
$total_all_jeux = array_sum(array_column($categoriesStats, 'nombre_jeux'));
foreach ($categoriesStats as $stat) {
    $pourcentage = $total_all_jeux > 0 ? round(($stat['nombre_jeux'] / $total_all_jeux) * 100, 1) : 0;
    $html .= '<tr>
                <td>' . htmlspecialchars($stat['categorie_nom']) . '</td>
                <td>' . $stat['nombre_jeux'] . '</td>
                <td>' . $pourcentage . '%</td>
              </tr>';
}

$html .= '</tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">🎮 DERNIERS JEUX AJOUTÉS</div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>URL</th>
                </tr>
            </thead>
            <tbody>';

// Afficher les 10 derniers jeux
$recent_jeux = array_slice($jeux_list, 0, 10);
foreach ($recent_jeux as $jeu) {
    $html .= '<tr>
                <td>' . ($jeu['id'] ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($jeu['titre'] ?? '') . '</td>
                <td><span class="badge">' . htmlspecialchars($jeu['categorie_nom'] ?? 'Non catégorisé') . '</span></td>
                <td>' . (isset($jeu['prix']) ? number_format($jeu['prix'], 2) . ' €' : '0.00 €') . '</td>
                <td>' . (!empty($jeu['lien_url']) ? '✅' : '❌') . '</td>
              </tr>';
}

$html .= '</tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">🏆 TOP DES CATÉGORIES</div>
        <table>
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Catégorie</th>
                    <th>Nombre de jeux</th>
                </tr>
            </thead>
            <tbody>';

// Trier par nombre de jeux décroissant
usort($categoriesStats, function($a, $b) {
    return $b['nombre_jeux'] - $a['nombre_jeux'];
});

$positions = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];
$top5 = array_slice($categoriesStats, 0, 5);

foreach ($top5 as $index => $stat) {
    $html .= '<tr>
                <td>' . ($positions[$index] ?? ($index + 1)) . '</td>
                <td>' . htmlspecialchars($stat['categorie_nom']) . '</td>
                <td>' . $stat['nombre_jeux'] . ' jeux</td>
              </tr>';
}

$html .= '</tbody>
        </table>
    </div>

    <div class="footer">
        <p>📄 Rapport généré automatiquement par le système GameZone</p>
        <p>📍 Gestion de jeux vidéo - Version 1.0</p>
        <p>© ' . date('Y') . ' GameZone - Tous droits réservés</p>
    </div>

    <script>
        // Auto-impression optionnelle
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>
</html>';

// Envoyer l'HTML au navigateur
header('Content-Type: text/html; charset=utf-8');
echo $html;
exit;