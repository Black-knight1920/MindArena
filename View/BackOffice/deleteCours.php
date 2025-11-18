<?php
include_once(__DIR__ . '/../../Controller/CoursController.php');
include_once(__DIR__ . '/../../Controller/ChapitreController.php');

if (isset($_GET['id'])) {
    $coursC = new CoursController();
    $chapitreC = new ChapitreController();
    
    // Supprimer les chapitres associés (sera fait automatiquement par CASCADE dans la BD)
    // Puis supprimer le cours
    $coursC->deleteCours($_GET['id']);
    
    header('Location: coursList.php?success=delete');
    exit;
} else {
    header('Location: coursList.php');
    exit;
}
?>

