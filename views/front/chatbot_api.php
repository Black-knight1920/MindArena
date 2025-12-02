<?php
// chatbot_api.php - IA via Hugging Face Router (OpenAI-style /v1/chat/completions)

header('Content-Type: application/json');

// Autoriser uniquement POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Méthode non autorisée (utilise POST)']);
    exit;
}

// Lire le JSON envoyé
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'JSON invalide']);
    exit;
}

$userMessage = isset($input['message']) ? trim($input['message']) : '';
$pageContext = isset($input['context']) ? trim($input['context']) : 'default';
$history     = isset($input['history']) && is_array($input['history']) ? $input['history'] : [];

// Validation et sanitization
if ($userMessage === '') {
    echo json_encode(['error' => 'Message vide']);
    exit;
}

// Limiter la longueur du message pour éviter les abus
if (mb_strlen($userMessage) > 2000) {
    echo json_encode(['error' => 'Message trop long (maximum 2000 caractères)']);
    exit;
}

// Sanitize user message
$userMessage = htmlspecialchars($userMessage, ENT_QUOTES, 'UTF-8');

// Charger la config AI
$configFile = __DIR__ . '/config/ai.php';
if (!file_exists($configFile)) {
    echo json_encode(['error' => 'Config AI manquante (config/ai.php introuvable)']);
    exit;
}
$config   = require $configFile;
$provider = $config['provider'] ?? 'huggingface';

if ($provider !== 'huggingface') {
    echo json_encode(['error' => 'Fournisseur AI non supporté: ' . $provider]);
    exit;
}

$hfToken = $config['hf_token'] ?? '';
$model   = $config['hf_model'] ?? '';

if (!$hfToken || !$model) {
    echo json_encode(['error' => 'Token Hugging Face ou modèle non configuré dans config/ai.php']);
    exit;
}

// Contexte de ton site pour guider le modèle
$systemPrompt = "Tu es MindArenaBot, un assistant IA avancé.

RÔLE :
- Tu peux répondre à TOUT type de question : programmation, mathématiques, culture générale, projets scolaires, organisation, etc.
- Quand la question concerne le site MindArena (forums, publications, signalements, dashboard), tu donnes des réponses adaptées au fonctionnement du site.
- Tu réponds en français, avec un ton simple, clair, utile et bienveillant.

FORMAT DE RÉPONSE (TRÈS IMPORTANT) :
- Tu peux réfléchir en interne, mais tu ne dois JAMAIS afficher ton raisonnement.
- Tu dois TOUJOURS renvoyer ta réponse au format suivant, et UNIQUEMENT ce format :

Réponse: [ta réponse finale pour l'utilisateur, sans expliquer ton raisonnement interne]

Ne renvoie JAMAIS de phrases comme « je vais… », « nous sommes sur la page… », « étape 1… » pour décrire ton raisonnement interne.
Ne parle jamais de tes pensées internes, renvoie seulement la phrase après 'Réponse:'.
";



$contextText = match ($pageContext) {
    'forums'           => "Page liste des forums (parcourir, créer, supprimer).",
    'forum_add'        => "Page de création de forum (titre + description).",
    'publications'     => "Page des publications d’un forum (afficher, supprimer, signaler).",
    'publication_add'  => "Page d’ajout de publication (auteur + contenu).",
    'report'           => "Page de signalement d’un contenu.",
    default            => "Contexte générique du front MindArena.",
};

// ===== Construction des messages avec historique =====
$messages = [
    [
        'role'    => 'system',
        'content' => $systemPrompt,
    ],
    [
        'role'    => 'system',
        'content' => "Contexte de la page : " . $contextText,
    ],
];

// On ajoute l'historique envoyé par le front (liste de {role, content})
if (is_array($history)) {
    foreach ($history as $msg) {
        if (!isset($msg['role'], $msg['content'])) {
            continue;
        }
        $role = $msg['role'];
        if (!in_array($role, ['user', 'assistant', 'system'], true)) {
            $role = 'user';
        }
        $messages[] = [
            'role'    => $role,
            'content' => $msg['content'],
        ];
    }
}

// On ajoute le nouveau message de l'utilisateur à la fin
$messages[] = [
    'role'    => 'user',
    'content' => $userMessage,
];

// Préparation de la requête au format OpenAI /chat/completions
$payload = [
    'model' => $model,
    'messages' => $messages,
    'max_tokens'  => 300,
    'temperature' => 0.6,
];

// Endpoint correct du router HF
$ch = curl_init('https://router.huggingface.co/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $hfToken,
    ],
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_TIMEOUT        => 30,
]);

$response = curl_exec($ch);

if ($response === false) {
    $err = curl_error($ch);
    curl_close($ch);
    echo json_encode(['error' => "Erreur cURL : $err"]);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

// Gestion d’erreur côté Hugging Face
if ($httpCode >= 400) {
    $msg = 'Erreur inconnue côté Hugging Face';
    if (is_array($data)) {
        if (isset($data['error'])) {
            $msg = is_string($data['error']) ? $data['error'] : json_encode($data['error']);
        } elseif (isset($data['message'])) {
            $msg = $data['message'];
        }
    }

    echo json_encode([
        'error' => "Erreur Hugging Face ($httpCode) : " . $msg,
    ]);
    exit;
}

// Format OpenAI-like : { choices: [ { message: { content: '...' } } ] }
$botReplyRaw = $data['choices'][0]['message']['content'] ?? null;

if (!$botReplyRaw) {
    echo json_encode([
        'error' => "Réponse inattendue de Hugging Face",
        'raw'   => $data,
    ]);
    exit;
}

/// On cherche la DERNIÈRE occurrence de "Réponse:" ou "Reponse:"
$text = trim($botReplyRaw);
$botReply = '';

$marker  = 'Réponse:';
$pos     = mb_strripos($text, $marker);

if ($pos === false) {
    $marker = 'Reponse:'; // au cas où le modèle n’utilise pas l’accent
    $pos    = mb_strripos($text, $marker);
}

if ($pos !== false) {
    // On prend tout ce qu'il y a APRÈS "Réponse:"
    $botReply = trim(mb_substr($text, $pos + mb_strlen($marker)));
} else {
    // Si le modèle n'a pas respecté le format, on garde tout le texte
    $botReply = $text;
}

// Sécurité : si vide, on renvoie quelque chose
if ($botReply === '') {
    $botReply = "Désolé, je n'ai pas réussi à formuler une réponse claire pour le moment.";
}

// 👉 Format final envoyé au front
$displayReply = 'Réponse=' . $botReply;

echo json_encode([
    'reply' => $displayReply
]);
