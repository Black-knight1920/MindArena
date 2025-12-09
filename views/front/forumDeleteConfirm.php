<?php
// views/front/forumDeleteConfirm.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$csrf = $csrf ?? ($_SESSION['_csrf'] ?? '');
$forumId = (int)($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer le forum</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/bootstrap.min.css">
    <style>
        body { background:#0a0615; color:#fff; padding-top:110px; font-family:"Roboto",sans-serif; }
        .card { background:#11182a; border:1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body>
<?php include __DIR__ . '/_header.php'; ?>
<div class="container" style="max-width:640px; margin-top:30px;">
    <div class="card p-4">
        <h2 class="mb-3 text-danger">Supprimer ce forum ?</h2>
        <p>Cette action est definitive. Vos publications resteront mais le forum sera retire.</p>
        <form method="post" action="index.php?action=delete-forum">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="id" value="<?= $forumId ?>">
            <div class="d-flex gap-2">
                <a class="btn btn-secondary" href="index.php?action=forums">Annuler</a>
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

