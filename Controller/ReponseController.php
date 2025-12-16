<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/../Model/Reponse.php";

class ReponseController {

    /* ============================
       AJOUTER UNE RÉPONSE
       ============================ */
    public function addReponse(Reponse $reponse) {
        // Validation
        $validationErrors = $this->validateReponse($reponse);
        if (!empty($validationErrors)) {
            // Afficher les erreurs
            echo "Erreur : données de la réponse invalides.";
            return false;
        }
        
        // Requête pour ajouter une réponse
        $sql = "INSERT INTO reponse (reclamationId, message, created_by)
                VALUES (:reclamationId, :message, :created_by)";
                
        $db = config::getConnexion();
        $q  = $db->prepare($sql);

        // Exécution de la requête avec les données
        return $q->execute([
            ':reclamationId' => $reponse->getReclamationId(),
            ':message'       => $reponse->getMessage(),
            ':created_by'    => $reponse->getCreatedBy()
        ]);
    }

    /* ============================
       LISTER TOUTES LES RÉPONSES
       ============================ */
    public function listReponse() {
        $sql = "SELECT * FROM reponse ORDER BY id DESC";
        $db  = config::getConnexion();
        return $db->query($sql); // Retourner le résultat de la requête
    }

    /* ============================
       RÉCUPÉRER LES RÉPONSES D'UNE RÉCLAMATION
       ============================ */
    public function getReponsesByReclamationId(int $reclamation_id) {
        // Utilisation de reclamationId ici
        $sql = "SELECT * FROM reponse WHERE reclamationId = :reclamation_id ORDER BY created_at DESC"; // Correction ici
        $db  = config::getConnexion();
        $q   = $db->prepare($sql);
        $q->execute([':reclamation_id' => $reclamation_id]);
        return $q->fetchAll(); // Retourne toutes les réponses de la réclamation
    }

    /* ============================
       RÉCUPÉRER UNE RÉPONSE PAR ID
       ============================ */
    public function getReponse(int $id) {
        $sql = "SELECT * FROM reponse WHERE id = :id";
        $db  = config::getConnexion();
        $q   = $db->prepare($sql);
        $q->execute([':id' => $id]);
        return $q->fetch(); // Retourner la réponse spécifique par ID
    }

    /* ============================
       MODIFIER UNE RÉPONSE
       ============================ */
    public function updateReponse(int $id, Reponse $reponse) {
        // Validation avant mise à jour
        $validationErrors = $this->validateReponse($reponse);
        if (!empty($validationErrors)) {
            // Afficher les erreurs
            echo "Erreur : réponse invalide.";
            return false;
        }
        
        $sql = "UPDATE reponse SET 
                reclamationId = :reclamationId,  // Utilisation de reclamationId
                message = :message, 
                created_by = :created_by
                WHERE id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        
        // Exécution de la requête de mise à jour
        return $q->execute([
            ':id' => $id,
            ':reclamationId' => $reponse->getReclamationId(),
            ':message' => $reponse->getMessage(),
            ':created_by' => $reponse->getCreatedBy()
        ]);
    }

    /* ============================
       SUPPRIMER UNE RÉPONSE
       ============================ */
    public function deleteReponse(int $id) {
        $sql = "DELETE FROM reponse WHERE id = :id";
        $db  = config::getConnexion();
        $q   = $db->prepare($sql);
        return $q->execute([':id' => $id]); // Suppression de la réponse
    }

    /* ============================
       COMPTER LES RÉPONSES D'UNE RÉCLAMATION
       ============================ */
    public function countReponsesByReclamationId(int $reclamation_id) {
        $sql = "SELECT COUNT(*) as count FROM reponse WHERE reclamationId = :reclamation_id"; 
        $db  = config::getConnexion();
        $q   = $db->prepare($sql);
        $q->execute([':reclamation_id' => $reclamation_id]);
        $result = $q->fetch();
        return $result['count'];  // Retourner le nombre de réponses
    }

    /* ============================
       VALIDATION DES DONNÉES
       ============================ */
    public function validateReponse(Reponse $reponse) {
        $errors = [];

        // Validation de l'ID de réclamation
        if (empty($reponse->getReclamationId()) || $reponse->getReclamationId() <= 0) {
            $errors[] = "L'ID de la réclamation est obligatoire.";
        }

        // Validation du texte de la réponse
        if (empty(trim($reponse->getMessage()))) {
            $errors[] = "Le texte de la réponse est obligatoire.";
        }
        if (strlen(trim($reponse->getMessage())) < 5) {
            $errors[] = "La réponse doit contenir au moins 5 caractères.";
        }

        return $errors; // Retourner les erreurs de validation, le cas échéant
    }
}
?>
