<?php
class Jeu {
    private $db;
    private $table = 'jeux';

    public $id;
    public $titre;
    public $description;
    public $categorie_id;
    public $image;
    public $lien_jeu;
    public $date_ajout;
    public $statut;

    public function __construct($db) {
        $this->db = $db;
    }

    public function ajouter() {
        $query = "INSERT INTO " . $this->table . " 
                 SET titre=:titre, description=:description, categorie_id=:categorie_id, 
                     image=:image, lien_jeu=:lien_jeu, statut='actif', date_ajout=NOW()";
        
        $stmt = $this->db->prepare($query);
        
        $this->titre = htmlspecialchars(strip_tags($this->titre));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->categorie_id = htmlspecialchars(strip_tags($this->categorie_id));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->lien_jeu = htmlspecialchars(strip_tags($this->lien_jeu));
        
        $stmt->bindParam(":titre", $this->titre);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":categorie_id", $this->categorie_id);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":lien_jeu", $this->lien_jeu);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function lireActifs() {
        $query = "SELECT j.*, c.nom as categorie_nom 
                 FROM " . $this->table . " j 
                 LEFT JOIN categories c ON j.categorie_id = c.id 
                 WHERE j.statut = 'actif' 
                 ORDER BY j.date_ajout DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function lireTous() {
        $query = "SELECT j.*, c.nom as categorie_nom 
                 FROM " . $this->table . " j 
                 LEFT JOIN categories c ON j.categorie_id = c.id 
                 ORDER BY j.date_ajout DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
}
?>