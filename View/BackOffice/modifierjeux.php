<?php
// View/BackOffice/modifierjeux.php
$controllerPath = __DIR__ . '/../../Controller/jeuxback.php';

if (!file_exists($controllerPath)) {
    die("Erreur: Fichier controller non trouvé à: " . $controllerPath);
}

require_once $controllerPath;

$controller = new JeuxBackController();

$jeu = null;
if (isset($_GET['id'])) {
    $jeu = $controller->getJeu($_GET['id']);
}

if (!$jeu) {
    header("Location: admin.php?section=jeux&error=Jeu non trouvé");
    exit();
}

$categories = $controller->getAllCategories();
?>

<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modifier Jeu - BackOffice</title>
  <link rel="shortcut icon" type="image/png" href="../../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../../assets/css/styles.min.css" />
  
  <!-- Template CSS moderne COMPLET -->
  <style>
    /* ================= THEME VARIABLES ================= */
    :root {
        --bg: #0f1018;
        --bg-soft: #151626;
        --sidebar-bg: #11121b;
        --sidebar-border: rgba(255,255,255,0.05);
        --sidebar-text: #e5e7eb;
        --sidebar-muted: #9ca3af;

        --header-bg: rgba(15,16,24,0.96);

        --card-bg: #181927;
        --card-border: rgba(148,163,184,0.25);

        --primary: #8b5cf6;
        --primary-soft: rgba(139,92,246,0.13);
        --primary-hover: #a855f7;

        --text: #f9fafb;
        --text-muted: #9ca3af;
        --border-subtle: rgba(148,163,184,0.25);

        --shadow-soft: 0 18px 45px rgba(15,23,42,0.55);
    }

    body.light {
        --bg: #f4f5fb;
        --bg-soft: #ffffff;
        --sidebar-bg: #ffffff;
        --sidebar-border: rgba(15,23,42,0.06);
        --sidebar-text: #111827;
        --sidebar-muted: #6b7280;

        --header-bg: rgba(255,255,255,0.96);

        --card-bg: #ffffff;
        --card-border: rgba(15,23,42,0.07);

        --primary: #7c3aed;
        --primary-soft: rgba(124,58,237,0.09);
        --primary-hover: #6d28d9;

        --text: #111827;
        --text-muted: #6b7280;
        --border-subtle: rgba(148,163,184,0.35);

        --shadow-soft: 0 18px 40px rgba(15,23,42,0.12);
    }

    /* ================= GLOBAL ================= */
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        min-height: 100vh;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif;
        background: radial-gradient(circle at top left, rgba(139,92,246,0.16), transparent 55%),
                    radial-gradient(circle at bottom right, rgba(236,72,153,0.14), transparent 55%),
                    var(--bg);
        color: var(--text);
        transition: background .25s ease, color .25s ease;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    /* ================= LAYOUT ================= */
    .admin-shell {
        display: flex;
        min-height: 100vh;
    }

    /* ---------- SIMPLE SIDEBAR ---------- */
    .admin-sidebar {
        width: 230px;
        background: var(--sidebar-bg);
        border-right: 1px solid var(--sidebar-border);
        display: flex;
        flex-direction: column;
        padding: 18px 16px 18px;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 4px 18px;
        border-bottom: 1px solid var(--sidebar-border);
        margin-bottom: 16px;
    }

    .sidebar-logo {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
        box-shadow: 0 10px 25px rgba(79,70,229,0.6);
    }

    .sidebar-title {
        display: flex;
        flex-direction: column;
    }
    .sidebar-title span:first-child {
        font-size: 14px;
        font-weight: 700;
        color: var(--sidebar-text);
    }
    .sidebar-title span:last-child {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .18em;
        color: var(--sidebar-muted);
    }

    .sidebar-nav {
        margin-top: 10px;
        list-style: none;
        padding: 0;
    }

    .sidebar-nav-label {
        font-size: 11px;
        text-transform: uppercase;
        color: var(--sidebar-muted);
        letter-spacing: .12em;
        margin: 10px 6px 6px;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 10px;
        border-radius: 9px;
        font-size: 14px;
        color: var(--sidebar-text);
        transition: background .18s ease, color .18s ease, transform .12s ease;
    }

    .sidebar-link i {
        font-size: 17px;
    }

    .sidebar-link:hover {
        background: var(--primary-soft);
        color: var(--primary);
        transform: translateX(2px);
    }

    .sidebar-link.active {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 0 0 1px rgba(255,255,255,0.08), 0 12px 25px rgba(88,28,135,0.6);
    }

    .sidebar-footer {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px dashed var(--sidebar-border);
        font-size: 12px;
        color: var(--sidebar-muted);
    }

    .sidebar-footer a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        margin-top: 6px;
        padding: 6px 8px;
        border-radius: 999px;
        transition: background .18s ease, color .18s ease;
    }

    .sidebar-footer a:hover {
        background: var(--primary-soft);
        color: var(--primary);
    }

    /* ---------- MAIN AREA ---------- */
    .admin-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    /* TOP BAR */
    .admin-header {
        position: sticky;
        top: 0;
        z-index: 20;
        height: 60px;
        background: var(--header-bg);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 22px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }

    .header-left-title {
        font-weight: 600;
    }

    .header-left-sub {
        font-size: 12px;
        color: var(--text-muted);
    }

    .header-search {
        flex: 0 0 280px;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        background: rgba(15,23,42,0.4);
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,0.35);
    }

    body.light .header-search {
        background: rgba(255,255,255,0.7);
        border-color: rgba(148,163,184,0.5);
    }

    .header-search input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 13px;
        background: transparent;
        color: var(--text);
    }

    .header-search i {
        font-size: 16px;
        color: var(--text-muted);
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    /* Toggle mode */
    .theme-toggle-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: var(--text-muted);
    }

    .theme-toggle {
        width: 46px;
        height: 22px;
        border-radius: 999px;
        background: rgba(15,23,42,0.75);
        position: relative;
        cursor: pointer;
    }

    body.light .theme-toggle {
        background: rgba(148,163,184,0.6);
    }

    .theme-toggle-thumb {
        width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #f9fafb;
        position: absolute;
        top: 2px;
        left: 3px;
        transition: transform .22s ease;
        box-shadow: 0 6px 16px rgba(15,23,42,0.65);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #0f172a;
    }

    body.light .theme-toggle-thumb {
        transform: translateX(22px);
    }

    .header-user {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }

    .user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
        box-shadow: 0 10px 20px rgba(88,28,135,0.7);
    }

    /* CONTENT WRAP */
    .admin-content {
        flex: 1;
        padding: 24px 24px 26px;
        min-height: calc(100vh - 60px);
    }

    .content-inner {
        max-width: 1320px;
        margin: 0 auto;
    }

    .card-shell {
        background: var(--card-bg);
        border-radius: 18px;
        border: 1px solid var(--card-border);
        box-shadow: var(--shadow-soft);
        padding: 22px 22px 20px;
    }

    /* Styles spécifiques pour les formulaires */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text);
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        background: var(--card-bg);
        color: var(--text);
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }

    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        background: var(--card-bg);
        color: var(--text);
        font-size: 14px;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(139,92,246,0.4);
    }

    .btn-secondary {
        background: var(--bg-soft);
        color: var(--text);
        border: 1px solid var(--border-subtle);
    }

    .btn-secondary:hover {
        background: var(--card-bg);
        transform: translateY(-1px);
    }

    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: var(--shadow-soft);
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: #10b981;
        color: white;
    }

    .alert-error {
        background: #ef4444;
        color: white;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text);
        margin: 0;
    }

    /* Styles pour l'image */
    .current-image {
        margin: 10px 0;
        padding: 15px;
        background: var(--primary-soft);
        border-radius: 8px;
        border: 1px solid var(--border-subtle);
    }

    .image-preview {
        max-width: 200px;
        height: auto;
        border-radius: 8px;
        border: 2px solid var(--border-subtle);
    }

    /* Simple utilities */
    .text-muted-small {
        font-size: 12px;
        color: var(--text-muted);
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -0.75rem;
    }

    .col-md-6 {
        flex: 0 0 50%;
        padding: 0 0.75rem;
    }

    .d-flex {
        display: flex;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .mt-4 {
        margin-top: 1.5rem;
    }

    .mb-4 {
        margin-bottom: 1.5rem;
    }

    @media (max-width: 960px) {
        .admin-sidebar {
            display: none;
        }
        .admin-header {
            padding-inline: 14px;
        }
        .header-search {
            display: none;
        }
        .admin-content {
            padding-inline: 14px;
        }
        .col-md-6 {
            flex: 0 0 100%;
        }
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
  </style>
</head>

<body class="light">
  <div class="admin-shell">
    
    <!-- Sidebar moderne -->
    <aside class="admin-sidebar">
      <div class="sidebar-brand">
        <div class="sidebar-logo">
          <i class="ti ti-joystick"></i>
        </div>
        <div class="sidebar-title">
          <span>GameAdmin</span>
          <span>BackOffice</span>
        </div>
      </div>

      <ul class="sidebar-nav">
        <li class="sidebar-nav-label">Dashboard</li>
        <li>
          <a href="admin.php" class="sidebar-link">
            <i class="ti ti-dashboard"></i>
            <span>Tableau de bord</span>
          </a>
        </li>
        
        <li class="sidebar-nav-label">Gestion</li>
        <li>
          <a href="admin.php?section=jeux" class="sidebar-link active">
            <i class="ti ti-joystick"></i>
            <span>Jeux</span>
          </a>
        </li>
        <li>
          <a href="admin.php?section=categories" class="sidebar-link">
            <i class="ti ti-category"></i>
            <span>Catégories</span>
          </a>
        </li>

        <li class="sidebar-nav-label">Site</li>
        <li>
          <a href="../FrontOffice/front.php" target="_blank" class="sidebar-link">
            <i class="ti ti-eye"></i>
            <span>Voir le site</span>
          </a>
        </li>
      </ul>

      <div class="sidebar-footer">
        <span>GameAdmin v1.0</span>
        <a href="../FrontOffice/front.php">
          <i class="ti ti-logout"></i>
          <span>Déconnexion</span>
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <!-- Header moderne -->
      <header class="admin-header">
        <div class="header-left">
          <div>
            <div class="header-left-title">Modifier un Jeu</div>
            <div class="header-left-sub">BackOffice - Administration</div>
          </div>
        </div>

        <div class="header-search">
          <i class="ti ti-search"></i>
          <input type="text" placeholder="Rechercher...">
        </div>

        <div class="header-right">
          <div class="theme-toggle-wrap">
            <span>Light</span>
            <div class="theme-toggle" onclick="toggleTheme()">
              <div class="theme-toggle-thumb">
                <i class="ti ti-sun"></i>
              </div>
            </div>
            <span>Dark</span>
          </div>

          <div class="header-user">
            <div class="user-avatar">
              <i class="ti ti-user"></i>
            </div>
            <span>Administrateur</span>
          </div>
        </div>
      </header>

      <!-- Content Area -->
      <div class="admin-content">
        <div class="content-inner">
          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
          <?php endif; ?>

          <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
          <?php endif; ?>

          <div class="card-shell">
            <div class="page-header">
              <h1 class="page-title">Modifier le Jeu</h1>
              <a href="admin.php?section=jeux" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i>
                Retour à la liste
              </a>
            </div>

            <form action="../../Controller/jeuxback.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="id" value="<?= isset($jeu['id']) ? $jeu['id'] : '' ?>">
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label" for="titre">Titre du jeu:</label>
                    <input type="text" class="form-control" id="titre" name="titre" value="<?= isset($jeu['titre']) ? htmlspecialchars($jeu['titre']) : '' ?>" required>
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="prix">Prix (€):</label>
                    <input type="number" class="form-control" id="prix" name="prix" step="0.01" min="0" value="<?= isset($jeu['prix']) ? $jeu['prix'] : '' ?>" required>
                  </div>

                  <!-- CHAMP URL AJOUTÉ -->
                  <div class="form-group">
                    <label class="form-label" for="lien_url">Lien URL du jeu:</label>
                    <input type="url" class="form-control" id="lien_url" name="lien_url" 
                           placeholder="https://example.com/jeu"
                           value="<?= isset($jeu['lien_url']) ? htmlspecialchars($jeu['lien_url']) : '' ?>">
                    <div class="text-muted-small">URL où le jeu est accessible en ligne (optionnel)</div>
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="categorie_id">Catégorie:</label>
                    <select class="form-select" id="categorie_id" name="categorie_id" required>
                      <option value="">Sélectionnez une catégorie</option>
                      <?php if (isset($categories) && is_array($categories)): ?>
                        <?php foreach ($categories as $categorie): ?>
                          <option value="<?= $categorie['id'] ?>" 
                                  <?= (isset($jeu['categorie_id']) && $categorie['id'] == $jeu['categorie_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categorie['nom']) ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label" for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description" rows="8" required><?= isset($jeu['description']) ? htmlspecialchars($jeu['description']) : '' ?></textarea>
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="image">Image:</label>
                    <?php if (isset($jeu['image']) && !empty($jeu['image'])): ?>
                      <div class="current-image">
                        <?php if (file_exists(__DIR__ . '/../../uploads/' . $jeu['image'])): ?>
                          <img src="../../uploads/<?= $jeu['image'] ?>" alt="Image actuelle" class="image-preview">
                        <?php elseif (isset($jeu['image']) && strpos($jeu['image'], 'assets/images/jeux/') !== false): ?>
                          <img src="../../<?= $jeu['image'] ?>" alt="Image actuelle" class="image-preview">
                        <?php endif; ?>
                        <p class="text-muted-small">Image actuelle: <?= basename($jeu['image']) ?></p>
                      </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="text-muted-small">Laissez vide pour conserver l'image actuelle</div>
                  </div>
                </div>
              </div>

              <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                  <i class="ti ti-edit"></i>
                  Modifier le Jeu
                </button>
                <a href="admin.php?section=jeux" class="btn btn-secondary">
                  <i class="ti ti-x"></i>
                  Annuler
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Fonction pour basculer entre light/dark mode
    function toggleTheme() {
      document.body.classList.toggle('light');
      document.body.classList.toggle('dark');
    }

    // Définir le mode par défaut
    document.body.classList.add('light');

    // Validation côté client pour l'URL
    document.getElementById('lien_url').addEventListener('blur', function(e) {
      const url = e.target.value.trim();
      if (url && !isValidUrl(url)) {
        alert('Veuillez entrer une URL valide (commençant par http:// ou https://)');
        e.target.focus();
      }
    });

    function isValidUrl(string) {
      try {
        new URL(string);
        return true;
      } catch (_) {
        return false;
      }
    }

    // Validation côté client
    document.querySelector('form').addEventListener('submit', function(e) {
      const titre = document.getElementById('titre').value.trim();
      const description = document.getElementById('description').value.trim();
      const prix = document.getElementById('prix').value;
      const categorie = document.getElementById('categorie_id').value;
      const lienUrl = document.getElementById('lien_url').value.trim();

      if (!titre) {
        alert('Le titre est requis');
        e.preventDefault();
        return;
      }

      if (!description) {
        alert('La description est requise');
        e.preventDefault();
        return;
      }

      if (!prix || prix <= 0) {
        alert('Le prix doit être supérieur à 0');
        e.preventDefault();
        return;
      }

      if (!categorie) {
        alert('Veuillez sélectionner une catégorie');
        e.preventDefault();
        return;
      }

      // Validation optionnelle de l'URL
      if (lienUrl && !isValidUrl(lienUrl)) {
        alert('Veuillez entrer une URL valide (commençant par http:// ou https://)');
        e.preventDefault();
        return;
      }
    });
  </script>
</body>
</html>