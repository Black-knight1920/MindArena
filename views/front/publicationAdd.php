<?php
// views/front/publicationAdd.php
$forum = $forum ?? null;
$forumId = $forum['id'] ?? ($_GET['forum_id'] ?? null);
$currentUser = $currentUser ?? '';
$errors = $errors ?? [];
$old = $old ?? ['author' => $currentUser, 'content' => ''];
$csrf = $csrf ?? '';

$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle publication - MindArena</title>
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
        .page-shell { max-width:900px; margin:0 auto 80px; padding:40px 20px; }
        .page-title { text-align: center; margin-bottom: 40px; position: relative; }
        .page-title h1 { font-size: 2.5rem; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; background: linear-gradient(135deg, #ff4df0, #ffb8ff, #7b2ff7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 0 0 30px rgba(255,77,240,0.3); }
        .page-title p { color:var(--ma-text-soft); font-size: 1.05rem; opacity: 0.9; }
        .page-title strong { color: #ffb8ff; font-weight: 700; }

        .card-form {
            background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
            border-radius: 24px;
            padding: 32px 28px 28px;
            border: 1px solid var(--ma-border);
            box-shadow: 0 30px 60px rgba(0,0,0,0.7), inset 0 1px 0 rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
        }
        .form-group { margin-bottom: 18px; }
        .form-group label { display:block; margin-bottom:8px; font-weight:700; color:#ffb8ff; }
        .form-control { width:100%; padding:12px; border-radius:12px; border:1px solid var(--ma-border); background:rgba(9,15,30,0.85); color:#fff; }
        .form-control:focus { border-color:var(--ma-primary); box-shadow:0 0 0 1px var(--ma-primary); background:rgba(6,10,22,0.95); }
        .form-text { font-size:0.85rem; color:#c7c7ff; opacity:0.8; }
        .error-text { color: var(--ma-danger); margin-top: 6px; font-size: 0.9rem; }
        .actions { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:20px; flex-wrap:wrap; }
        .btn-primary { padding: 12px 20px; border-radius: 12px; background: linear-gradient(135deg, #ff4df0, #7b2ff7); color:#fff; border:none; font-weight:800; cursor:pointer; }
        .link { color:#c084fc; text-decoration:none; font-weight:600; }
        .link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<div class="page-shell">
    <div class="page-title">
        <h1>Nouvelle publication</h1>
        <p>Forum : <strong><?= htmlspecialchars($forum['title'] ?? '') ?></strong></p>
    </div>

    <div class="card-form">
        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?action=add-publication&forum_id=<?= urlencode($forumId) ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <div class="form-group">
                <label>Auteur</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($currentUser) ?>" readonly>
                <div class="form-text">Le pseudo connectAc est utilisAc automatiquement.</div>
            </div>

            <div class="form-group">
                <label for="content">Contenu <span class="text-danger">*</span></label>
                <textarea name="content" id="content" rows="5" class="form-control" required><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
                <?php if (!empty($errors)): ?>
                    <div class="error-text"><?= htmlspecialchars(implode(' ', $errors)) ?></div>
                <?php endif; ?>
            </div>

            <div class="actions">
                <a class="link" href="<?= $BASE ?>/index.php?action=publications&forum_id=<?= urlencode($forumId) ?>">&larr; Retour</a>
                <button type="submit" class="btn-primary">Publier</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/chatbot.php'; ?>

</body>
</html>

