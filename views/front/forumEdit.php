<?php
// views/front/forumEdit.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$errors = $errors ?? [];
$title = $title ?? '';
$description = $description ?? '';
$csrf = $csrf ?? ($_SESSION['_csrf'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un forum - MindArena</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= BASE_URL ?>/ENDGAME/img/favicon.ico" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/animate.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/style.css">
    <style>
        body { margin:0; font-family:"Roboto",sans-serif; background: radial-gradient(circle at top, #3a1158 0, #0a0615 45%, #05030b 100%); color:#fff; padding-top:110px; min-height:100vh; }
        .page-shell { max-width:1000px; margin:0 auto 60px; padding:25px 15px; }
        .card-form { background:rgba(8,22,36,0.98); border-radius:18px; padding:22px 22px 18px; border:1px solid rgba(255,255,255,0.08); box-shadow:0 20px 40px rgba(0,0,0,0.7); }
        .error-text { font-size:.78rem; color:#ff4b5c; display:none; margin-top:4px; }
        .form-control { background:rgba(9,15,30,0.85); border-radius:12px; border:1px solid rgba(255,255,255,0.08); color:#fff; }
        .form-control:focus { border-color:#ff4df0; box-shadow:0 0 0 1px #ff4df0; }
    </style>
</head>
<body>
<?php include __DIR__ . '/_header.php'; ?>
<div class="page-shell">
    <h1 style="color:#ff4df0;font-weight:800;margin-bottom:10px;">Modifier le forum</h1>
    <p style="color:#d7d7ff;margin-bottom:18px;">Met a jour le titre ou la description de ce forum.</p>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div class="card-form">
        <form method="post" id="forumEditForm" action="index.php?action=edit-forum&id=<?= (int)($_GET['id'] ?? 0) ?>" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <div class="mb-3">
                <label class="form-label">Titre du forum <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control" required maxlength="80" value="<?= htmlspecialchars($title) ?>">
                <div class="form-text">Titre entre 3 et 80 caracteres.</div>
                <div class="error-text" id="titleError" style="<?= empty($errors['title']) ? '' : 'display:block;' ?>">
                    <?= htmlspecialchars($errors['title'] ?? '') ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description (optionnel)</label>
                <textarea name="description" id="description" rows="4" class="form-control" maxlength="500"><?= htmlspecialchars($description) ?></textarea>
                <div class="form-text">Max 500 caracteres.</div>
                <div class="error-text" id="descriptionError" style="<?= empty($errors['description']) ? '' : 'display:block;' ?>">
                    <?= htmlspecialchars($errors['description'] ?? '') ?>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="index.php?action=forums" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('forumEditForm');
    const title = document.getElementById('title');
    const titleError = document.getElementById('titleError');
    const description = document.getElementById('description');
    const descriptionError = document.getElementById('descriptionError');

    const showError = (el, msg) => {
        el.textContent = msg;
        el.style.display = msg ? 'block' : 'none';
    };

    const validateTitle = () => {
        const v = title.value.trim();
        if (v.length < 3 || v.length > 80) {
            showError(titleError, 'Le titre doit contenir entre 3 et 80 caracteres.');
            return false;
        }
        showError(titleError, '');
        return true;
    };

    const validateDescription = () => {
        const v = description.value.trim();
        if (v.length > 500) {
            showError(descriptionError, 'La description ne doit pas depasser 500 caracteres.');
            return false;
        }
        showError(descriptionError, '');
        return true;
    };

    title.addEventListener('input', validateTitle);
    description.addEventListener('input', validateDescription);
    validateTitle();
    validateDescription();

    form.addEventListener('submit', (e) => {
        let ok = true;
        if (!validateTitle()) ok = false;
        if (!validateDescription()) ok = false;
        if (!ok) e.preventDefault();
    });
});
</script>
</body>
</html>

