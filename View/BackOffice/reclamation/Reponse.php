<?php
class Reponse {
    private ?int $id;
    private int $reclamation_id;
    private string $message;
    private ?string $created_by;

    public function __construct(
        ?int $id,
        int $reclamation_id,
        string $message,
        ?string $created_by = 'admin'
    ) {
        $this->id = $id;
        $this->reclamation_id = $reclamation_id;
        $this->message = $message;
        $this->created_by = $created_by;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getReclamationId(): int { return $this->reclamation_id; }
    public function getMessage(): string { return $this->message; }
    public function getCreatedBy(): ?string { return $this->created_by; }

    // Setters
    public function setReclamationId(int $reclamation_id): void { $this->reclamation_id = $reclamation_id; }
    public function setMessage(string $message): void { $this->message = $message; }
    public function setCreatedBy(?string $created_by): void { $this->created_by = $created_by; }
}
?>
