<?php
include_once(__DIR__ . '/../../Controller/CoursController.php');
include_once(__DIR__ . '/../../Controller/ChapitreController.php');

$coursC = new CoursController();
$chapitreC = new ChapitreController();
$list = $coursC->listCours();
?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Liste des Cours - MindArena</title>
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
              <a class="sidebar-link active" href="coursList.php">
                <i class="ti ti-school"></i>
                <span class="hide-menu">Liste Cours</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="addCours.php">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <h4 class="card-title mb-0">Liste des Cours</h4>
                  <a href="addCours.php" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i> Ajouter un Cours
                  </a>
                </div>

                <?php if(isset($_GET['success'])): ?>
                  <div class="alert alert-success alert-dismissible fade show">
                    ✅ Cours ajouté avec succès !
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>
                <?php endif; ?>

                <?php if(empty($list)): ?>
                  <div class="text-center py-5">
                    <i class="ti ti-school fs-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun cours disponible</h4>
                    <a href="addCours.php" class="btn btn-primary mt-3">
                      <i class="ti ti-plus me-2"></i> Ajouter un Cours
                    </a>
                  </div>
                <?php else: ?>
                  <div class="table-responsive">
                    <table class="table text-nowrap mb-0 align-middle">
                      <thead class="text-dark fs-4">
                        <tr>
                          <th class="border-bottom-0">ID</th>
                          <th class="border-bottom-0">Titre</th>
                          <th class="border-bottom-0">Description</th>
                          <th class="border-bottom-0">Durée</th>
                          <th class="border-bottom-0">Niveau</th>
                          <th class="border-bottom-0">Formateur</th>
                          <th class="border-bottom-0">Chapitres</th>
                          <th class="border-bottom-0 text-end">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($list as $cours): 
                          $chapitres = $chapitreC->getChapitresByCoursId($cours['id']);
                          $badgeClass = 'bg-success';
                          switch($cours['niveau']) {
                            case 'Débutant': $badgeClass = 'bg-success'; break;
                            case 'Intermédiaire': $badgeClass = 'bg-warning'; break;
                            case 'Avancé': $badgeClass = 'bg-info'; break;
                            case 'Expert': $badgeClass = 'bg-danger'; break;
                          }
                        ?>
                        <tr>
                          <td class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">#<?php echo $cours['id']; ?></h6>
                          </td>
                          <td class="border-bottom-0">
                            <h6 class="fw-semibold mb-0"><?php echo htmlspecialchars($cours['titre']); ?></h6>
                          </td>
                          <td class="border-bottom-0">
                            <p class="mb-0 fw-normal" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                              <?php echo htmlspecialchars(substr($cours['description'], 0, 50)) . '...'; ?>
                            </p>
                          </td>
                          <td class="border-bottom-0">
                            <p class="mb-0 fw-normal">
                              <i class="ti ti-clock me-1"></i>
                              <?php echo $cours['duree']; ?>h
                            </p>
                          </td>
                          <td class="border-bottom-0">
                            <span class="badge <?php echo $badgeClass; ?> rounded-3 fw-semibold">
                              <?php echo htmlspecialchars($cours['niveau']); ?>
                            </span>
                          </td>
                          <td class="border-bottom-0">
                            <p class="mb-0 fw-normal">
                              <i class="ti ti-user me-1"></i>
                              <?php echo htmlspecialchars($cours['formateur']); ?>
                            </p>
                          </td>
                          <td class="border-bottom-0">
                            <span class="badge bg-primary rounded-3">
                              <?php echo count($chapitres); ?> chapitre(s)
                            </span>
                          </td>
                          <td class="border-bottom-0 text-end">
                            <div class="d-flex align-items-center gap-2 justify-content-end">
                              <a href="updateCours.php?id=<?php echo $cours['id']; ?>" class="btn btn-info btn-sm" title="Modifier">
                                <i class="ti ti-edit"></i>
                              </a>
                              <a href="deleteCours.php?id=<?php echo $cours['id']; ?>" 
                                 class="btn btn-danger btn-sm" 
                                 onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')"
                                 title="Supprimer">
                                <i class="ti ti-trash"></i>
                              </a>
                            </div>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
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
</body>
</html>
