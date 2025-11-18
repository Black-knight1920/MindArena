<?php
require 'db.php';

$id = $_GET['id'];

$sql = "DELETE FROM reclamation WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

header("Location: ../admin/reclamations.php");
exit;
