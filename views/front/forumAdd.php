<?php
// views/front/forumAdd.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un forum - MindArena</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="/mindarena_forum/ENDGAME/img/favicon.ico" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/font-awesome.min.css">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/animate.css">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/style.css">

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
        <p>Crée une nouvelle catégorie de discussion pour la communauté.</p>
    </div>

    <div class="card-form">
        <form method="post" id="forumForm" novalidate>
            <div class="mb-3">
                <label class="form-label">Titre du forum <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title"
                       class="form-control" required maxlength="80">
                <div class="form-text">
                    Utilise un titre clair et concis (3 à 80 caractères).
                </div>
                <div class="error-text" id="titleError"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Créé par (pseudo) <span class="text-danger">*</span></label>
                <input type="text" name="created_by" id="created_by"
                       class="form-control" maxlength="50" required>
                <div class="form-text">Visible sur la fiche du forum.</div>
                <div class="error-text" id="creatorError"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description (optionnel)</label>
                <textarea name="description" id="description" rows="4"
                          class="form-control" maxlength="500"></textarea>
                <div class="form-text">
                    Résume en quelques phrases le rôle de ce forum.
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="/mindarena_forum/front/forums" class="btn btn-secondary-ma">
                    ← Retour
                </a>
                <button type="submit" class="btn btn-primary-ma">
                    Créer le forum
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validation dynamique
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('forumForm');
    const title = document.getElementById('title');
    const createdBy = document.getElementById('created_by');
    const titleError = document.getElementById('titleError');
    const creatorError = document.getElementById('creatorError');

    function validateTitle() {
        const v = title.value.trim();
        if (v.length < 3) {
            titleError.textContent = "Le titre doit contenir au moins 3 caractères.";
            titleError.style.display = 'block';
            return false;
        }
        titleError.textContent = "";
        titleError.style.display = 'none';
        return true;
    }

    function validateCreator() {
        const v = createdBy.value.trim();
        if (!v.length) {
            creatorError.textContent = "Le pseudo du créateur est obligatoire.";
            creatorError.style.display = 'block';
            return false;
        }
        creatorError.textContent = "";
        creatorError.style.display = 'none';
        return true;
    }

    title.addEventListener('input', validateTitle);
    createdBy.addEventListener('input', validateCreator);

    form.addEventListener('submit', (e) => {
        if (!validateTitle() || !validateCreator()) {
            e.preventDefault();
        }
    });

    window.MA_CHAT_CONTEXT = 'forums';
});
</script>

<?php include __DIR__ . '/chatbot.php'; ?>

</body>
</html>
