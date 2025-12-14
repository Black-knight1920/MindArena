<?php
// Empêcher tout affichage HTML
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Répondre aux preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    exit;
}

session_start();

// Configuration Stripe
// IMPORTANT: Remplacer avec votre clé secrète Stripe
define('STRIPE_SECRET_KEY', 'sk_test_xxxxxxxxxxxxxxxxxxxxxxxx

// Vérifier la méthode de requête
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['montant']) || !isset($input['devise'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

$montant = floatval($input['montant']);
$devise = strtoupper($input['devise']);

// Valider le montant
if ($montant <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Montant invalide']);
    exit;
}

// Convertir le montant en centimes pour Stripe (EUR/USD)
$montantCentimes = intval($montant * 100);

try {
    // Vérifier que Stripe est installé
    $composerPath = __DIR__ . '/../../vendor/autoload.php';
    if (!file_exists($composerPath)) {
        // Stripe n'est pas installé - retourner un mode simulation
        // Retourner un statut qui indique qu'il faut soumettre directement
        echo json_encode([
            'clientSecret' => null,
            'paymentIntentId' => null,
            'mode' => 'simulation',
            'skipPayment' => true,
            'message' => 'Mode simulation - Stripe non installé. Formulaire soumis directement.'
        ]);
        exit;
    }
    
    // Inclure la bibliothèque Stripe
    require_once $composerPath;
    
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    
    // Créer un PaymentIntent
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $montantCentimes,
        'currency' => $devise === 'EUR' ? 'eur' : 'usd',
        'payment_method_types' => ['card'],
        'metadata' => [
            'montant_display' => $montant,
            'devise' => $devise,
            'timestamp' => time()
        ]
    ]);
    
    // Retourner le clientSecret
    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret,
        'paymentIntentId' => $paymentIntent->id,
        'mode' => 'production',
        'skipPayment' => false
    ]);
    
} catch (\Stripe\Exception\ApiErrorException $e) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Erreur Stripe: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}

// Nettoyer et envoyer
ob_end_flush();
?>
?>
