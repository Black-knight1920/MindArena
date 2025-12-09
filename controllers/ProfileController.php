<?php

require_once __DIR__ . '/../Models/Forum.php';
require_once __DIR__ . '/../Models/Publication.php';
require_once __DIR__ . '/../Models/Profile.php';
require_once __DIR__ . '/../Models/Account.php';
require_once __DIR__ . '/../Services/UserStatsService.php';

class ProfileController
{
    private Forum $forumModel;
    private Publication $publicationModel;
    private Profile $profileModel;
    private Account $accountModel;
    private UserStatsService $statsService;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo              = $pdo;
        $this->forumModel       = new Forum($pdo);
        $this->publicationModel = new Publication($pdo);
        $this->profileModel     = new Profile($pdo);
        $this->accountModel     = new Account($pdo);
        $this->statsService     = new UserStatsService($pdo);
    }

    private function getBaseUrl(): string
    {
        $BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return $BASE === '' ? '/' : $BASE;
    }

    public function show(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $BASE = $this->getBaseUrl();

        if (!isset($_SESSION['user'])) {
            header('Location: ' . $BASE . '/index.php?action=login');
            exit;
        }

        $userSession = $_SESSION['user'];
        $username    = $userSession['username'] ?? '';
        $userId      = isset($userSession['id']) ? (int) $userSession['id'] : null;

        $accountData = null;
        $profileData = null;

        if ($userId !== null) {
            $accountData = $this->accountModel->findById($userId);
            $profileData = $this->profileModel->getByUserId($userId);
        } elseif ($username !== '') {
            $accountData = $this->accountModel->findByIdentifier($username);
        }

        $forumsByUser = $username !== '' ? $this->forumModel->getByCreator($username, 50) : [];
        $pubsByUser   = $username !== '' ? $this->publicationModel->getByAuthorWithFullDetails($username, 50) : [];
        $userRank     = $username !== '' ? $this->statsService->getUserStatsWithRank($username) : null;
        $score        = $userRank['score'] ?? 0;
        $firstForumId = !empty($forumsByUser) ? (int)($forumsByUser[0]['id'] ?? 0) : null;

        // Render the existing view with prepared data
        include VIEW_PATH . '/front/profile.php';
    }
}
