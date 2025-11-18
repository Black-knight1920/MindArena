<?php
require_once __DIR__."/../../../Controller/DonController.php";

$donCtrl = new DonController();
$id = $_GET['id'] ?? 0;

if ($id) {
    $donCtrl->deleteDon($id);
}

header("Location: donList.php");
exit;
?>