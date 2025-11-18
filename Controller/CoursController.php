<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../Model/Cours.php');

class CoursController {

    public function listCours() {
        $sql = "SELECT * FROM cours";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteCours($id) {
        $sql = "DELETE FROM cours WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addCours(Cours $cours) {
        $sql = "INSERT INTO cours VALUES (NULL, :titre, :description, :duree, :niveau, :formateur)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $cours->getTitre(),
                'description' => $cours->getDescription(),
                'duree' => $cours->getDuree(),
                'niveau' => $cours->getNiveau(),
                'formateur' => $cours->getFormateur()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateCours(Cours $cours, $id) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE cours SET 
                    titre = :titre,
                    description = :description,
                    duree = :duree,
                    niveau = :niveau,
                    formateur = :formateur
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'titre' => $cours->getTitre(),
                'description' => $cours->getDescription(),
                'duree' => $cours->getDuree(),
                'niveau' => $cours->getNiveau(),
                'formateur' => $cours->getFormateur()
            ]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function showCours($id) {
        $sql="SELECT * FROM cours WHERE id= $id";
        $db= config::getConnexion();
        $query= $db->prepare($sql);

        try
        {
            $query->execute();
            $cours= $query->fetch();
            return $cours;
        }
        catch(Exception $e)
        {
            die('Error: '. $e->getMessage());
        }
    }

    public function getLastInsertId() {
        $db = config::getConnexion();
        return $db->lastInsertId();
    }
   
}
?>

