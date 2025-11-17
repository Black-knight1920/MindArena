<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/../Model/Don.php";

class DonController {
    
    public function addDon(Don $don) {
        //validation avant insertion
        $validationErrors = $this->validateDon($don);
        if (!empty($validationErrors)) {
            echo "Erreur : données du don invalides.";
            return false;
        }
        
        $sql = "INSERT INTO don (montant, dateDon, typeDon, organisationId) 
                VALUES (:montant, :dateDon, :typeDon, :organisationId)";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $result = $q->execute([
            ':montant' => $don->getMontant(),
            ':dateDon' => $don->getDateDon()->format('Y-m-d'),
            ':typeDon' => $don->getTypeDon(),
            ':organisationId' => $don->getOrganisationId()
        ]);
        
        // Mise à jour automatique du montant total
        if ($result) {
            $this->updateMontantOrganisation($don->getOrganisationId());
        }
        
        return $result;
    }

    public function listDon() {
        $sql = "SELECT d.*, o.nom as organisation_nom,
                (SELECT COALESCE(SUM(d2.montant), 0) 
                FROM don d2 
                WHERE d2.organisationId = o.id) as montant_total_organisation
                FROM don d 
                LEFT JOIN organisation o ON d.organisationId = o.id 
                ORDER BY d.dateDon DESC";
        $db = config::getConnexion();
        return $db->query($sql);
    }

    public function getDon(int $id) {
        $sql = "SELECT d.*, o.nom as organisation_nom,
                (SELECT COALESCE(SUM(d2.montant), 0) FROM don d2 WHERE d2.organisationId = o.id) as montant_total_organisation
                FROM don d 
                LEFT JOIN organisation o ON d.organisationId = o.id 
                WHERE d.id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $q->execute([':id' => $id]);
        return $q->fetch();
    }

    
    public function deleteDon(int $id) {
        // Récupérer l'organisation avant suppression
        $don = $this->getDon($id);
        $organisationId = $don['organisationId'];
        
        $sql = "DELETE FROM don WHERE id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $result = $q->execute([':id' => $id]);
        
        // Mise à jour automatique du montant total
        if ($result) {
            $this->updateMontantOrganisation($organisationId);
        }
        
        return $result;
    }

    public function getOrganisationsForSelect() {
        $sql = "SELECT o.*,
                (SELECT COALESCE(SUM(d.montant), 0) FROM don d WHERE d.organisationId = o.id) as montant_total
                FROM organisation o 
                ORDER BY o.nom";
        $db = config::getConnexion();
        return $db->query($sql)->fetchAll();
    }

   // Mise à jour du montant total d'une organisation
    public function updateMontantOrganisation(int $organisationId) {
        $sql = "UPDATE organisation 
                SET montant_total = (
                    SELECT COALESCE(SUM(montant), 0) 
                    FROM don 
                    WHERE organisationId = :organisationId
                )
                WHERE id = :organisationId";
        
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        return $q->execute([
            ':organisationId' => $organisationId
        ]);
    }

    
      //Validation des données du don

    public function validateDon(Don $don) {
        $errors = [];
        
        // Validation du montant
        $montant = $don->getMontant();
        if ($montant <= 0) {
            $errors[] = "Le montant doit être supérieur à 0€";
        }
        if ($montant > 1000000) {
            $errors[] = "Le montant ne peut pas dépasser 1,000,000€";
        }
        if (!is_numeric($montant)) {
            $errors[] = "Le montant doit être un nombre valide";
        }
        
        // Validation de la date
        $today = new DateTime();
        $dateDon = $don->getDateDon();
        if ($dateDon > $today) {
            $errors[] = "La date du don ne peut pas être dans le futur";
        }
        
        // Validation du type
        $allowedTypes = ['Monétaire', 'Matériel']; 
        if (!in_array($don->getTypeDon(), $allowedTypes)) {
            $errors[] = "Type de don invalide";
        }
        
        // Validation de l'organisation
        if ($don->getOrganisationId() <= 0) {
            $errors[] = "Organisation invalide";
        }
        
        return $errors;
    }
}
?>