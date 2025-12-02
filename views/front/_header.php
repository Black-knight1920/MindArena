<?php
// Header commun pour tout le front

// Action actuelle (via index.php?action=xxx)
$action = $_GET['action'] ?? 'home';

// Base URL calculée automatiquement (ex: /mindarena_forum)
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';

// Pour l'état "actif" on vérifie l'action
function is_active_action(string $action, array $targets): string {
    return in_array($action, $targets, true) ? 'active' : '';
}
?>
<header class="header-section" style="background:#020617;">
    <div class="header-warp">
        <div class="header-bar-warp">

            <!-- LOGO = HOME -->
            <a href="<?= $BASE ?>/index.php?action=home" class="site-logo mindarena-logo">
                <img src="<?= $BASE ?>/ENDGAME/img/logo.jpg" alt="MindArena" style="max-height: 60px; width: auto;" onerror="this.src='<?= $BASE ?>/endgame/img/logo.png'; this.onerror=null;">
            </a>

            <nav class="top-nav-area w-100">
                <ul class="main-menu primary-menu">

                    <!-- HOME -->
                    <li>
                        <a href="<?= $BASE ?>/index.php?action=home"
                           class="<?= is_active_action($action, ['home']) ?>">
                            Home
                        </a>
                    </li>

                    <!-- FORUMS (actif aussi sur publications) -->
                    <li>
                        <a href="<?= $BASE ?>/index.php?action=forums"
                           class="<?= is_active_action($action, ['forums','publications']) ?>">
                            Forums
                        </a>
                    </li>

                    <!-- NEW FORUM -->
                    <li>
                        <a href="<?= $BASE ?>/index.php?action=add-forum"
                           class="<?= is_active_action($action, ['add-forum']) ?>">
                            New Forum
                        </a>
                    </li>

                    <!-- TOP CONTRIBUTORS -->
                    <li>
                        <a href="<?= $BASE ?>/index.php?action=top-contributors"
                           class="<?= is_active_action($action, ['top-contributors']) ?>">
                            Top Contributors
                        </a>
                    </li>
                </ul>
            </nav>

        </div>
    </div>
</header>
