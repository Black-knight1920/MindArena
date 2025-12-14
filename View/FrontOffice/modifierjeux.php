<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Jeu - BackOffice</title>
    <link rel="stylesheet" href="back.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>BackOffice - Modifier un Jeu</h1>
            <nav class="admin-nav">
                <a href="jeuxback.php">Jeux</a>
                <a href="categorieback.php">Catégories</a>
                <a href="../FrontOffice/front.php">Site Public</a>
                <a href="logout.php">Déconnexion</a>
            </nav>
        </header>

        <div class="admin-main">
            <aside class="admin-sidebar">
                <h3>Navigation Jeux</h3>
                <ul>
                    <li><a href="jeuxback.php">Liste des Jeux</a></li>
                    <li><a href="jeuxback.php?action=create">Nouveau Jeu</a></li>
                    <li><a href="categorieback.php">Gestion des Catégories</a></li>
                </ul>

                <div class="sidebar-info">
                    <h4>Informations du jeu</h4>
                    <div class="info-item">
                        <strong>ID:</strong> <?php echo $jeu->id; ?>
                    </div>
                    <div class="info-item">
                        <strong>Statut:</strong> <span class="status-active">Actif</span>
                    </div>
                    <div class="info-item">
                        <strong>Dernière modification:</strong> <?php echo date('d/m/Y'); ?>
                    </div>
                </div>
            </aside>

            <main class="admin-content">
                <div class="admin-header-actions">
                    <h2>Modifier le jeu</h2>
                    <div class="action-buttons">
                        <a href="jeuxback.php" class="btn">← Retour à la liste</a>
                        <a href="jeuxback.php?action=delete&id=<?php echo $jeu->id; ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce jeu ?')">
                           Supprimer
                        </a>
                    </div>
                </div>

                <?php if($message): ?>
                    <div class="admin-message error"><?php echo $message; ?></div>
                <?php endif; ?>

                <form method="POST" class="admin-form" onsubmit="return validateJeuForm()">
                    <input type="hidden" name="id" value="<?php echo $jeu->id; ?>">
                    
                    <div class="form-section">
                        <h3>Informations générales</h3>
                        
                        <div class="form-group">
                            <label for="titre">Titre du jeu *</label>
                            <input type="text" id="titre" name="titre" 
                                   value="<?php echo htmlspecialchars($jeu->titre); ?>" 
                                   required placeholder="Ex: Monopoly, Échecs, The Legend of Zelda..."
                                   maxlength="200">
                            <span class="error-message" id="titre-error"></span>
                            <div class="form-help">Le titre doit contenir entre 2 et 200 caractères</div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4" 
                                      placeholder="Décrivez le jeu, ses règles, son univers..."
                                      maxlength="1000"><?php echo htmlspecialchars($jeu->description); ?></textarea>
                            <span class="error-message" id="description-error"></span>
                            <div class="form-help">Maximum 1000 caractères (reste: <span id="char-count"><?php echo 1000 - strlen($jeu->description); ?></span>)</div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Caractéristiques du jeu</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="type">Type de jeu *</label>
                                <select id="type" name="type" required>
                                    <option value="">Sélectionnez un type...</option>
                                    <option value="Stratégie" <?php echo $jeu->type == 'Stratégie' ? 'selected' : ''; ?>>Stratégie</option>
                                    <option value="Aventure" <?php echo $jeu->type == 'Aventure' ? 'selected' : ''; ?>>Aventure</option>
                                    <option value="Action" <?php echo $jeu->type == 'Action' ? 'selected' : ''; ?>>Action</option>
                                    <option value="RPG" <?php echo $jeu->type == 'RPG' ? 'selected' : ''; ?>>RPG</option>
                                    <option value="Réflexion" <?php echo $jeu->type == 'Réflexion' ? 'selected' : ''; ?>>Réflexion</option>
                                    <option value="Sport" <?php echo $jeu->type == 'Sport' ? 'selected' : ''; ?>>Sport</option>
                                    <option value="Course" <?php echo $jeu->type == 'Course' ? 'selected' : ''; ?>>Course</option>
                                    <option value="Combat" <?php echo $jeu->type == 'Combat' ? 'selected' : ''; ?>>Combat</option>
                                    <option value="Simulation" <?php echo $jeu->type == 'Simulation' ? 'selected' : ''; ?>>Simulation</option>
                                    <option value="Autre" <?php echo $jeu->type == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                                </select>
                                <span class="error-message" id="type-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="difficulte">Niveau de difficulté *</label>
                                <select id="difficulte" name="difficulte" required>
                                    <option value="">Sélectionnez...</option>
                                    <option value="Facile" <?php echo $jeu->difficulte == 'Facile' ? 'selected' : ''; ?>>Facile</option>
                                    <option value="Moyen" <?php echo $jeu->difficulte == 'Moyen' ? 'selected' : ''; ?>>Moyen</option>
                                    <option value="Difficile" <?php echo $jeu->difficulte == 'Difficile' ? 'selected' : ''; ?>>Difficile</option>
                                    <option value="Expert" <?php echo $jeu->difficulte == 'Expert' ? 'selected' : ''; ?>>Expert</option>
                                </select>
                                <span class="error-message" id="difficulte-error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="theme">Thème principal *</label>
                            <input type="text" id="theme" name="theme" 
                                   value="<?php echo htmlspecialchars($jeu->theme); ?>" 
                                   required placeholder="Ex: Fantasy, Médiéval, Science-fiction, Moderne..."
                                   maxlength="100">
                            <span class="error-message" id="theme-error"></span>
                            <div class="form-help">Exemples: Fantasy, Médiéval, Science-fiction, Horreur, Historique</div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Classification</h3>
                        
                        <div class="form-group">
                            <label for="categorie_id">Catégorie *</label>
                            <select id="categorie_id" name="categorie_id" required>
                                <option value="">Sélectionnez une catégorie...</option>
                                <?php while ($row = $categories->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo $row['id']; ?>" 
                                        <?php echo $jeu->categorie_id == $row['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['nom']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <span class="error-message" id="categorie_id-error"></span>
                            <div class="form-help">
                                <a href="categorieback.php?action=create" target="_blank">Créer une nouvelle catégorie</a>
                            </div>
                        </div>
                    </div>

                    <div class="form-preview">
                        <h4>Aperçu du jeu :</h4>
                        <div class="preview-card">
                            <div class="preview-header">
                                <strong id="preview-titre"><?php echo htmlspecialchars($jeu->titre); ?></strong>
                                <span class="badge badge-<?php echo strtolower($jeu->difficulte); ?>" id="preview-difficulte">
                                    <?php echo $jeu->difficulte; ?>
                                </span>
                            </div>
                            <div class="preview-meta">
                                <span id="preview-type"><?php echo htmlspecialchars($jeu->type); ?></span> • 
                                <span id="preview-theme"><?php echo htmlspecialchars($jeu->theme); ?></span>
                            </div>
                            <p id="preview-description"><?php echo $jeu->description ? htmlspecialchars($jeu->description) : 'La description apparaîtra ici...'; ?></p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <span class="btn-icon">✓</span>
                            Enregistrer les modifications
                        </button>
                        <a href="jeuxback.php" class="btn btn-secondary">
                            <span class="btn-icon">×</span>
                            Annuler
                        </a>
                        <button type="button" class="btn btn-outline" onclick="previewGame()">
                            <span class="btn-icon">👁️</span>
                            Prévisualiser
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        function validateJeuForm() {
            let isValid = true;
            
            // Validation du titre
            const titre = document.getElementById('titre');
            const titreError = document.getElementById('titre-error');
            if (titre.value.trim() === '') {
                titreError.textContent = 'Le titre du jeu est obligatoire';
                isValid = false;
            } else if (titre.value.trim().length < 2) {
                titreError.textContent = 'Le titre doit contenir au moins 2 caractères';
                isValid = false;
            } else if (titre.value.trim().length > 200) {
                titreError.textContent = 'Le titre ne doit pas dépasser 200 caractères';
                isValid = false;
            } else {
                titreError.textContent = '';
            }
            
            // Validation du type
            const type = document.getElementById('type');
            const typeError = document.getElementById('type-error');
            if (type.value === '') {
                typeError.textContent = 'Le type de jeu est obligatoire';
                isValid = false;
            } else {
                typeError.textContent = '';
            }
            
            // Validation de la difficulté
            const difficulte = document.getElementById('difficulte');
            const difficulteError = document.getElementById('difficulte-error');
            if (difficulte.value === '') {
                difficulteError.textContent = 'La difficulté est obligatoire';
                isValid = false;
            } else {
                difficulteError.textContent = '';
            }
            
            // Validation du thème
            const theme = document.getElementById('theme');
            const themeError = document.getElementById('theme-error');
            if (theme.value.trim() === '') {
                themeError.textContent = 'Le thème est obligatoire';
                isValid = false;
            } else if (theme.value.trim().length < 2) {
                themeError.textContent = 'Le thème doit contenir au moins 2 caractères';
                isValid = false;
            } else {
                themeError.textContent = '';
            }
            
            // Validation de la catégorie
            const categorie = document.getElementById('categorie_id');
            const categorieError = document.getElementById('categorie_id-error');
            if (categorie.value === '') {
                categorieError.textContent = 'La catégorie est obligatoire';
                isValid = false;
            } else {
                categorieError.textContent = '';
            }
            
            return isValid;
        }

        // Mise à jour en temps réel de l'aperçu
        document.addEventListener('DOMContentLoaded', function() {
            const titre = document.getElementById('titre');
            const description = document.getElementById('description');
            const type = document.getElementById('type');
            const difficulte = document.getElementById('difficulte');
            const theme = document.getElementById('theme');
            const charCount = document.getElementById('char-count');
            
            // Initialisation du compteur
            const initialLength = description.value.length;
            charCount.textContent = 1000 - initialLength;
            
            // Écouteurs d'événements
            if (titre) {
                titre.addEventListener('input', function() {
                    document.getElementById('preview-titre').textContent = 
                        this.value || 'Titre du jeu';
                });
            }
            
            if (description) {
                description.addEventListener('input', function() {
                    const remaining = 1000 - this.value.length;
                    charCount.textContent = remaining;
                    document.getElementById('preview-description').textContent = 
                        this.value || 'La description apparaîtra ici...';
                    
                    // Changer la couleur du compteur
                    if (remaining < 50) {
                        charCount.style.color = '#e74c3c';
                    } else if (remaining < 100) {
                        charCount.style.color = '#f39c12';
                    } else {
                        charCount.style.color = '#27ae60';
                    }
                });
            }
            
            if (type) {
                type.addEventListener('change', function() {
                    document.getElementById('preview-type').textContent = this.value;
                });
            }
            
            if (difficulte) {
                difficulte.addEventListener('change', function() {
                    const badge = document.getElementById('preview-difficulte');
                    badge.textContent = this.value;
                    badge.className = 'badge badge-' + this.value.toLowerCase();
                });
            }
            
            if (theme) {
                theme.addEventListener('input', function() {
                    document.getElementById('preview-theme').textContent = this.value;
                });
            }
        });

        function previewGame() {
            // Simulation de prévisualisation
            alert('Prévisualisation du jeu:\n\n' +
                  'Titre: ' + document.getElementById('titre').value + '\n' +
                  'Type: ' + document.getElementById('type').value + '\n' +
                  'Difficulté: ' + document.getElementById('difficulte').value + '\n' +
                  'Thème: ' + document.getElementById('theme').value);
        }
    </script>

    <style>
        .admin-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #ecf0f1;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .form-section h3 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #ddd;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-help {
            font-size: 0.875rem;
            color: #7f8c8d;
            margin-top: 0.25rem;
        }

        .form-help a {
            color: #3498db;
            text-decoration: none;
        }

        .form-help a:hover {
            text-decoration: underline;
        }

        .form-preview {
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #9b59b6;
        }

        .form-preview h4 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .preview-card {
            background: white;
            padding: 1.5rem;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .preview-header strong {
            color: #2c3e50;
            font-size: 1.3rem;
            flex: 1;
            margin-right: 1rem;
        }

        .preview-meta {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .preview-card p {
            color: #5d6d7e;
            margin: 0;
            line-height: 1.6;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #3498db;
            color: #3498db;
        }

        .btn-outline:hover {
            background: #3498db;
            color: white;
        }

        .sidebar-info {
            margin-top: 2rem;
            padding: 1rem;
            background: #2c3e50;
            border-radius: 4px;
        }

        .sidebar-info h4 {
            color: #ecf0f1;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            color: #bdc3c7;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .status-active {
            color: #2ecc71;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .admin-header-actions {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .preview-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .preview-header strong {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }

            .form-actions {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</body>
</html>