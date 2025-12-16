<?php
require_once __DIR__."/../../../Controller/ReponseController.php";

$repCtrl = new ReponseController();
$id = $_GET['id'] ?? 0;
$reclamation_id = $_GET['reclamation_id'] ?? 0;

if ($id) {
    $repCtrl->deleteReponse($id);
}

// Rediriger vers la vue de la réclamation
header("Location: viewReclamation.php?id=" . $reclamation_id);
exit;
?>

