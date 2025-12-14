<?php
// ajouterjeux.php - Template EndGam
require_once '../../Model/config.php';

// Initialiser la connexion à la base de données
$database = new Database();
$conn = $database->getConnection();

$categories = [];
$message = '';
$message_type = '';

try {
    $sql_categories = "SELECT * FROM categories ORDER BY nom";
    $stmt_categories = $conn->prepare($sql_categories);
    $stmt_categories->execute();
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Erreur lors du chargement des catégories: " . $e->getMessage();
    $message_type = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $url_jeu = trim($_POST['url_jeu']);
    $categorie_id = $_POST['categorie_id'] ?: null;
    
    if (empty($titre) || empty($url_jeu)) {
        $message = "Le titre et l'URL du jeu sont obligatoires";
        $message_type = 'error';
    } else {
        try {
            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/jeux/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array(strtolower($file_extension), $allowed_extensions)) {
                    $filename = uniqid() . '.' . $file_extension;
                    $image_path = $upload_dir . $filename;
                    
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                        throw new Exception("Erreur lors de l'upload de l'image");
                    }
                } else {
                    throw new Exception("Format d'image non supporté. Utilisez JPG, PNG, GIF ou WebP.");
                }
            }
            
            // REQUÊTE CORRIGÉE : Supprimer la colonne 'statut' qui n'existe pas
            $sql = "INSERT INTO jeux (titre, description, url_jeu, image, categorie_id, date_ajout) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$titre, $description, $url_jeu, $image_path, $categorie_id]);
            
            $message = "Jeu ajouté avec succès!";
            $message_type = 'success';
            $_POST = [];
            
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EndGam - Ajouter un Jeu</title>

  <!-- === Feuilles de style du template === -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/slicknav.min.css">
  <link rel="stylesheet" href="css/owl.carousel.min.css">
  <link rel="stylesheet" href="css/animate.css">
  <link rel="stylesheet" href="css/style.css">

  <style>
  /* === STYLES DU FORMULAIRE AJOUT === */
  .add-game-section {
    padding: 120px 0;
    background: linear-gradient(45deg, #501755, #2d1854);
    color: #fff;
  }

  .add-game-section h2 {
    font-size: 42px;
    margin-bottom: 30px;
    text-transform: uppercase;
    text-align: center;
  }

  .game-form {
    max-width: 800px;
    margin: 0 auto;
    background: rgba(255,255,255,0.1);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 25px;
  }

  .form-group {
    text-align: left;
  }

  .form-group.full-width {
    grid-column: span 2;
  }

  .game-form label {
    display: block;
    text-align: left;
    margin-bottom: 8px;
    font-weight: 600;
  }

  .game-form input, .game-form select, .game-form textarea {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    color: #333;
    font-size: 1em;
  }

  .game-form textarea {
    height: 100px;
    resize: vertical;
  }

  .submit-btn {
    grid-column: span 2;
    padding: 15px 20px;
    border: none;
    border-radius: 10px;
    background: #ff9800;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
    font-size: 1.1em;
  }

  .submit-btn:hover {
    background: #e68900;
  }

  .message {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
  }

  .message.success {
    background: rgba(76, 175, 80, 0.2);
    color: #4caf50;
    border: 1px solid #4caf50;
  }

  .message.error {
    background: rgba(244, 67, 54, 0.2);
    color: #f44336;
    border: 1px solid #f44336;
  }

  .back-link {
    display: inline-block;
    margin-top: 20px;
    color: #ff9800;
    text-decoration: none;
    font-weight: bold;
  }

  .back-link:hover {
    color: #e68900;
    text-decoration: underline;
  }

  .url-hint {
    font-size: 0.9em;
    color: #ccc;
    margin-top: 5px;
  }

  @media (max-width: 768px) {
    .form-grid {
      grid-template-columns: 1fr;
    }
    
    .form-group.full-width {
      grid-column: span 1;
    }
    
    .submit-btn {
      grid-column: span 1;
    }
  }
  </style>
</head>

<body>

  <!-- ======= HEADER ======= -->
  <header class="header-section">
    <div class="header-warp">
      <div class="header-bar-warp d-flex">
        <a href="jeuxliste.php" class="site-logo"><img src="img/logo.png" alt="EndGam"></a>
        <nav class="top-nav-area w-100">
          <ul class="main-menu primary-menu">
            <li><a href="jeuxliste.php">Accueil</a></li>
            <li><a href="#">Gestion</a>
              <ul class="sub-menu">
                <li><a href="ajouterjeux.php">Ajouter Jeu</a></li>
                <li><a href="categoricliste.php">Catégories</a></li>
              </ul>
            </li>
            <li><a href="jeuxliste.php">Jeux</a></li>
            <li><a href="#">Contact</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <!-- ======= SECTION AJOUT DE JEU ======= -->
  <section class="add-game-section">
    <div class="container">
      <h2>➕ Ajouter un Jeu Friv</h2>
      
      <?php if ($message): ?>
        <div class="message <?= $message_type ?>">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" enctype="multipart/form-data" class="game-form">
        <div class="form-grid">
          <div class="form-group">
            <label for="titre">Titre du jeu *</label>
            <input type="text" id="titre" name="titre" 
                   value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" 
                   placeholder="Ex: Basketball Stars" required>
          </div>
          
          <div class="form-group">
            <label for="categorie_id">Catégorie</label>
            <select id="categorie_id" name="categorie_id">
              <option value="">-- Choisir une catégorie --</option>
              <?php foreach ($categories as $categorie): ?>
                <option value="<?= $categorie['id'] ?>" 
                        <?= ($_POST['categorie_id'] ?? '') == $categorie['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($categorie['nom']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group full-width">
            <label for="description">Description</label>
            <textarea id="description" name="description" 
                      placeholder="Décrivez brièvement le jeu..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
          </div>
          
          <div class="form-group full-width">
            <label for="url_jeu">URL du jeu (Friv) *</label>
            <input type="url" id="url_jeu" name="url_jeu" 
                   value="<?= htmlspecialchars($_POST['url_jeu'] ?? '') ?>" 
                   placeholder="https://friv.com/jeu-exemple" required>
            <div class="url-hint">Collez ici le lien direct vers le jeu sur Friv</div>
          </div>
          
          <div class="form-group full-width">
            <label for="image">Image du jeu</label>
            <input type="file" id="image" name="image" accept="image/*">
            <div class="url-hint">Formats supportés: JPG, PNG, GIF, WebP</div>
          </div>
        </div>
        
        <button type="submit" class="submit-btn">🎮 Ajouter le Jeu</button>
      </form>
      
      <div style="text-align: center; margin-top: 20px;">
        <a href="jeuxliste.php" class="back-link">← Retour à la liste des jeux</a>
      </div>
    </div>
  </section>

  <!-- ======= FOOTER ======= -->
  <footer class="footer-section">
    <div class="container">
      <p>© 2025 EndGam | Plateforme de Jeux Friv</p>
    </div>
  </footer>

  <!-- ======= JS Template ======= -->
  <script src="js/jquery-3.2.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.slicknav.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/main.js"></script>
</body>
</html>