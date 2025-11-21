<?php
require_once __DIR__."/../../../Controller/OrganisationController.php";
require_once __DIR__."/../../../Model/Organisation.php";

$orgCtrl = new OrganisationController();
$message = '';
$messageType = '';

if ($_POST) {
    try {
        $organisation = new Organisation(
            null,
            trim($_POST['nom']),
            trim($_POST['description']),
            trim($_POST['website_url'] ?? '')
        );
        
        // Validation côté serveur
        $validationErrors = $orgCtrl->validateOrganisation($organisation);
        
        if (empty($validationErrors)) {
            if ($orgCtrl->addOrganisation($organisation)) {
                $message = "✅ Organisation ajoutée avec succès!";
                $messageType = 'success';
                header("refresh:2;url=organisationList.php");
            }
        } else {
            $message = "❌ Erreurs de validation:<br>• " . implode("<br>• ", $validationErrors);
            $messageType = 'error';
        }
        
    } catch (Exception $e) {
        $message = "❌ Erreur: " . $e->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Organisation - Mind Arena</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #5e72e4;
            --secondary: #8392ab;
            --success: #2dce89;
            --danger: #f5365c;
            --warning: #fb6340;
            --dark: #212529;
            --sidebar-bg: #172b4d;
            --sidebar-color: #fff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fe;
            color: #525f7f;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left-color: var(--primary);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s;
            min-height: 100vh;
        }

        .main-header {
            background: #fff;
            padding: 1.5rem 2rem;
            box-shadow: 0 1px 15px rgba(0, 0, 0, 0.04);
            border-bottom: 1px solid #e9ecef;
        }

        /* Form Styling */
        .form-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .form-card {
            padding: 2rem;
        }

        .btn {
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(87deg, var(--primary) 0, #825ee4 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        .btn-outline-secondary {
            border: 2px solid var(--secondary);
            color: var(--secondary);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-control:focus {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
        }

        /* Validation Styles */
        .error-field {
            border-color: var(--danger) !important;
            box-shadow: 0 0 0 0.2rem rgba(245, 54, 92, 0.25) !important;
        }

        .success-field {
            border-color: var(--success) !important;
            box-shadow: 0 0 0 0.2rem rgba(45, 206, 137, 0.25) !important;
        }

        .validation-error {
            color: var(--danger);
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: block;
        }

        .char-count {
            font-size: 0.8rem;
            color: var(--secondary);
            margin-top: 0.25rem;
        }

        .char-count.warning {
            color: var(--warning);
        }

        .message {
            padding: 1rem 1.5rem;
            margin: 1rem 0;
            border-radius: 8px;
            border: 1px solid transparent;
        }

        .success {
            background: rgba(45, 206, 137, 0.1);
            color: #155724;
            border-color: rgba(45, 206, 137, 0.2);
        }

        .error {
            background: rgba(245, 54, 92, 0.1);
            color: #721c24;
            border-color: rgba(245, 54, 92, 0.2);
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -280px;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <!-- ======= Sidebar ======= -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h5 class="mb-0 text-white">
                <i class="bi bi-rocket-takeoff me-2"></i>
                Mind Arena
            </h5>
            <small class="text-muted">Backoffice</small>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-link active" href="addOrganisation.php">
                <i class="bi bi-plus-circle me-2"></i>
                Nouvelle organisation
            </a>
            <a class="nav-link" href="organisationList.php">
                <i class="bi bi-building me-2"></i>
                Liste des organisations
            </a>
            <a class="nav-link" href="../don/donList.php">
                <i class="bi bi-currency-euro me-2"></i>
                Dons
            </a>
            <a class="nav-link" href="/projet-dons/backoffice.php">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </nav>
    </div>

    <!-- ======= Main Content ======= -->
    <div class="main-content">
        <div class="main-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nouvelle Organisation
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 mt-2">
                            <li class="breadcrumb-item"><a href="/projet-dons/backoffice.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="organisationList.php">Organisations</a></li>
                            <li class="breadcrumb-item active">Nouvelle</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="organisationList.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>

        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-container">
                        <div class="form-card">
                            <?php if ($message): ?>
                                <div class="message <?= $messageType === 'success' ? 'success' : 'error' ?>">
                                    <?= $message ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" id="orgForm" novalidate>
                                <div class="mb-4">
                                    <label for="nom" class="form-label">
                                        <i class="bi bi-building me-1"></i>
                                        Nom de l'organisation
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nom" 
                                           name="nom" 
                                           placeholder="Ex: Médecins Sans Frontières">
                                    <span class="validation-error" id="nomError"></span>
                                    <div class="char-count" id="nomCount">0 caractères</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="description" class="form-label">
                                        <i class="bi bi-text-paragraph me-1"></i>
                                        Description
                                    </label>
                                    <textarea class="form-control" 
                                              id="description" 
                                              name="description" 
                                              rows="5"
                                              placeholder="Décrivez l'organisation, sa mission, ses objectifs..."></textarea>
                                    <span class="validation-error" id="descriptionError"></span>
                                    <div class="char-count" id="descriptionCount">0 caractères</div>
                                </div>

                                <div class="mb-4">
                                    <label for="website_url" class="form-label">
                                        <i class="bi bi-globe me-1"></i>
                                        Site Web (URL)
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="website_url" 
                                           name="website_url" 
                                           placeholder="Ex: https://www.organisation.org">
                                    <span class="validation-error" id="websiteUrlError"></span>
                                    <div class="char-count" id="websiteUrlCount">0 caractères</div>
                                </div>
                                
                                <div class="d-flex gap-3 pt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Enregistrer l'organisation
                                    </button>
                                    <a href="organisationList.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Validation côté client pour les organisations
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('orgForm');
            const fields = {
                nom: document.getElementById('nom'),
                description: document.getElementById('description'),
                website_url: document.getElementById('website_url')
            };

            const counters = {
                nom: document.getElementById('nomCount'),
                description: document.getElementById('descriptionCount'),
                website_url: document.getElementById('websiteUrlCount')
            };

            // Limites de caractères
            const limits = {
                nom: 100,
                description: 500,
                website_url: 255
            };

            // Compteur de caractères en temps réel
            fields.nom.addEventListener('input', function() {
                updateCharCount(this, counters.nom, limits.nom);
                validateField('nom');
            });

            fields.description.addEventListener('input', function() {
                updateCharCount(this, counters.description, limits.description);
                validateField('description');
            });

            fields.website_url.addEventListener('input', function() {
                updateCharCount(this, counters.website_url, limits.website_url);
                validateField('website_url');
            });

            // Validation en temps réel pour tous les champs
            Object.keys(fields).forEach(fieldName => {
                fields[fieldName].addEventListener('blur', function() {
                    validateField(fieldName);
                });
            });

            // Validation à la soumission
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                Object.keys(fields).forEach(fieldName => {
                    if (!validateField(fieldName)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    // Afficher un message d'erreur plus élégant
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'message error';
                    alertDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Veuillez corriger les erreurs dans le formulaire avant de soumettre.';
                    form.parentNode.insertBefore(alertDiv, form);
                    
                    // Scroll vers le haut pour voir le message
                    alertDiv.scrollIntoView({ behavior: 'smooth' });
                }
            });

            function updateCharCount(field, counter, maxLength) {
                const length = field.value.length;
                counter.textContent = `${length} caractères`;
                if (length > maxLength * 0.8) {
                    counter.classList.add('warning');
                } else {
                    counter.classList.remove('warning');
                }
                
                // Limiter manuellement la longueur
                if (length > maxLength) {
                    field.value = field.value.substring(0, maxLength);
                    counter.textContent = `${maxLength} caractères (limite atteinte)`;
                    counter.classList.add('warning');
                }
            }

            function validateField(fieldName) {
                const field = fields[fieldName];
                const errorElement = document.getElementById(fieldName + 'Error');
                const value = field.value.trim();
                const maxLength = limits[fieldName];
                
                // Réinitialiser
                field.classList.remove('error-field', 'success-field');
                errorElement.textContent = '';
                
                let isValid = true;
                let message = '';
                
                switch(fieldName) {
                    case 'nom':
                        if (!value) {
                            message = "Le nom de l'organisation est obligatoire";
                            isValid = false;
                        } else if (value.length < 2) {
                            message = "Le nom doit contenir au moins 2 caractères";
                            isValid = false;
                        } else if (value.length > maxLength) {
                            message = `Le nom ne peut pas dépasser ${maxLength} caractères`;
                            isValid = false;
                        }
                        break;
                        
                    case 'description':
                        if (!value) {
                            message = "La description est obligatoire";
                            isValid = false;
                        } else if (value.length < 10) {
                            message = "La description doit contenir au moins 10 caractères";
                            isValid = false;
                        } else if (value.length > maxLength) {
                            message = `La description ne peut pas dépasser ${maxLength} caractères`;
                            isValid = false;
                        }
                        break;

                    case 'website_url':
                        // Le champ URL est optionnel, mais s'il est rempli, on valide le format
                        if (value && !isValidUrl(value)) {
                            message = "Veuillez entrer une URL valide (commençant par http:// ou https://)";
                            isValid = false;
                        } else if (value.length > maxLength) {
                            message = `L'URL ne peut pas dépasser ${maxLength} caractères`;
                            isValid = false;
                        }
                        break;
                }
                
                if (!isValid) {
                    field.classList.add('error-field');
                    errorElement.textContent = message;
                } else if (value) {
                    field.classList.add('success-field');
                }
                
                return isValid;
            }

            function isValidUrl(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }

            // Initialiser les compteurs
            Object.keys(fields).forEach(fieldName => {
                const field = fields[fieldName];
                const counter = counters[fieldName];
                updateCharCount(field, counter, limits[fieldName]);
            });
        });
    </script>
</body>
</html>