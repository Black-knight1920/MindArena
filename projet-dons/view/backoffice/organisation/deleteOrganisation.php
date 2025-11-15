<?php
require_once __DIR__."/../../../Controller/OrganisationController.php";

$orgCtrl = new OrganisationController();
$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        $orgCtrl->deleteOrganisation($id);
    } catch (Exception $e) {
        // Gérer l'erreur si nécessaire
    }
}

header("Location: organisationList.php");
exit;
?>