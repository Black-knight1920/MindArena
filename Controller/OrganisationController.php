<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/../Model/Organisation.php";

class OrganisationController {
    
    public function addOrganisation(Organisation $org) {
        $validationErrors = $this->validateOrganisation($org);
        if (!empty($validationErrors)) {
            echo "Erreur : organisation invalide.";
            return false;
        }

        $sql = "INSERT INTO organisation (nom, description, website_url) 
                VALUES (:nom, :description, :website_url)";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $result = $q->execute([
            ':nom' => $org->getNom(),
            ':description' => $org->getDescription(),
            ':website_url' => $org->getWebsiteUrl()
        ]);
        
        if (!$result) {
            echo "Erreur : impossible d'ajouter l'organisation.";
            return false;
        }
        return true;
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
        $validationErrors = $this->validateOrganisation($org);
        if (!empty($validationErrors)) {
            echo "Erreur : organisation invalide.";
            return false;
        }
        
        $sql = "UPDATE organisation SET 
                nom = :nom, description = :description, website_url = :website_url
                WHERE id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        return $q->execute([
            ':id' => $id,
            ':nom' => $org->getNom(),
            ':description' => $org->getDescription(),
            ':website_url' => $org->getWebsiteUrl()
        ]);
    }

    public function deleteOrganisation(int $id) {
        $sql = "DELETE FROM organisation WHERE id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        return $q->execute([':id' => $id]);
    }

    //Récupère le montant total d'une organisation spécifique
    public function getMontantOrganisation(int $organisationId) {
        $sql = "SELECT COALESCE(SUM(montant), 0) as total FROM don WHERE organisationId = :organisationId";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $q->execute([':organisationId' => $organisationId]);
        $result = $q->fetch();
        return $result ? (float)$result['total'] : 0.0;
    }

    //Validation des données de l'organisation
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
        
        // Validation de l'URL (optionnelle mais doit être valide si fournie)
        $websiteUrl = trim($org->getWebsiteUrl() ?? '');
        if (!empty($websiteUrl)) {
            if (!filter_var($websiteUrl, FILTER_VALIDATE_URL)) {
                $errors[] = "L'URL du site web n'est pas valide";
            } else if (strlen($websiteUrl) > 255) {
                $errors[] = "L'URL ne peut pas dépasser 255 caractères";
            }
        }
        
        return $errors;
    }
}
?>