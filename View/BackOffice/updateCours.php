<?php
include_once(__DIR__ . '/../../Controller/CoursController.php');
include_once(__DIR__ . '/../../Controller/ChapitreController.php');
include_once(__DIR__ . '/../../Model/Cours.php');
include_once(__DIR__ . '/../../Model/Chapitre.php');

$coursC = new CoursController();
$chapitreC = new ChapitreController();

$cours = null;
$chapitres = [];

// Récupérer le cours à modifier
if (isset($_POST['id']) || isset($_GET['id'])) {
    $id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
    $cours = $coursC->showCours($id);
    $chapitres = $chapitreC->getChapitresByCoursId($id);
}

// Traitement de la mise à jour (seulement si tous les champs requis sont présents)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update']) && isset($_POST['titre']) && !empty($_POST['titre'])) {
    $cours = new Cours(
        $_POST['id'],
        $_POST['titre'],
        $_POST['description'] ?? '',
        $_POST['duree'],
        $_POST['niveau'],
        $_POST['formateur']
    );
    $coursC->updateCours($cours, $_POST['id']);

    // Supprimer les anciens chapitres
    $oldChapitres = $chapitreC->getChapitresByCoursId($_POST['id']);
    foreach ($oldChapitres as $oldChapitre) {
        $chapitreC->deleteChapitre($oldChapitre['id']);
    }

    // Ajouter les nouveaux chapitres
    if (isset($_POST['chapitres']) && is_array($_POST['chapitres'])) {
        foreach ($_POST['chapitres'] as $chapitreData) {
            if (!empty($chapitreData['titre'])) {
                $chapitre = new Chapitre(
                    null,
                    $chapitreData['titre'],
                    $chapitreData['description'] ?? '',
                    $chapitreData['ordre'] ?? 1,
                    $_POST['id']
                );
                $chapitreC->addChapitre($chapitre);
            }
        }
    }

    header('Location: coursList.php?success=update');
    exit;
}

if (!$cours) {
    header('Location: coursList.php');
    exit;
}
?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modifier un Cours - MindArena</title>
  <link rel="shortcut icon" type="image/png" href="../FrontOffice/images/mindarena-logo.svg" />
  <link rel="stylesheet" href="../FrontOffice/assets/css/styles.min.css" />
  <link rel="stylesheet" href="../FrontOffice/assets/css/icons/tabler-icons/tabler-icons.css" />
  <style>
    .mindarena-logo-section {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 15px 20px;
    }
    .mindarena-logo-section img {
      height: 40px;
      width: auto;
    }
    .esprit-logo-small {
      height: 25px;
      width: auto;
      opacity: 0.8;
    }
  </style>
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <div class="mindarena-logo-section">
            <img src="../FrontOffice/images/mindarena-logo.svg" alt="MindArena">
            <div>
              <h5 class="mb-0" style="color: #5d87ff; font-weight: 700;">MindArena</h5>
              <img src="../FrontOffice/images/esprit-logo.svg" alt="Esprit" class="esprit-logo-small" style="height: 25px; width: auto; opacity: 0.8;">
            </div>
          </div>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-6"></i>
          </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">Menu</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="index.html" aria-expanded="false">
                <i class="ti ti-atom"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)" aria-expanded="false">
                <div class="d-flex align-items-center gap-3">
                  <span class="d-flex">
                    <i class="ti ti-school"></i>
                  </span>
                  <span class="hide-menu">Cours</span>
                </div>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a class="sidebar-link justify-content-between" href="coursList.php">
                    <div class="d-flex align-items-center gap-3">
                      <div class="round-16 d-flex align-items-center justify-content-center">
                        <i class="ti ti-circle"></i>
                      </div>
                      <span class="hide-menu">Liste des Cours</span>
                    </div>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link justify-content-between" href="addCours.php">
                    <div class="d-flex align-items-center gap-3">
                      <div class="round-16 d-flex align-items-center justify-content-center">
                        <i class="ti ti-circle"></i>
                      </div>
                      <span class="hide-menu">Ajouter un Cours</span>
                    </div>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </aside>
    <!-- Sidebar End -->

    <!-- Main wrapper -->
    <div class="body-wrapper">
      <!-- Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <i class="ti ti-bell-ringing"></i>
                  <div class="notification bg-primary rounded-circle"></div>
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <div class="d-flex align-items-center">
                    <div class="user-profile-img">
                      <img src="../FrontOffice/assets/images/profile/user-1.jpg" class="rounded-circle"
                        width="35" height="35" alt="" />
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!-- Header End -->
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-4">Modifier le Cours</h4>
                
                <form method="POST" action="updateCours.php">
                  <input type="hidden" name="id" value="<?php echo $cours['id']; ?>">
                  
                  <!-- Informations du Cours -->
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label class="form-label">Titre du Cours *</label>
                      <input type="text" class="form-control" name="titre" value="<?php echo htmlspecialchars($cours['titre']); ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Formateur *</label>
                      <input type="text" class="form-control" name="formateur" value="<?php echo htmlspecialchars($cours['formateur']); ?>" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label class="form-label">Description</label>
                      <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($cours['description']); ?></textarea>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-md-4">
                      <label class="form-label">Durée (heures) *</label>
                      <input type="number" class="form-control" name="duree" value="<?php echo $cours['duree']; ?>" required min="1">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Niveau *</label>
                      <select class="form-select" name="niveau" required>
                        <option value="">Sélectionner...</option>
                        <option value="Débutant" <?php echo $cours['niveau'] == 'Débutant' ? 'selected' : ''; ?>>Débutant</option>
                        <option value="Intermédiaire" <?php echo $cours['niveau'] == 'Intermédiaire' ? 'selected' : ''; ?>>Intermédiaire</option>
                        <option value="Avancé" <?php echo $cours['niveau'] == 'Avancé' ? 'selected' : ''; ?>>Avancé</option>
                        <option value="Expert" <?php echo $cours['niveau'] == 'Expert' ? 'selected' : ''; ?>>Expert</option>
                      </select>
                    </div>
                  </div>

                  <hr class="my-4">

                  <!-- Chapitres -->
                  <h5 class="mb-3">Chapitres du Cours</h5>
                  <div id="chapitres-container">
                    <?php if(empty($chapitres)): ?>
                    <div class="chapitre-item mb-3 p-3 border rounded">
                      <div class="row">
                        <div class="col-md-1">
                          <label class="form-label">Ordre</label>
                          <input type="number" class="form-control" name="chapitres[0][ordre]" value="1" min="1">
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Titre du Chapitre</label>
                          <input type="text" class="form-control" name="chapitres[0][titre]" placeholder="Ex: Introduction">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Description</label>
                          <input type="text" class="form-control" name="chapitres[0][description]" placeholder="Description du chapitre">
                        </div>
                        <div class="col-md-1">
                          <label class="form-label">&nbsp;</label>
                          <button type="button" class="btn btn-danger btn-sm w-100 remove-chapitre" style="display:none;">
                            <i class="ti ti-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                    <?php else: ?>
                      <?php foreach($chapitres as $index => $chapitre): ?>
                      <div class="chapitre-item mb-3 p-3 border rounded">
                        <div class="row">
                          <div class="col-md-1">
                            <label class="form-label">Ordre</label>
                            <input type="number" class="form-control" name="chapitres[<?php echo $index; ?>][ordre]" value="<?php echo $chapitre['ordre']; ?>" min="1">
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">Titre du Chapitre</label>
                            <input type="text" class="form-control" name="chapitres[<?php echo $index; ?>][titre]" value="<?php echo htmlspecialchars($chapitre['titre']); ?>" placeholder="Ex: Introduction">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="chapitres[<?php echo $index; ?>][description]" value="<?php echo htmlspecialchars($chapitre['description']); ?>" placeholder="Description du chapitre">
                          </div>
                          <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-chapitre" <?php echo count($chapitres) > 1 ? '' : 'style="display:none;"'; ?>>
                              <i class="ti ti-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>

                  <button type="button" class="btn btn-info mb-3" id="add-chapitre">
                    <i class="ti ti-plus me-2"></i> Ajouter un Chapitre
                  </button>

                  <div class="mt-4">
                    <button type="submit" name="update" class="btn btn-primary">
                      <i class="ti ti-check me-2"></i> Enregistrer les Modifications
                    </button>
                    <a href="coursList.php" class="btn btn-secondary">
                      <i class="ti ti-x me-2"></i> Annuler
                    </a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../FrontOffice/assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../FrontOffice/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../FrontOffice/assets/js/sidebarmenu.js"></script>
  <script src="../FrontOffice/assets/js/app.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script>
    let chapitreIndex = <?php echo count($chapitres); ?>;
    
    $('#add-chapitre').click(function() {
      const chapitreHtml = `
        <div class="chapitre-item mb-3 p-3 border rounded">
          <div class="row">
            <div class="col-md-1">
              <label class="form-label">Ordre</label>
              <input type="number" class="form-control" name="chapitres[${chapitreIndex}][ordre]" value="${chapitreIndex + 1}" min="1">
            </div>
            <div class="col-md-4">
              <label class="form-label">Titre du Chapitre</label>
              <input type="text" class="form-control" name="chapitres[${chapitreIndex}][titre]" placeholder="Ex: Introduction">
            </div>
            <div class="col-md-6">
              <label class="form-label">Description</label>
              <input type="text" class="form-control" name="chapitres[${chapitreIndex}][description]" placeholder="Description du chapitre">
            </div>
            <div class="col-md-1">
              <label class="form-label">&nbsp;</label>
              <button type="button" class="btn btn-danger btn-sm w-100 remove-chapitre">
                <i class="ti ti-trash"></i>
              </button>
            </div>
          </div>
        </div>
      `;
      $('#chapitres-container').append(chapitreHtml);
      chapitreIndex++;
      updateRemoveButtons();
    });

    $(document).on('click', '.remove-chapitre', function() {
      $(this).closest('.chapitre-item').remove();
      updateRemoveButtons();
    });

    function updateRemoveButtons() {
      const items = $('.chapitre-item');
      if (items.length > 1) {
        $('.remove-chapitre').show();
      } else {
        $('.remove-chapitre').hide();
      }
    }
  </script>
</body>
</html>

