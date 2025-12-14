<?php
class Organisation {
    private ?int $id;
    private string $nom;
    private string $description;
    private ?string $websiteUrl;
    private ?string $imageUrl;

    public function __construct(
        ?int $id, 
        string $nom, 
        string $description,
        ?string $websiteUrl = null,
        ?string $imageUrl = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->websiteUrl = $websiteUrl;
        $this->imageUrl = $imageUrl;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getDescription(): string { return $this->description; }
    public function getWebsiteUrl(): ?string { return $this->websiteUrl; }
    public function getImageUrl(): ?string { return $this->imageUrl; }

    // Setters
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setWebsiteUrl(?string $websiteUrl): void { $this->websiteUrl = $websiteUrl; }
    public function setImageUrl(?string $imageUrl): void { $this->imageUrl = $imageUrl; }
}
?>