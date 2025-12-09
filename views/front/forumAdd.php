<?php
// views/front/forumAdd.php
$errors = $errors ?? [];
$title = $title ?? '';
$description = $description ?? '';
$currentUser = $currentUser ?? '';
$csrf = $csrf ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CrAcer un forum - MindArena</title>
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
        }
        body {
            margin:0;
            font-family:"Roboto",sans-serif;
            background: radial-gradient(circle at top, #3a1158 0, #0a0615 45%, #05030b 100%);
            color:#fff;
            padding-top:110px;
            min-height:100vh;
        }

        .page-shell {
            max-width:1000px;
            margin:0 auto 60px;
            padding:25px 15px;
        }
        .page-title {
            margin-bottom:20px;
        }
        .page-title h1 {
            font-size:2rem;
            font-weight:800;
            color:var(--ma-primary);
        }
        .page-title p { color:var(--ma-text-soft); }

        .card-form {
            background:var(--ma-card);
            border-radius:18px;
            padding:22px 22px 18px;
            border:1px solid var(--ma-border);
            box-shadow:0 20px 40px rgba(0,0,0,0.7);
        }
        .form-label {
            font-size:.9rem;
            font-weight:500;
        }
        .form-control {
            background:rgba(9,15,30,0.85);
            border-radius:12px;
            border:1px solid var(--ma-border);
            color:#fff;
            font-size:.9rem;
        }
        .form-control:focus {
            border-color:var(--ma-primary);
            box-shadow:0 0 0 1px var(--ma-primary);
            background:rgba(6,10,22,0.95);
        }
        .form-text {
            font-size:.75rem;
            color:#8f8bc0;
        }
        .error-text {
            font-size:.78rem;
            color:var(--ma-danger);
            display:none;
            margin-top:4px;
        }

        .btn-primary-ma {
            background:linear-gradient(135deg,var(--ma-primary),var(--ma-secondary));
            border:none;
            border-radius:999px;
            padding:10px 22px;
            font-weight:600;
        }
        .btn-secondary-ma {
            border-radius:999px;
            padding:10px 18px;
            border:1px solid var(--ma-border);
            background:transparent;
            color:#ccc;
        }
        .btn-secondary-ma:hover {
            background:rgba(255,255,255,0.05);
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>
<div class="page-shell">
    <div class="page-title">
        <h1>Nouveau forum</h1>
        <p>CrAcer une nouvelle categorie de discussion pour la communautAc.</p>
    </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div class="card-form">
        <form method="post" id="forumForm" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
            <div class="mb-3">
                <label class="form-label">Titre du forum <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title"
                       class="form-control" required maxlength="80"
                       value="<?= htmlspecialchars($title) ?>">
                <div class="form-text">
                    Utilise un titre clair et concis (3 a 80 caracteres).
                </div>
                <div class="error-text" id="titleError" style="<?= empty($errors['title']) ? '' : 'display:block;' ?>">
                    <?= htmlspecialchars($errors['title'] ?? '') ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Crée par</label>
                <input type="text" name="created_by" id="created_by"
                       class="form-control" value="<?= htmlspecialchars($currentUser) ?>" readonly>
                <div class="form-text">Automatique : votre pseudo connectAc.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description (optionnel)</label>
                <textarea name="description" id="description" rows="4"
                          class="form-control" maxlength="500"><?= htmlspecialchars($description) ?></textarea>
                <div class="form-text">
                    Résume en quelques phrases ce forum.
                </div>
                <div class="error-text" id="descriptionError"></div>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>/index.php?action=forums" class="btn btn-secondary-ma">
                     Retour
                </a>
                <button type="submit" class="btn btn-primary-ma">
                    Créer le forum
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('forumForm');
    const title = document.getElementById('title');
    const titleError = document.getElementById('titleError');
    const description = document.getElementById('description');
    const descriptionError = document.getElementById('descriptionError');

    function showError(el, message) {
        el.textContent = message;
        el.style.display = message ? 'block' : 'none';
    }

    function validateTitle() {
        const v = title.value.trim();
        if (v.length < 3 || v.length > 80) {
            showError(titleError, "Le titre doit contenir entre 3 et 80 caractA\"res.");
            return false;
        }
        showError(titleError, "");
        return true;
    }

    function validateDescription() {
        const v = description.value.trim();
        if (v.length > 500) {
            showError(descriptionError, "La description ne doit pas dAcpasser 500 caractA\"res.");
            return false;
        }
        showError(descriptionError, "");
        return true;
    }

    title.addEventListener('input', validateTitle);
    description.addEventListener('input', validateDescription);

    form.addEventListener('submit', (e) => {
        let valid = true;
        if (!validateTitle()) valid = false;
        if (!validateDescription()) valid = false;
        if (!valid) e.preventDefault();
    });
});
</script>
</body>
</html>

