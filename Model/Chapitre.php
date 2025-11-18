<?php
class Chapitre {
    private ?int $id;
    private ?string $titre;
    private ?string $description;
    private ?int $ordre;
    private ?int $cours_id;

    //Constructor
    public function __construct(?int $id, ?string $titre, ?string $description, ?int $ordre, ?int $cours_id) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->ordre = $ordre;
        $this->cours_id = $cours_id;
    }

    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Titre</th><th>Description</th><th>Ordre</th><th>Cours ID</th></tr>";
        echo "<tr>";
        echo "<td>{$this->id}</td>";
        echo "<td>{$this->titre}</td>";
        echo "<td>{$this->description}</td>";
        echo "<td>{$this->ordre}</td>";
        echo "<td>{$this->cours_id}</td>";
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

    public function getOrdre(): ?int {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): void {
        $this->ordre = $ordre;
    }

    public function getCoursId(): ?int {
        return $this->cours_id;
    }

    public function setCoursId(?int $cours_id): void {
        $this->cours_id = $cours_id;
    }
}
?>

