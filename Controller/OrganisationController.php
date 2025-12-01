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

        $sql = "INSERT INTO organisation (nom, description, website_url, image_url) 
                VALUES (:nom, :description, :website_url, :image_url)";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $result = $q->execute([
            ':nom' => $org->getNom(),
            ':description' => $org->getDescription(),
            ':website_url' => $org->getWebsiteUrl(),
            ':image_url' => $org->getImageUrl()
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
                nom = :nom, description = :description, website_url = :website_url, image_url = :image_url
                WHERE id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        return $q->execute([
            ':id' => $id,
            ':nom' => $org->getNom(),
            ':description' => $org->getDescription(),
            ':website_url' => $org->getWebsiteUrl(),
            ':image_url' => $org->getImageUrl()
        ]);
    }

    public function deleteOrganisation(int $id) {
        $sql = "DELETE FROM organisation WHERE id = :id";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        return $q->execute([':id' => $id]);
    }

    public function getMontantOrganisation(int $organisationId) {
        $sql = "SELECT COALESCE(SUM(montant), 0) as total FROM don WHERE organisationId = :organisationId";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $q->execute([':organisationId' => $organisationId]);
        $result = $q->fetch();
        return $result ? (float)$result['total'] : 0.0;
    }

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
        
        // Validation de l'image URL (optionnelle) - MODIFIÉ POUR ACCEPTER LES CHEMINS LOCAUX
        $imageUrl = trim($org->getImageUrl() ?? '');
        if (!empty($imageUrl)) {
            // Si c'est une URL complète (http://... ou https://...)
            if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                // C'est une URL valide, vérifier la longueur
                if (strlen($imageUrl) > 255) {
                    $errors[] = "L'URL de l'image ne peut pas dépasser 255 caractères";
                }
            } 
            // Sinon, vérifier si c'est un chemin local (commence par /)
            elseif (strpos($imageUrl, '/') === 0) {
                // C'est un chemin local, vérifier la longueur
                if (strlen($imageUrl) > 255) {
                    $errors[] = "Le chemin de l'image ne peut pas dépasser 255 caractères";
                }
                // Optionnel : vérifier l'extension
                $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                if (!empty($extension) && !in_array(strtolower($extension), $allowedExtensions)) {
                    $errors[] = "L'extension du fichier image n'est pas autorisée (formats: jpg, jpeg, png, gif, webp, svg)";
                }
            } else {
                // Ni URL valide, ni chemin local
                $errors[] = "Le chemin de l'image n'est pas valide. Doit être une URL complète (http://...) ou un chemin local commençant par /";
            }
        }
        
        return $errors;
    }
    
    // Méthode pour valider spécifiquement un chemin d'image
    public function isValidImagePath($path): bool {
        if (empty($path)) {
            return true;
        }
        
        // Si c'est une URL complète
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return true;
        }
        
        // Si c'est un chemin local (commence par /)
        if (strpos($path, '/') === 0) {
            // Vérifier l'extension si présente
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            if (empty($extension)) {
                return true; // Pas d'extension, on accepte
            }
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            return in_array(strtolower($extension), $allowedExtensions);
        }
        
        return false;
    }
}
?>