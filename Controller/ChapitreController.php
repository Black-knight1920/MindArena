<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../Model/Chapitre.php');

class ChapitreController {

    public function listChapitres() {
        $sql = "SELECT c.*, co.titre as cours_titre FROM chapitre c LEFT JOIN cours co ON c.cours_id = co.id ORDER BY c.cours_id, c.ordre";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteChapitre($id) {
        $sql = "DELETE FROM chapitre WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addChapitre(Chapitre $chapitre) {
        $sql = "INSERT INTO chapitre VALUES (NULL, :titre, :description, :ordre, :cours_id)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $chapitre->getTitre(),
                'description' => $chapitre->getDescription(),
                'ordre' => $chapitre->getOrdre(),
                'cours_id' => $chapitre->getCoursId()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateChapitre(Chapitre $chapitre, $id) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE chapitre SET 
                    titre = :titre,
                    description = :description,
                    ordre = :ordre,
                    cours_id = :cours_id
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'titre' => $chapitre->getTitre(),
                'description' => $chapitre->getDescription(),
                'ordre' => $chapitre->getOrdre(),
                'cours_id' => $chapitre->getCoursId()
            ]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function showChapitre($id) {
        $sql="SELECT * FROM chapitre WHERE id= $id";
        $db= config::getConnexion();
        $query= $db->prepare($sql);

        try
        {
            $query->execute();
            $chapitre= $query->fetch();
            return $chapitre;
        }
        catch(Exception $e)
        {
            die('Error: '. $e->getMessage());
        }
    }

    public function listCours() {
        $sql = "SELECT id, titre FROM cours";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getChapitresByCoursId($cours_id) {
        $sql = "SELECT * FROM chapitre WHERE cours_id = :cours_id ORDER BY ordre";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['cours_id' => $cours_id]);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }
   
}
?>

