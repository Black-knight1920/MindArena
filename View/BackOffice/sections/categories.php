<?php
// Vérifier que les variables existent
$jeux_list = isset($jeux_list) && is_array($jeux_list) ? $jeux_list : [];
$categories_list = isset($categories_list) && is_array($categories_list) ? $categories_list : [];
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-md-flex align-items-center">
                    <div>
                        <h4 class="card-title">Gestion des Catégories</h4>
                        <p class="card-subtitle">Liste des catégories de jeux</p>
                    </div>
                    <div class="ms-auto">
                        <a href="ajoutercategorie.php" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Nouvelle catégorie
                        </a>
                    </div>
                </div>

                <?php if (!empty($categories_list)): ?>
                <div class="table-responsive mt-4">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Nombre de jeux</th>
                                <th>Date création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $categories_with_count = [];
                            foreach ($categories_list as $categorie) {
                                $count = 0;
                                foreach ($jeux_list as $jeu) {
                                    if (isset($jeu['categorie_id']) && $jeu['categorie_id'] == $categorie['id']) {
                                        $count++;
                                    }
                                }
                                $categories_with_count[] = [
                                    'categorie' => $categorie,
                                    'jeux_count' => $count
                                ];
                            }
                            
                            foreach ($categories_with_count as $item): 
                                $categorie = $item['categorie'];
                                
                                // Gestion sécurisée des données
                                $id = $categorie['id'] ?? '';
                                $nom = htmlspecialchars($categorie['nom'] ?? 'Nom inconnu');
                                $description = $categorie['description'] ?? '';
                                
                                // Gestion sécurisée de la date
                                $date_creation = $categorie['date_creation'] ?? null;
                                if ($date_creation && strtotime($date_creation)) {
                                    $date_affichee = date('d/m/Y', strtotime($date_creation));
                                } else {
                                    $date_affichee = '-';
                                }
                            ?>
                            <tr>
                                <td><?php echo $id; ?></td>
                                <td>
                                    <strong><?php echo $nom; ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $item['jeux_count']; ?> jeux</span>
                                </td>
                                <td><?php echo $date_affichee; ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="modifiercategorie.php?id=<?php echo $id; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="supprimercategorie.php?id=<?php echo $id; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Supprimer"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="ti ti-category fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Aucune catégorie trouvée</h5>
                    <p class="text-muted">Commencez par créer votre première catégorie.</p>
                    <a href="ajoutercategorie.php" class="btn btn-primary">Créer une catégorie</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>