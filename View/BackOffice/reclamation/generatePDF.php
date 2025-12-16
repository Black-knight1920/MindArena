<?php
// Inclure l'autoloader de Composer pour charger TCPDF
require_once __DIR__ . '/../../../vendor/autoload.php'; // Charge l'autoloader de Composer

// Inclure le contrôleur des réclamations
require_once __DIR__ . "/../../../Controller/ReclamationController.php";

// Récupérer l'ID de la réclamation depuis l'URL (ou utiliser 0 si non spécifié)
$id = $_GET['id'] ?? 0;

// Initialiser le contrôleur des réclamations
$recCtrl = new ReclamationController();
// Récupérer la réclamation par ID
$reclamation = $recCtrl->getReclamation($id);

// Vérifier si la réclamation existe
if (!$reclamation) {
    die('Réclamation non trouvée');
}

// Créer un objet TCPDF
$pdf = new TCPDF();
// Ajouter une page au PDF
$pdf->AddPage();
// Définir la police
$pdf->SetFont('helvetica', '', 12);

// Ajouter des informations de la réclamation dans le PDF
$pdf->Cell(0, 10, 'Réclamation ID: ' . $reclamation['id'], 0, 1);
$pdf->Cell(0, 10, 'Nom: ' . $reclamation['full_name'], 0, 1);
$pdf->Cell(0, 10, 'Email: ' . $reclamation['email'], 0, 1);
$pdf->Cell(0, 10, 'Sujet: ' . $reclamation['subject'], 0, 1);
// Utiliser MultiCell pour afficher le message (avec un retour à la ligne automatique)
$pdf->MultiCell(0, 10, 'Message: ' . $reclamation['message']);
// Afficher la date de création de la réclamation
$pdf->Cell(0, 10, 'Date de création: ' . date('d/m/Y à H:i', strtotime($reclamation['created_at'])), 0, 1);

// Sortie du PDF
// 'D' signifie que le PDF sera téléchargé, vous pouvez changer 'D' en 'I' pour afficher dans le navigateur
$pdf->Output('reclamation_' . $reclamation['id'] . '.pdf', 'D');
?>
