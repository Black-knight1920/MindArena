<?php
// views/front/report.php
$targetType = $targetType ?? ($_GET['target_type'] ?? 'forum');
$targetId   = $targetId   ?? ($_GET['target_id']   ?? null);
$target     = $target     ?? null;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../services/Csrf.php';
$csrf = Csrf::token();

// Base URL calculée automatiquement
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Signaler un contenu - MindArena</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?= BASE_URL ?>/ENDGAME/img/favicon.ico" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/animate.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/style.css">

    <style>
        :root {
            --ma-bg: #0a0615;
            --ma-card: rgba(8, 22, 36, 0.98);
            --ma-primary: #ff4df0;
            --ma-secondary: #b01ba5;
            --ma-border: rgba(255,255,255,0.08);
            --ma-danger: #ff4b5c;
            --ma-warning: #ffca5f;
            --ma-text-soft: #d7d7ff;
            --ma-glow: rgba(255,77,240,0.4);
        }

        * { box-sizing: border-box; }
        body {
            margin:0;
            font-family:"Roboto",sans-serif;
            background: radial-gradient(ellipse at top, #3a1158 0%, #2a0f4a 25%, #0a0615 50%, #05030b 100%);
            background-attachment: fixed;
            color:#fff;
            padding-top:110px;
            min-height:100vh;
            overflow-x: hidden;
        }
        .page-shell { max-width:800px; margin:0 auto 80px; padding:40px 20px; animation: fadeInUp 0.6s ease-out; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(30px);} to {opacity:1; transform:translateY(0);} }

        .page-title { text-align: center; margin-bottom: 40px; position: relative; }
        .page-title::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--ma-warning), transparent);
            border-radius: 2px;
        }
        .page-title h1 {
            font-size: 2.5rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #ffca5f, #ffb84d, #ff4df0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(255,202,95,0.3);
        }
        .page-title p { color:var(--ma-text-soft); font-size: 1.05rem; opacity: 0.9; }

        .card-form {
            background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
            border-radius: 24px;
            padding: 32px 28px 28px;
            border: 1px solid var(--ma-border);
            box-shadow: 0 30px 60px rgba(0,0,0,0.7), inset 0 1px 0 rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }
        .card-form::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,202,95,0.1), transparent 70%);
            animation: rotate 20s linear infinite;
        }
        @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg);} }
        .card-form > * { position: relative; z-index: 1; }

        .target-preview {
            font-size:.9rem; color:#ffd9ff; padding:16px 18px; border-radius:16px;
            background:rgba(255,202,95,0.1); backdrop-filter: blur(10px); border:2px solid rgba(255,202,95,0.3);
            margin-bottom:24px; position: relative; z-index: 1;
        }
        .target-preview strong { color: var(--ma-warning); font-weight: 700; }

        .form-label { font-size:.95rem; font-weight:600; color: #ffb8ff; margin-bottom: 8px; display: block; }
        .form-select, .form-control {
            background:rgba(9,15,30,0.95);
            backdrop-filter: blur(10px);
            border-radius:14px;
            border:2px solid var(--ma-border);
            color:#fff;
            font-size:.95rem;
            padding: 14px 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
        }
        .form-select:focus, .form-control:focus {
            border-color:var(--ma-warning);
            box-shadow: 0 0 25px rgba(255,202,95,0.5), 0 4px 20px rgba(0,0,0,0.3);
            background:rgba(6,10,22,1);
            outline: none;
            transform: translateY(-2px);
        }
        .form-control::placeholder { color: rgba(222,215,255,0.5); }
        textarea.form-control { resize: vertical; min-height: 120px; }
        .form-select option { background: #0a0615; color: #fff; }

        .error-text { font-size:.8rem; color:var(--ma-danger); display:none; margin-top:6px; font-weight: 500; animation: shake 0.3s ease; }
        @keyframes shake { 0%,100% { transform: translateX(0);} 25% { transform: translateX(-5px);} 75% { transform: translateX(5px);} }

        .btn-primary-ma {
            background:linear-gradient(135deg,var(--ma-warning),#ffb84d);
            border:none;
            border-radius:999px;
            padding:14px 32px;
            font-weight:700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
            color: #2b163b;
            box-shadow: 0 0 20px rgba(255,202,95,0.6), 0 4px 15px rgba(255,202,95,0.3), inset 0 1px 0 rgba(255,255,255,0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        .btn-primary-ma::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        .btn-primary-ma:hover::before { left: 100%; }
        .btn-primary-ma:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 0 30px rgba(255,202,95,0.9), 0 8px 25px rgba(255,202,95,0.4); }

        .btn-secondary-ma {
            border-radius:999px;
            padding:14px 28px;
            border:2px solid var(--ma-border);
            background:rgba(8,22,36,0.7);
            backdrop-filter: blur(10px);
            color:#ffb8ff;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-secondary-ma:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,77,240,0.4); transform: translateY(-2px); color: #fff; }

        .d-flex { display: flex; }
        .gap-2 { gap: 12px; }
        .mb-3 { margin-bottom: 24px; }
        .text-danger { color: var(--ma-danger); }

        @media (max-width: 768px) {
            .page-title h1 { font-size: 2rem; }
            .card-form { padding: 24px 20px 20px; }
            .d-flex { flex-direction: column; }
            .btn-primary-ma, .btn-secondary-ma { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<div class="page-shell">
    <div class="page-title">
        <h1>Signaler un contenu</h1>
        <p>Merci d’aider à garder MindArena sain pour toute la communauté.</p>
    </div>

    <div class="card-form">
        <div class="target-preview">
            <strong>Cible :</strong>
            <?= htmlspecialchars($targetType) ?> #<?= (int)$targetId ?>
            <?php if ($target && !empty($target['title'])): ?>
                – <?= htmlspecialchars($target['title']) ?>
            <?php elseif ($target && !empty($target['content'])): ?>
                – « <?= htmlspecialchars(mb_substr($target['content'],0,60)) ?>… »
            <?php endif; ?>
        </div>

        <form method="post" id="reportForm" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="target_type" value="<?= htmlspecialchars($targetType) ?>">
            <input type="hidden" name="target_id" value="<?= (int)$targetId ?>">

            <div class="mb-3">
                <label class="form-label">Raison du signalement <span class="text-danger">*</span></label>
                <select name="reason" id="reason" class="form-select" required aria-label="Choisir la raison">
                    <option value="">Sélectionner…</option>
                    <option value="spam">Spam / publicité</option>
                    <option value="contenu_inapproprie">Contenu inapproprié</option>
                    <option value="insultes">Insultes / harcèlement</option>
                    <option value="hors_sujet">Hors sujet</option>
                    <option value="autre">Autre</option>
                </select>
                <div class="error-text" id="reasonError"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Détails supplémentaires</label>
                <textarea name="details" id="details" rows="4" class="form-control" maxlength="1000" placeholder="Explique brièvement le problème…"></textarea>
            </div>

            <div class="d-flex gap-2">
                <a href="javascript:history.back();" class="btn-secondary-ma">
                    <i class="fa fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn-primary-ma">
                    <i class="fa fa-flag"></i> Envoyer le signalement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('reportForm');
    const reason = document.getElementById('reason');
    const reasonError = document.getElementById('reasonError');

    function validateReason() {
        if (!reason.value) {
            reasonError.textContent = "Merci de sélectionner une raison.";
            reasonError.style.display = 'block';
            return false;
        }
        reasonError.textContent = "";
        reasonError.style.display = 'none';
        return true;
    }

    reason.addEventListener('change', validateReason);

    form.addEventListener('submit', (e) => {
        if (!validateReason()) e.preventDefault();
    });

    window.MA_CHAT_CONTEXT = 'reports';
});
</script>

<?php include __DIR__ . '/chatbot.php'; ?>

</body>
</html>

