<?php

require_once __DIR__ . '/../Models/PasswordReset.php';
require_once __DIR__ . '/../Models/Account.php';

class PasswordResetController
{
    private PasswordReset $resetModel;
    private Account $accountModel;

    public function __construct(PDO $pdo)
    {
        $this->resetModel   = new PasswordReset($pdo);
        $this->accountModel = new Account($pdo);
    }

    /**
     * Creates a reset token for the given username/email.
     * Returns ['success'=>bool, 'token'=>string|null, 'error'=>string|null, 'expires'=>DateTimeInterface|null]
     */
    public function request(string $identifier): array
    {
        $account = $this->accountModel->findByIdentifier($identifier);
        if (!$account) {
            return [
                'success' => false,
                'token'   => null,
                'error'   => 'Utilisateur introuvable.',
                'expires' => null,
            ];
        }

        $token   = bin2hex(random_bytes(16));
        $expires = new DateTimeImmutable('+1 hour');

        $this->resetModel->create($account['name'], $token, $expires);

        return [
            'success' => true,
            'token'   => $token,
            'error'   => null,
            'expires' => $expires,
        ];
    }

    /**
     * Resets password using the token.
     */
    public function reset(string $token, string $newPassword): array
    {
        $row = $this->resetModel->findByToken($token);
        if (!$row) {
            return [
                'success' => false,
                'error'   => 'Token invalide.',
            ];
        }

        $expiresAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['expire']);
        if ($expiresAt && $expiresAt < new DateTimeImmutable()) {
            $this->resetModel->deleteByToken($token);
            return [
                'success' => false,
                'error'   => 'Token expirA.',
            ];
        }

        $account = $this->accountModel->findByIdentifier($row['namee']);
        if (!$account) {
            $this->resetModel->deleteByToken($token);
            return [
                'success' => false,
                'error'   => 'Utilisateur introuvable.',
            ];
        }

        $this->accountModel->updatePassword((int) $account['id'], $newPassword);
        $this->resetModel->deleteByToken($token);

        return [
            'success' => true,
            'error'   => null,
        ];
    }
}
