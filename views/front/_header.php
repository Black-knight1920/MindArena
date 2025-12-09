<?php
// Header commun pour tout le front (statique)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? 'home';
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';

$isLoggedIn = isset($_SESSION['user']);
$username   = $isLoggedIn ? ($_SESSION['user']['username'] ?? '') : '';
$role       = $isLoggedIn ? ($_SESSION['user']['role'] ?? '') : '';

function is_active_action(string $action, array $targets): string {
    return in_array($action, $targets, true) ? 'active' : '';
}
?>
<header class="header-section" style="background:#020617;">
    <div class="header-warp">
        <div class="header-bar-warp">

            <a href="<?= $BASE ?>/index.php?action=home" class="site-logo mindarena-logo">
                <img src="<?= $BASE ?>/ENDGAME/img/logo.jpg" alt="MindArena" style="max-height: 60px; width: auto;" onerror="this.src='<?= $BASE ?>/endgame/img/logo.png'; this.onerror=null;">
            </a>

            <nav class="top-nav-area w-100">
                <ul class="main-menu primary-menu">
                    <li>
                        <a href="<?= $BASE ?>/index.php?action=home"
                           class="<?= is_active_action($action, ['home']) ?>">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="<?= $BASE ?>/index.php?action=forums"
                           class="<?= is_active_action($action, ['forums','publications']) ?>">
                            Forums
                        </a>
                    </li>
                    <li>
                        <a href="<?= $isLoggedIn ? $BASE . '/index.php?action=add-forum' : $BASE . '/index.php?action=login' ?>"
                           class="<?= is_active_action($action, ['add-forum']) ?>">
                            New Forum
                        </a>
                    </li>
                    <li>
                        <a href="<?= $BASE ?>/index.php?action=top-contributors"
                           class="<?= is_active_action($action, ['top-contributors']) ?>">
                            Top Contributors
                        </a>
                    </li>
                </ul>

                <ul class="main-menu primary-menu" style="margin-left:auto;">
                    <?php if ($isLoggedIn): ?>
                        <?php if ($role === 'admin'): ?>
                            <li style="position:relative;">
                                <a href="javascript:void(0);" class="<?= is_active_action($action, ['profile']) ?>">
                                    <?= htmlspecialchars($username) ?> (admin)
                                </a>
                                <ul class="sub-menu" style="position:absolute; right:0; background:#0b1224; padding:10px 0; border:1px solid rgba(255,255,255,0.08); min-width:180px;">
                                    <li><a href="<?= $BASE ?>/admin.php?action=dashboard">Dashboard admin</a></li>
                                    <li><a href="<?= $BASE ?>/admin.php?action=admin-add">Créer un admin</a></li>
                                    <li><a href="<?= $BASE ?>/logout.php">Déconnexion</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="<?= $BASE ?>/index.php?action=profile"
                                   class="<?= is_active_action($action, ['profile']) ?>">
                                    <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)
                                </a>
                            </li>
                            <li>
                                <a href="<?= $BASE ?>/logout.php">Déconnexion</a>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li>
                            <a href="<?= $BASE ?>/index.php?action=login">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

        </div>
    </div>
</header>
<?php @include __DIR__ . '/_flash.php'; ?>

