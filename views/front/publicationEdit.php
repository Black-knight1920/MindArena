<?php
// views/front/publicationEdit.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$publication = $publication ?? [];
$forum = $forum ?? null;
$errors = $errors ?? [];
$old = $old ?? [
    'content' => $publication['content'] ?? '',
    'forum_id' => $publication['forum_id'] ?? 0,
];
$csrf = $csrf ?? ($_SESSION['_csrf'] ?? '');
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la publication</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/bootstrap.min.css">
    <style>
        body { background:#0a0615; color:#fff; padding-top:110px; font-family:"Roboto",sans-serif; }
        .card { background:rgba(8,22,36,0.94); border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:24px; }
        .error-text { color:#ff4b5c; margin-top:6px; display:block; }
    </style>
</head>
<body>
<?php include __DIR__ . '/_header.php'; ?>
<div class="container" style="max-width:800px; margin-top:30px;">
    <div class="card">
        <h1 class="h4 mb-3">Modifier la publication</h1>
        <p class="text-muted">Forum : <?= htmlspecialchars($forum['title'] ?? '') ?></p>
        <form method="post" action="<?= $BASE ?>/index.php?action=edit-publication&id=<?= (int)($publication['id'] ?? 0) ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <div class="mb-3">
                <label for="content" class="form-label">Contenu</label>
                <textarea name="content" id="content" rows="5" class="form-control" required><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
                <?php if (!empty($errors)): ?>
                    <div class="error-text"><?= htmlspecialchars(implode(' ', $errors)) ?></div>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-secondary" href="<?= $BASE ?>/index.php?action=publications&forum_id=<?= (int)($publication['forum_id'] ?? 0) ?>">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const content = document.getElementById('content');
    const form = content?.form;
    if (!form || !content) return;
    form.addEventListener('submit', (e) => {
        const v = (content.value || '').trim();
        if (v.length < 5) {
            e.preventDefault();
            alert('Le contenu doit contenir au moins 5 caracteres.');
        }
    });
});
</script>
</body>
</html>

