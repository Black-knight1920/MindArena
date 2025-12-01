<?php
require_once __DIR__."/../config.php";
require_once __DIR__."/../Model/Don.php";
require_once __DIR__."/../Model/DonateurClasse.php";

class DonController {
    
    public function addDon(Don $don) {
        //validation avant insertion
        $validationErrors = $this->validateDon($don);
        if (!empty($validationErrors)) {
            echo "Erreur : données du don invalides.";
            return false;
        }
        
        $sql = "INSERT INTO don (montant, dateDon, typeDon, organisationId, nom_donateur, prenom_donateur) 
                VALUES (:montant, :dateDon, :typeDon, :organisationId, :nomDonateur, :prenomDonateur)";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $result = $q->execute([
            ':montant' => $don->getMontant(),
            ':dateDon' => $don->getDateDon()->format('Y-m-d'),
            ':typeDon' => $don->getTypeDon(),
            ':organisationId' => $don->getOrganisationId(),
            ':nomDonateur' => $don->getNomDonateur(),
            ':prenomDonateur' => $don->getPrenomDonateur()
        ]);
        
        // Mise à jour automatique du montant total ET des classes donateurs
        if ($result) {
            $this->updateMontantOrganisation($don->getOrganisationId());
            $this->mettreAJourClasseDonateur($don);
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
        try {
            // Récupérer l'organisation avant suppression
            $don = $this->getDon($id);
            
            if (!$don) {
                error_log("Don non trouvé avec l'ID: " . $id);
                return false;
            }
            
            $organisationId = $don['organisationId'];
            $nomDonateur = $don['nom_donateur'];
            $prenomDonateur = $don['prenom_donateur'];
            
            $sql = "DELETE FROM don WHERE id = :id";
            $db = config::getConnexion();
            $q = $db->prepare($sql);
            $result = $q->execute([':id' => $id]);
            
            // Mise à jour automatique du montant total ET des classes donateurs
            if ($result) {
                $this->updateMontantOrganisation($organisationId);
                
                // Mettre à jour la classe du donateur après suppression
                if (!empty($nomDonateur) && !empty($prenomDonateur)) {
                    $this->mettreAJourClasseDonateurApresSuppression($nomDonateur, $prenomDonateur);
                }
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Erreur deleteDon: " . $e->getMessage());
            return false;
        }
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
        
        // Validation du nom du donateur (optionnel mais avec limites si fourni)
        $nomDonateur = $don->getNomDonateur();
        if ($nomDonateur && strlen($nomDonateur) > 100) {
            $errors[] = "Le nom du donateur ne peut pas dépasser 100 caractères";
        }
        
        // Validation du prénom du donateur (optionnel mais avec limites si fourni)
        $prenomDonateur = $don->getPrenomDonateur();
        if ($prenomDonateur && strlen($prenomDonateur) > 100) {
            $errors[] = "Le prénom du donateur ne peut pas dépasser 100 caractères";
        }
        
        return $errors;
    }

    // ========== MÉTHODES POUR LE SYSTÈME DE CLASSES ==========

    public function getClassementDonateurs($limit = 10) {
        try {
            $sql = "SELECT nom, prenom, total_dons, classe 
                    FROM donateurs 
                    WHERE total_dons > 0 
                    ORDER BY total_dons DESC 
                    LIMIT :limit";
            
            $db = config::getConnexion();
            $q = $db->prepare($sql);
            $q->bindValue(':limit', $limit, PDO::PARAM_INT);
            $q->execute();
            
            return $q->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur getClassementDonateurs: " . $e->getMessage());
            return [];
        }
    }

    public function getInfosDonateur($nomDonateur, $prenomDonateur) {
        try {
            $sql = "SELECT * FROM donateurs 
                    WHERE nom = :nom AND prenom = :prenom";
            
            $db = config::getConnexion();
            $q = $db->prepare($sql);
            $q->execute([
                ':nom' => $nomDonateur,
                ':prenom' => $prenomDonateur
            ]);
            
            $donateur = $q->fetch();
            
            if ($donateur) {
                // Ajouter les infos de classe
                $infosClasse = DonateurClasse::getInfosClasse($donateur['classe']);
                $donateur['badge'] = $infosClasse['badge'];
                $donateur['couleur'] = $infosClasse['couleur'];
                return $donateur;
            } else {
                // Retourner des infos par défaut si le donateur n'existe pas encore
                $totalDons = $this->getTotalDonsByDonateur($nomDonateur, $prenomDonateur);
                $classe = DonateurClasse::getClasse($totalDons);
                $infosClasse = DonateurClasse::getInfosClasse($classe);
                
                return [
                    'nom' => $nomDonateur,
                    'prenom' => $prenomDonateur,
                    'total_dons' => $totalDons,
                    'classe' => $classe,
                    'badge' => $infosClasse['badge'],
                    'couleur' => $infosClasse['couleur']
                ];
            }
            
        } catch (PDOException $e) {
            // En cas d'erreur, retourner des infos basiques
            error_log("Erreur getInfosDonateur: " . $e->getMessage());
            return [
                'nom' => $nomDonateur,
                'prenom' => $prenomDonateur,
                'total_dons' => 0,
                'classe' => 'Novice',
                'badge' => '🟢',
                'couleur' => '#00FF00'
            ];
        }
    }

    /**
     * Récupère le total des dons d'un donateur
     */
    public function getTotalDonsByDonateur($nomDonateur, $prenomDonateur) {
        try {
            $sql = "SELECT COALESCE(SUM(montant), 0) as total_dons 
                    FROM don 
                    WHERE nom_donateur = :nomDonateur 
                    AND prenom_donateur = :prenomDonateur";
            
            $db = config::getConnexion();
            $q = $db->prepare($sql);
            $q->execute([
                ':nomDonateur' => $nomDonateur,
                ':prenomDonateur' => $prenomDonateur
            ]);
            
            $result = $q->fetch();
            return $result['total_dons'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erreur getTotalDonsByDonateur: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Met à jour la classe du donateur après un don
     */
    private function mettreAJourClasseDonateur(Don $don) {
        $nomDonateur = $don->getNomDonateur();
        $prenomDonateur = $don->getPrenomDonateur();
        
        // Si pas de nom/prénom, on ne fait rien
        if (empty($nomDonateur) || empty($prenomDonateur)) {
            return;
        }
        
        // Calculer le total des dons pour ce donateur
        $totalDons = $this->getTotalDonsByDonateur($nomDonateur, $prenomDonateur);
        
        // Déterminer la nouvelle classe
        $nouvelleClasse = DonateurClasse::getClasse($totalDons);
        
        // Mettre à jour ou insérer dans la table donateurs
        $this->mettreAJourDonateur($nomDonateur, $prenomDonateur, $totalDons, $nouvelleClasse);
    }
    
    /**
     * Met à jour après suppression - CORRIGÉE
     */
    private function mettreAJourClasseDonateurApresSuppression($nomDonateur, $prenomDonateur) {
        if (empty($nomDonateur) || empty($prenomDonateur)) {
            return;
        }
        
        // Calculer le nouveau total des dons pour ce donateur
        $totalDons = $this->getTotalDonsByDonateur($nomDonateur, $prenomDonateur);
        $nouvelleClasse = DonateurClasse::getClasse($totalDons);
        
        // Debug log
        error_log("Mise à jour donateur après suppression - " . $prenomDonateur . " " . $nomDonateur . ": Total=" . $totalDons . ", Classe=" . $nouvelleClasse);
        
        // Mettre à jour ou supprimer le donateur si total = 0
        if ($totalDons > 0) {
            $this->mettreAJourDonateur($nomDonateur, $prenomDonateur, $totalDons, $nouvelleClasse);
        } else {
            // Si le donateur n'a plus de dons, le supprimer de la table donateurs
            $this->supprimerDonateur($nomDonateur, $prenomDonateur);
            error_log("Donateur supprimé (total=0): " . $prenomDonateur . " " . $nomDonateur);
        }
    }
    
    /**
     * NOUVELLE MÉTHODE : Supprime un donateur de la table donateurs
     */
    private function supprimerDonateur($nom, $prenom) {
        try {
            $sql = "DELETE FROM donateurs WHERE nom = :nom AND prenom = :prenom";
            $db = config::getConnexion();
            $q = $db->prepare($sql);
            $result = $q->execute([
                ':nom' => $nom,
                ':prenom' => $prenom
            ]);
            
            if ($result) {
                error_log("Donateur supprimé avec succès: " . $prenom . " " . $nom);
            } else {
                error_log("Échec suppression donateur: " . $prenom . " " . $nom);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur supprimerDonateur: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour ou crée un donateur
     */
    private function mettreAJourDonateur($nom, $prenom, $totalDons, $classe) {
        try {
            // Vérifier si le donateur existe
            $sqlCheck = "SELECT id FROM donateurs WHERE nom = :nom AND prenom = :prenom";
            $db = config::getConnexion();
            $qCheck = $db->prepare($sqlCheck);
            $qCheck->execute([':nom' => $nom, ':prenom' => $prenom]);
            $donateurExiste = $qCheck->fetch();
            
            if ($donateurExiste) {
                // Mettre à jour le donateur existant
                $sql = "UPDATE donateurs 
                        SET total_dons = :totalDons, classe = :classe 
                        WHERE nom = :nom AND prenom = :prenom";
            } else {
                // Créer un nouveau donateur
                $sql = "INSERT INTO donateurs (nom, prenom, total_dons, classe) 
                        VALUES (:nom, :prenom, :totalDons, :classe)";
            }
            
            $q = $db->prepare($sql);
            return $q->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':totalDons' => $totalDons,
                ':classe' => $classe
            ]);
            
        } catch (PDOException $e) {
            error_log("Erreur mettreAJourDonateur: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère l'historique des dons d'un donateur
     */
    public function getHistoriqueDonsDonateur($nom, $prenom) {
        try {
            $sql = "SELECT d.*, o.nom as organisation_nom 
                    FROM don d 
                    LEFT JOIN organisation o ON d.organisationId = o.id 
                    WHERE d.nom_donateur = :nom 
                    AND d.prenom_donateur = :prenom 
                    ORDER BY d.dateDon DESC 
                    LIMIT 10";
            
            $db = config::getConnexion();
            $q = $db->prepare($sql);
            $q->execute([
                ':nom' => $nom,
                ':prenom' => $prenom
            ]);
            
            return $q->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur getHistoriqueDonsDonateur: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Synchronise les donateurs depuis la table don
     */
    public function synchroniserDonateurs() {
        try {
            $sql = "SELECT DISTINCT nom_donateur as nom, prenom_donateur as prenom 
                    FROM don 
                    WHERE nom_donateur IS NOT NULL 
                    AND prenom_donateur IS NOT NULL 
                    AND nom_donateur != '' 
                    AND prenom_donateur != ''";
            
            $db = config::getConnexion();
            $donateurs = $db->query($sql)->fetchAll();
            
            $compteur = 0;
            foreach ($donateurs as $donateur) {
                $totalDons = $this->getTotalDonsByDonateur($donateur['nom'], $donateur['prenom']);
                $classe = DonateurClasse::getClasse($totalDons);
                
                $this->mettreAJourDonateur($donateur['nom'], $donateur['prenom'], $totalDons, $classe);
                $compteur++;
            }
            
            return $compteur . " donateurs synchronisés";
            
        } catch (PDOException $e) {
            return "Erreur synchronisation: " . $e->getMessage();
        }
    }

    /**
     * NOUVELLE MÉTHODE : Synchronise tous les donateurs (méthode de secours)
     */
    public function resynchroniserTousLesDonateurs() {
        try {
            // Récupérer tous les donateurs distincts de la table don
            $sql = "SELECT DISTINCT nom_donateur as nom, prenom_donateur as prenom 
                    FROM don 
                    WHERE nom_donateur IS NOT NULL 
                    AND prenom_donateur IS NOT NULL 
                    AND nom_donateur != '' 
                    AND prenom_donateur != ''";
            
            $db = config::getConnexion();
            $donateurs = $db->query($sql)->fetchAll();
            
            $compteur = 0;
            foreach ($donateurs as $donateur) {
                $totalDons = $this->getTotalDonsByDonateur($donateur['nom'], $donateur['prenom']);
                $classe = DonateurClasse::getClasse($totalDons);
                
                if ($totalDons > 0) {
                    $this->mettreAJourDonateur($donateur['nom'], $donateur['prenom'], $totalDons, $classe);
                    $compteur++;
                } else {
                    // Supprimer les donateurs sans dons
                    $this->supprimerDonateur($donateur['nom'], $donateur['prenom']);
                }
            }
            
            // Supprimer aussi les donateurs qui n'ont plus de dons dans la table don
            $sqlSuppression = "DELETE FROM donateurs 
                              WHERE (nom, prenom) NOT IN (
                                  SELECT DISTINCT nom_donateur, prenom_donateur 
                                  FROM don 
                                  WHERE nom_donateur IS NOT NULL 
                                  AND prenom_donateur IS NOT NULL 
                                  AND nom_donateur != '' 
                                  AND prenom_donateur != ''
                              )";
            $db->exec($sqlSuppression);
            
            return $compteur . " donateurs resynchronisés";
            
        } catch (PDOException $e) {
            return "Erreur resynchronisation: " . $e->getMessage();
        }
    }
}
?>