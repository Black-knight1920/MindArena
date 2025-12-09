<?php
class LogoutController {
    public function logout() {
        session_start();
        session_destroy();
        header("Location: /mindarena_forum/index.php?action=login");
        exit();
    }
}
?>
