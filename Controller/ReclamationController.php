<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/../Model/Reclamation.php";

class ReclamationController {
       public function create(Reclamation $rec) {
    // Validation
    $validationErrors = $this->validateReclamation($rec);
    if (!empty($validationErrors)) {
        $error = "Erreur : données de la réclamation invalides.";
        include __DIR__ . '/../views/contact.html';
        return;
    }
    
    $sql = "INSERT INTO reclamation (full_name, email, subject, message)
            VALUES (:nom, :email, :sujet, :message)";
    
    $db = config::getConnexion();
    $q  = $db->prepare($sql);

    // Exécution de la requête pour insérer la réclamation
    $result = $q->execute([
        ':nom'     => $rec->getNom(),
        ':email'   => $rec->getEmail(),
        ':sujet'   => $rec->getSubject(),
        ':message' => $rec->getMessage()
    ]);
    
    // Si l'insertion a réussi, rediriger
    if ($result) {
        header("Location: indexv1.php?action=contact_success");
        exit;
    } else {
        // Si l'insertion a échoué, afficher un message d'erreur
        $error = "❌ Erreur lors de l'ajout de la réclamation.";
        include __DIR__ . '/../views/contact.html';
    }
}

    /* ============================
       AJOUTER UNE RÉCLAMATION
       ============================ */
    
    
    
       public function addReclamation(Reclamation $rec) {
        // Validation
        $validationErrors = $this->validateReclamation($rec);
        if (!empty($validationErrors)) {
            echo "Erreur : données de la réclamation invalides.";
            return false;
        }
        
        $sql = "INSERT INTO reclamation (full_name, email, subject, message)
                VALUES (:nom, :email, :sujet, :message)";
                
        $db = config::getConnexion();
        $q  = $db->prepare($sql);

        return $q->execute([
            ':nom'     => $rec->getNom(),
            ':email'   => $rec->getEmail(),
            ':sujet'   => $rec->getSubject(),
            ':message' => $rec->getMessage()
        ]);
    }

    /* ============================
       LISTER TOUTES LES RÉCLAMATIONS
       ============================ */
    /* ============================
   LISTER TOUTES LES RÉCLAMATIONS AVEC RECHERCHE
   ============================ */
    public function listReclamation($search = '') {
        // Ajouter la condition de recherche si nécessaire
        if ($search) {
            $sql = "SELECT * FROM reclamation WHERE full_name LIKE :search 
                    OR subject LIKE :search 
                    OR message LIKE :search 
                    ORDER BY id DESC";
        } else {
            $sql = "SELECT * FROM reclamation ORDER BY id DESC";
        }

        $db = config::getConnexion();
        $q = $db->prepare($sql);
        
        // Si une recherche est faite, on lie le paramètre
        if ($search) {
            $q->execute([':search' => '%' . $search . '%']);
        } else {
            $q->execute();
        }

        return $q->fetchAll(); // Retourner toutes les réclamations correspondantes
    }


    /* ============================
       RÉCUPÉRER UNE RÉCLAMATION PAR ID
       ============================ */
    public function getReclamation(int $id) {
        $sql = "SELECT * FROM reclamation WHERE id = :id";
        $db  = config::getConnexion();
        $q   = $db->prepare($sql);

        $q->execute([':id' => $id]);
        return $q->fetch();
    }

    /* ============================
       SUPPRIMER UNE RÉCLAMATION
       ============================ */
    public function deleteReclamation(int $id) {
        $sql = "DELETE FROM reclamation WHERE id = :id";
        $db  = config::getConnexion();
        $q   = $db->prepare($sql);

        return $q->execute([':id' => $id]);
    }

    /* ============================
       VALIDATION DES DONNÉES
       ============================ */
    public function validateReclamation(Reclamation $rec) {
        $errors = [];

        // Nom
        if (empty(trim($rec->getNom()))) {
            $errors[] = "Le nom est obligatoire.";
        }

        // Email
        if (!filter_var($rec->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }

        // Sujet
        if (empty(trim($rec->getSubject()))) {
            $errors[] = "Le sujet est obligatoire.";
        }

        // Message
        if (empty(trim($rec->getMessage()))) {
            $errors[] = "Le message est obligatoire.";
        }
        if (strlen($rec->getMessage()) < 10) {
            $errors[] = "Le message doit contenir au moins 10 caractères.";
        }

        return $errors;
    }
    
    public function updateReclamation(int $id, Reclamation $org) {
        // Validation avant mise à jour
        $validationErrors = $this->validateReclamation($org);
        if (!empty($validationErrors)) {
            echo "Erreur : reclamation invalide.";
            return false;
        }
        
        // Utiliser les noms de colonnes existants
        $sql = "UPDATE reclamation SET 
                full_name = :nom, email = :email, 
                subject = :subject, message = :message 
                WHERE id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        return $q->execute([
            ':id' => $id,
            ':nom' => $org->getNom(),
            ':email' => $org->getEmail(),
            ':subject' => $org->getSubject(),
            ':message' => $org->getMessage()
        ]);
    }
}
?>
