<?php
require_once __DIR__."/../../../Controller/DonController.php";

$donCtrl = new DonController();
$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        $donCtrl->deleteDon($id);
    } catch (Exception $e) {
        // Gérer l'erreur si nécessaire
    }
}

header("Location: donList.php");
exit;
?>