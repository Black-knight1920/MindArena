<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/../Model/Organisation.php";

class OrganisationController {
    
    public function addOrganisation(Organisation $org) {
        // Validation avant insertion
        $validationErrors = $this->validateOrganisation($org);
        if (!empty($validationErrors)) {
            throw new Exception(implode(", ", $validationErrors));
        }
        
        // Utiliser les noms de colonnes existants dans la base
        $sql = "INSERT INTO organisation (nom, description) 
                VALUES (:nom, :description)";
        $db = config::getConnexion();
        
        try {
            $q = $db->prepare($sql);
            $result = $q->execute([
                ':nom' => $org->getNom(),
                ':description' => $org->getDescription()
            ]);
            
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erreur: " . $e->getMessage());
        }
    }

    public function listOrganisations() {
        $sql = "SELECT o.*, 
                (SELECT COALESCE(SUM(d.montant), 0) FROM don d WHERE d.organisationId = o.id) as montant_total
                FROM organisation o 
                ORDER BY o.nom";
        $db = config::getConnexion();
        return $db->query($sql)->fetchAll();
    }

    public function getOrganisation(int $id) {
        $sql = "SELECT o.*,
                (SELECT COALESCE(SUM(d.montant), 0) FROM don d WHERE d.organisationId = o.id) as montant_total
                FROM organisation o 
                WHERE o.id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $q->execute([':id' => $id]);
        return $q->fetch();
    }

    public function updateOrganisation(int $id, Organisation $org) {
        // Validation avant mise à jour
        $validationErrors = $this->validateOrganisation($org);
        if (!empty($validationErrors)) {
            throw new Exception(implode(", ", $validationErrors));
        }
        
        // Utiliser les noms de colonnes existants
        $sql = "UPDATE organisation SET 
                nom = :nom, description = :description 
                WHERE id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        return $q->execute([
            ':id' => $id,
            ':nom' => $org->getNom(),
            ':description' => $org->getDescription()
        ]);
    }

    public function deleteOrganisation(int $id) {
        $sql = "DELETE FROM organisation WHERE id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        return $q->execute([':id' => $id]);
    }

    public function getActiveOrganisations() {
        $sql = "SELECT o.*,
                (SELECT COALESCE(SUM(d.montant), 0) FROM don d WHERE d.organisationId = o.id) as montant_total
                FROM organisation o 
                ORDER BY o.nom";
        $db = config::getConnexion();
        return $db->query($sql)->fetchAll();
    }

    /**
     * Récupère les organisations avec leur montant total calculé
     */
    public function getOrganisationsWithMontant() {
        $sql = "SELECT o.*, 
                (SELECT COALESCE(SUM(d.montant), 0) FROM don d WHERE d.organisationId = o.id) as montant_total
                FROM organisation o 
                ORDER BY o.nom";
        $db = config::getConnexion();
        return $db->query($sql)->fetchAll();
    }

    /**
     * Récupère le montant total d'une organisation spécifique
     */
    public function getMontantOrganisation(int $organisationId) {
        $sql = "SELECT COALESCE(SUM(montant), 0) as total FROM don WHERE organisationId = :organisationId";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $q->execute([':organisationId' => $organisationId]);
        $result = $q->fetch();
        return $result ? (float)$result['total'] : 0.0;
    }

    /**
     * Validation des données de l'organisation
     */
    public function validateOrganisation(Organisation $org) {
        $errors = [];
        
        // Validation du nom
        $nom = trim($org->getNom());
        if (empty($nom)) {
            $errors[] = "Le nom de l'organisation est obligatoire";
        } else if (strlen($nom) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères";
        } else if (strlen($nom) > 100) {
            $errors[] = "Le nom ne peut pas dépasser 100 caractères";
        }
        
        // Validation de la description
        $description = trim($org->getDescription());
        if (empty($description)) {
            $errors[] = "La description est obligatoire";
        } else if (strlen($description) < 10) {
            $errors[] = "La description doit contenir au moins 10 caractères";
        } else if (strlen($description) > 500) {
            $errors[] = "La description ne peut pas dépasser 500 caractères";
        }
        
        return $errors;
    }
}
?>