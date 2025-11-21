<?php
class Organisation {
    private ?int $id;
    private string $nom;
    private string $description;
    private ?string $websiteUrl;

    public function __construct(
        ?int $id, 
        string $nom, 
        string $description,
        ?string $websiteUrl = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->websiteUrl = $websiteUrl;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getDescription(): string { return $this->description; }
    public function getWebsiteUrl(): ?string { return $this->websiteUrl; }

    // Setters
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setWebsiteUrl(?string $websiteUrl): void { $this->websiteUrl = $websiteUrl; }
}
?>