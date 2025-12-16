<?php
class Reclamation {
    private ?int $id;
    private string $nom;
    private string $email;
    private string $subject;
    private string $message;

    public function __construct(
        ?int $id,
        string $nom,
        string $email,
        string $subject,
        string $message,
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getEmail(): string { return $this->email; }
    public function getSubject(): string { return $this->subject; }
    public function getMessage(): string { return $this->message; }

    // Setters
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setSubject(string $subject): void { $this->subject = $subject; }
    public function setMessage(string $message): void { $this->message = $message; }
}
?>
