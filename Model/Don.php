<?php
class Don {
    private ?int $id;
    private float $montant;
    private DateTime $dateDon;
    private string $typeDon;
    private int $organisationId;
    private ?string $organisationNom;
    private ?string $nomDonateur;
    private ?string $prenomDonateur;

    public function __construct(
        ?int $id, 
        float $montant, 
        DateTime $dateDon, 
        string $typeDon, 
        int $organisationId, 
        ?string $organisationNom = null,
        ?string $nomDonateur = null,
        ?string $prenomDonateur = null
    ) {
        $this->id = $id;
        $this->montant = $montant;
        $this->dateDon = $dateDon;
        $this->typeDon = $typeDon;
        $this->organisationId = $organisationId;
        $this->organisationNom = $organisationNom;
        $this->nomDonateur = $nomDonateur;
        $this->prenomDonateur = $prenomDonateur;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getMontant(): float { return $this->montant; }
    public function getDateDon(): DateTime { return $this->dateDon; }
    public function getTypeDon(): string { return $this->typeDon; }
    public function getOrganisationId(): int { return $this->organisationId; }
    public function getOrganisationNom(): ?string { return $this->organisationNom; }
    public function getNomDonateur(): ?string { return $this->nomDonateur; }
    public function getPrenomDonateur(): ?string { return $this->prenomDonateur; }

    // Setters
    public function setMontant(float $montant): void { $this->montant = $montant; }
    public function setDateDon(DateTime $dateDon): void { $this->dateDon = $dateDon; }
    public function setTypeDon(string $typeDon): void { $this->typeDon = $typeDon; }
    public function setOrganisationId(int $organisationId): void { $this->organisationId = $organisationId; }
    public function setNomDonateur(?string $nomDonateur): void { $this->nomDonateur = $nomDonateur; }
    public function setPrenomDonateur(?string $prenomDonateur): void { $this->prenomDonateur = $prenomDonateur; }
}
?>