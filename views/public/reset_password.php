<?php
require_once __DIR__ . '/../../config/bootstrap.php';
// Redirect to the unified reset page (expects ?token=...)
$token = isset($_GET['token']) ? $_GET['token'] : '';
$url = BASE_URL . '/index.php?action=reset';
if ($token !== '') {
    $url .= '&token=' . urlencode($token);
}
header('Location: ' . $url);
exit;

