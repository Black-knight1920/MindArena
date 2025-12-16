<?php
require_once __DIR__."/../../../Controller/ReclamationController.php";

$orgCtrl = new ReclamationController();
$id = $_GET['id'] ?? 0;

if ($id) {
    $orgCtrl->deleteReclamation($id);
}

header("Location: reclamationList.php");
exit;
?>