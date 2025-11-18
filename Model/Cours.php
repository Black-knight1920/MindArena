<?php
class Cours {
    private ?int $id;
    private ?string $titre;
    private ?string $description;
    private ?int $duree;
    private ?string $niveau;
    private ?string $formateur;

    //Constructor
    public function __construct(?int $id, ?string $titre, ?string $description, ?int $duree, ?string $niveau, ?string $formateur) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->duree = $duree;
        $this->niveau = $niveau;
        $this->formateur = $formateur;
    }

    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Titre</th><th>Description</th><th>Durée</th><th>Niveau</th><th>Formateur</th></tr>";
        echo "<tr>";
        echo "<td>{$this->id}</td>";
        echo "<td>{$this->titre}</td>";
        echo "<td>{$this->description}</td>";
        echo "<td>{$this->duree}</td>";
        echo "<td>{$this->niveau}</td>";
        echo "<td>{$this->formateur}</td>";
        echo "</tr>";
        echo "</table>";
    }

    // Getters and Setters
    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getTitre(): ?string {
        return $this->titre;
    }

    public function setTitre(?string $titre): void {
        $this->titre = $titre;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getDuree(): ?int {
        return $this->duree;
    }

    public function setDuree(?int $duree): void {
        $this->duree = $duree;
    }

    public function getNiveau(): ?string {
        return $this->niveau;
    }

    public function setNiveau(?string $niveau): void {
        $this->niveau = $niveau;
    }

    public function getFormateur(): ?string {
        return $this->formateur;
    }

    public function setFormateur(?string $formateur): void {
        $this->formateur = $formateur;
    }
}
?>

