<?php
class Organisation {
    private ?int $id;
    private string $nom;
    private string $description;

    public function __construct(
        ?int $id, 
        string $nom, 
        string $description
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getDescription(): string { return $this->description; }

    // Setters
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setDescription(string $description): void { $this->description = $description; }
}
?>