<?php
require_once __DIR__."/../../../Controller/OrganisationController.php";

$orgCtrl = new OrganisationController();
$id = $_GET['id'] ?? 0;

if ($id) {
    $orgCtrl->deleteOrganisation($id);
}

header("Location: organisationList.php");
exit;
?>