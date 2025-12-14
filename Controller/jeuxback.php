<?php
// Controller/jeuxback.php - Version compatible avec votre classe Database
require_once __DIR__ . '/../config/Database.php';

class JeuxBackController {
    private $pdo;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->pdo = $this->db->getConnection();
        
        // Créer les tables si elles n'existent pas
        $this->createTablesIfNotExist();
    }

    private function createTablesIfNotExist() {
        try {
            // Vérifier si la table categories existe
            $check_categories = $this->pdo->query("SHOW TABLES LIKE 'categories'");
            if ($check_categories->rowCount() == 0) {
                $this->createCategoriesTable();
            }
            
            // Vérifier si la table jeux existe
            $check_jeux = $this->pdo->query("SHOW TABLES LIKE 'jeux'");
            if ($check_jeux->rowCount() == 0) {
                $this->createJeuxTable();
                $this->insertSampleData();
            } else {
                // Vérifier si la colonne prix_promotion existe, sinon l'ajouter
                $this->checkAndAddMissingColumns();
            }
            
        } catch (PDOException $e) {
            // Les tables n'existent pas encore, on les crée
            $this->createCategoriesTable();
            $this->createJeuxTable();
            $this->insertSampleData();
        }
    }

    private function checkAndAddMissingColumns() {
        try {
            // Vérifier si la colonne prix_promotion existe
            $check_column = $this->pdo->query("SHOW COLUMNS FROM jeux LIKE 'prix_promotion'");
            if ($check_column->rowCount() == 0) {
                // Ajouter la colonne manquante
                $this->pdo->exec("ALTER TABLE jeux ADD COLUMN prix_promotion DECIMAL(10,2) NULL AFTER prix");
            }
            
            // Vérifier si la colonne lien_url existe (NOUVEAU)
            $check_column_url = $this->pdo->query("SHOW COLUMNS FROM jeux LIKE 'lien_url'");
            if ($check_column_url->rowCount() == 0) {
                // Ajouter la colonne manquante
                $this->pdo->exec("ALTER TABLE jeux ADD COLUMN lien_url VARCHAR(500) NULL AFTER image");
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout de la colonne: " . $e->getMessage());
        }
    }

    private function createCategoriesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            description TEXT,
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);
    }

    private function createJeuxTable() {
        $sql = "CREATE TABLE IF NOT EXISTS jeux (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titre VARCHAR(255) NOT NULL,
            description TEXT,
            prix DECIMAL(10,2),
            prix_promotion DECIMAL(10,2) NULL,
            categorie_id INT,
            image VARCHAR(255),
            lien_url VARCHAR(500) NULL, -- NOUVEAU CHAMP
            date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL
        )";
        $this->pdo->exec($sql);
    }

    private function insertSampleData() {
        // Insérer des catégories de base
        $categories = [
            ['Action', 'Jeux palpitants remplis d\'action et d\'adrénaline'],
            ['Aventure', 'Explorez des mondes fantastiques et vivez des histoires captivantes'],
            ['RPG', 'Incarnez des héros et forgez votre destinée'],
            ['Stratégie', 'Jeux de réflexion et de planification'],
            ['Sport', 'Simulations sportives et compétitions'],
            ['FPS', 'Jeux de tir à la première personne']
        ];
        
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO categories (nom, description) VALUES (?, ?)");
        foreach ($categories as $categorie) {
            $stmt->execute($categorie);
        }
        
        // Insérer quelques jeux de test avec URL
        $jeux = [
            ['Cyberpunk 2077', 'Jeu de rôle en monde ouvert futuriste', 49.99, null, 3, null, 'https://store.steampowered.com/app/1091500/Cyberpunk_2077/'],
            ['Call of Duty', 'Jeu de tir à la première personne intense', 59.99, 49.99, 1, null, 'https://www.callofduty.com/'],
            ['FIFA 24', 'Simulation de football réaliste', 69.99, null, 5, null, 'https://www.ea.com/games/ea-sports-fc/fc-24'],
            ['Civilization VI', 'Jeu de stratégie au tour par tour', 39.99, 29.99, 4, null, 'https://civilization.com/'],
            ['The Witcher 3', 'RPG fantastique au monde ouvert', 39.99, null, 3, null, 'https://www.thewitcher.com/en/witcher3']
        ];
        
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO jeux (titre, description, prix, prix_promotion, categorie_id, image, lien_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($jeux as $jeu) {
            $stmt->execute($jeu);
        }
    }

    // Méthode pour récupérer un jeu par ID
    public function getJeu($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT j.*, c.nom as categorie_nom 
                                       FROM jeux j 
                                       LEFT JOIN categories c ON j.categorie_id = c.id 
                                       WHERE j.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getJeu: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour récupérer tous les jeux
    public function getAllJeux() {
        try {
            $sql = "SELECT j.*, c.nom as categorie_nom 
                    FROM jeux j 
                    LEFT JOIN categories c ON j.categorie_id = c.id 
                    ORDER BY j.id DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllJeux: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour récupérer toutes les catégories
    public function getAllCategories() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY nom");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllCategories: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour ajouter un jeu - MODIFIÉE POUR INCLURE LIEN_URL
    public function addJeu($titre, $description, $prix, $categorie_id, $quantite, $image = null, $lien_url = null) {
        try {
            $sql = "INSERT INTO jeux (titre, description, prix, categorie_id, image, lien_url) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $titre,
                $description, 
                $prix,
                $categorie_id,
                $image,
                $lien_url  // NOUVEAU PARAMÈTRE
            ]);
        } catch (PDOException $e) {
            error_log("Erreur addJeu: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour mettre à jour un jeu - MODIFIÉE POUR INCLURE LIEN_URL
    public function updateJeu($id, $data, $image = null) {
        try {
            if ($image) {
                $sql = "UPDATE jeux SET titre = ?, description = ?, prix = ?, prix_promotion = ?, categorie_id = ?, image = ?, lien_url = ? WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    $data['titre'],
                    $data['description'],
                    $data['prix'],
                    $data['prix_promotion'] ?? null,
                    $data['categorie_id'],
                    $image,
                    $data['lien_url'] ?? null,  // NOUVEAU
                    $id
                ]);
            } else {
                $sql = "UPDATE jeux SET titre = ?, description = ?, prix = ?, prix_promotion = ?, categorie_id = ?, lien_url = ? WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    $data['titre'],
                    $data['description'],
                    $data['prix'],
                    $data['prix_promotion'] ?? null,
                    $data['categorie_id'],
                    $data['lien_url'] ?? null,  // NOUVEAU
                    $id
                ]);
            }
        } catch (PDOException $e) {
            error_log("Erreur updateJeu: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour supprimer un jeu
    public function deleteJeu($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM jeux WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erreur deleteJeu: " . $e->getMessage());
            return false;
        }
    }

    // Gestion des requêtes
    public function handleRequest() {
        try {
            if (!isset($_POST['action'])) {
                return;
            }

            if ($_POST['action'] == 'update') {
                $id = $_POST['id'];
                $data = [
                    'titre' => $_POST['titre'],
                    'description' => $_POST['description'],
                    'prix' => $_POST['prix'],
                    'prix_promotion' => $_POST['prix_promotion'] ?? null,
                    'categorie_id' => $_POST['categorie_id'],
                    'lien_url' => $_POST['lien_url'] ?? null  // NOUVEAU
                ];
                
                // Gestion de l'image
                $image = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $uploadDir = __DIR__ . '/../uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileName = uniqid() . '_' . $_FILES['image']['name'];
                    $uploadFile = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $image = $fileName;
                    }
                }
                
                if ($this->updateJeu($id, $data, $image)) {
                    header("Location: ../View/BackOffice/admin.php?section=jeux&success=Jeu modifié avec succès");
                } else {
                    header("Location: ../View/BackOffice/admin.php?section=jeux&error=Erreur lors de la modification");
                }
                exit();
            }
            
            // Gérer d'autres actions (add, delete, etc.)
            elseif ($_POST['action'] == 'add') {
                $data = [
                    'titre' => $_POST['titre'],
                    'description' => $_POST['description'],
                    'prix' => $_POST['prix'],
                    'prix_promotion' => $_POST['prix_promotion'] ?? null,
                    'categorie_id' => $_POST['categorie_id'],
                    'lien_url' => $_POST['lien_url'] ?? null  // NOUVEAU
                ];
                
                $image = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $uploadDir = __DIR__ . '/../uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileName = uniqid() . '_' . $_FILES['image']['name'];
                    $uploadFile = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $image = $fileName;
                    }
                }
                
                if ($this->addJeu($data['titre'], $data['description'], $data['prix'], $data['categorie_id'], 0, $image, $data['lien_url'])) {
                    header("Location: ../View/BackOffice/admin.php?section=jeux&success=Jeu ajouté avec succès");
                } else {
                    header("Location: ../View/BackOffice/admin.php?section=jeux&error=Erreur lors de l'ajout");
                }
                exit();
            }
            
            elseif ($_POST['action'] == 'delete') {
                $id = $_POST['id'];
                if ($this->deleteJeu($id)) {
                    header("Location: ../View/BackOffice/admin.php?section=jeux&success=Jeu supprimé avec succès");
                } else {
                    header("Location: ../View/BackOffice/admin.php?section=jeux&error=Erreur lors de la suppression");
                }
                exit();
            }
        } catch (Exception $e) {
            error_log("Erreur handleRequest: " . $e->getMessage());
            header("Location: ../View/BackOffice/admin.php?section=jeux&error=Une erreur est survenue");
            exit();
        }
    }
}

// Gérer la requête si le controller est appelé directement
if (isset($_POST['action'])) {
    $controller = new JeuxBackController();
    $controller->handleRequest();
}
?>