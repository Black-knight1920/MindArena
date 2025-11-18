<?php
include_once(__DIR__ . '/../../Controller/CoursController.php');
include_once(__DIR__ . '/../../Controller/ChapitreController.php');
include_once(__DIR__ . '/../../Model/Cours.php');
include_once(__DIR__ . '/../../Model/Chapitre.php');

$coursC = new CoursController();
$chapitreC = new ChapitreController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajouter le cours
    $cours = new Cours(
        null,
        $_POST['titre'],
        $_POST['description'],
        $_POST['duree'],
        $_POST['niveau'],
        $_POST['formateur']
    );
    $coursC->addCours($cours);
    $cours_id = $coursC->getLastInsertId();

    // Ajouter les chapitres
    if (isset($_POST['chapitres']) && is_array($_POST['chapitres'])) {
        foreach ($_POST['chapitres'] as $chapitreData) {
            if (!empty($chapitreData['titre'])) {
                $chapitre = new Chapitre(
                    null,
                    $chapitreData['titre'],
                    $chapitreData['description'] ?? '',
                    $chapitreData['ordre'] ?? 1,
                    $cours_id
                );
                $chapitreC->addChapitre($chapitre);
            }
        }
    }

    header('Location: coursList.php?success=add');
    exit;
}
?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ajouter un Cours - MindArena</title>
  <link rel="shortcut icon" type="image/png" href="../FrontOffice/images/mindarena-logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <link rel="stylesheet" href="./assets/css/icons/tabler-icons/tabler-icons.css" />
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./index.html" class="text-nowrap logo-img">
            <img src="assets/images/logos/logo.svg" alt="" />
          </a>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="sidebar-item">
              <a class="sidebar-link" href="index.html">
                <i class="ti ti-atom"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="coursList.php">
                <i class="ti ti-school"></i>
                <span class="hide-menu">Liste Cours</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link active" href="addCours.php">
                <i class="ti ti-plus"></i>
                <span class="hide-menu">Ajouter Cours</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>
    <!-- Sidebar End -->

    <!-- Main wrapper -->
    <div class="body-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-4">Ajouter un Nouveau Cours</h4>
                
                <form method="POST" action="addCours.php">
                  <!-- Informations du Cours -->
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label class="form-label">Titre du Cours *</label>
                      <input type="text" class="form-control" name="titre" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Formateur *</label>
                      <input type="text" class="form-control" name="formateur" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label class="form-label">Description</label>
                      <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-md-4">
                      <label class="form-label">Durée (heures) *</label>
                      <input type="number" class="form-control" name="duree" required min="1">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Niveau *</label>
                      <select class="form-select" name="niveau" required>
                        <option value="">Sélectionner...</option>
                        <option value="Débutant">Débutant</option>
                        <option value="Intermédiaire">Intermédiaire</option>
                        <option value="Avancé">Avancé</option>
                        <option value="Expert">Expert</option>
                      </select>
                    </div>
                  </div>

                  <hr class="my-4">

                  <!-- Chapitres -->
                  <h5 class="mb-3">Chapitres du Cours</h5>
                  <div id="chapitres-container">
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
                  </div>

                  <button type="button" class="btn btn-info mb-3" id="add-chapitre">
                    <i class="ti ti-plus me-2"></i> Ajouter un Chapitre
                  </button>

                  <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                      <i class="ti ti-check me-2"></i> Enregistrer le Cours
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

  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script>
    let chapitreIndex = 1;
    
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

