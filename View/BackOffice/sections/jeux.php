<?php
// Vérifier que les variables existent
$jeux_list = isset($jeux_list) && is_array($jeux_list) ? $jeux_list : [];
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="d-md-flex align-items-center">
                    <div>
                        <h4 class="card-title">Gestion des Jeux</h4>
                        <p class="card-subtitle">Liste complète des jeux</p>
                    </div>
                    <div class="ms-auto">
                        <a href="ajouterjeux.php" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Ajouter un jeu
                        </a>
                    </div>
                </div>

                <?php if (!empty($jeux_list)): ?>
                <div class="table-responsive mt-4">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jeux_list as $jeu): 
                                // Gestion sécurisée des données
                                $id = $jeu['id'] ?? '';
                                $titre = htmlspecialchars($jeu['titre'] ?? 'Titre inconnu');
                                $type = htmlspecialchars($jeu['type'] ?? '-');
                                $description = $jeu['description'] ?? '';
                                $categorie_nom = htmlspecialchars($jeu['categorie_nom'] ?? 'Non catégorisé');
                                $prix = isset($jeu['prix']) ? number_format($jeu['prix'], 2, ',', ' ') . ' €' : '0,00 €';
                                $image = $jeu['image'] ?? null;
                                
                                // Gestion sécurisée de la date
                                $date_creation = $jeu['date_creation'] ?? null;
                                if ($date_creation && strtotime($date_creation)) {
                                    $date_affichee = date('d/m/Y', strtotime($date_creation));
                                } else {
                                    $date_affichee = '-';
                                }
                            ?>
                            <tr>
                                <td><?php echo $id; ?></td>
                                <td>
                                    <?php if (!empty($image)): ?>
                                        <img src="../uploads/<?php echo $image; ?>" class="jeu-image" alt="<?php echo $titre; ?>">
                                    <?php else: ?>
                                        <div class="jeu-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="ti ti-joystick text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo $titre; ?></strong>
                                    <br>
                                    <small class="text-muted">Type: <?php echo $type; ?></small>
                                </td>
                                <td>
                                    <?php 
                                    echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $categorie_nom; ?></span>
                                </td>
                                <td>
                                    <strong><?php echo $prix; ?></strong>
                                </td>
                                <td><?php echo $date_affichee; ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="modifierjeux.php?id=<?php echo $id; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="supprimerjeux.php?id=<?php echo $id; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Supprimer"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce jeu ?')">
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
                    <i class="ti ti-joystick fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Aucun jeu trouvé</h5>
                    <p class="text-muted">Commencez par ajouter votre premier jeu.</p>
                    <a href="ajouterjeux.php" class="btn btn-primary">Ajouter un jeu</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>