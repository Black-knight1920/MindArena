<?php
require_once __DIR__ . '/../../config/bootstrap.php';
// Redirige vers l'index principal à la racine publique
header('Location: ' . BASE_URL . '/index.php');
exit;
