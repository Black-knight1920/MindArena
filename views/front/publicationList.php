<?php
// views/front/publicationList.php
// $forum et $publications fournis par PublicationController::listFront()
if (!isset($forum) || !$forum) {
    die("<h2 style='color:white;text-align:center;margin-top:50px'>Forum introuvable</h2>");
}
$publications = $publications ?? [];
$currentUser = $currentUser ?? null;
$isAdmin = $isAdmin ?? false;

$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($forum['title']) ?> - Publications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?= BASE_URL ?>/ENDGAME/img/favicon.ico" rel="shortcut icon" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/style.css">
    <style>
        body { background: radial-gradient(ellipse at top, #4d1b7d 0%, #2a0f4a 25%, #160820 50%, #0a0515 100%); color:#fff; padding-top:110px; font-family:"Roboto",sans-serif; }
        .header { text-align:center; padding:60px 20px 30px; }
        .header h1 { font-size:2.4rem; font-weight:900; background:linear-gradient(135deg,#ff4df0,#ffb8ff,#7b2ff7); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:10px; }
        .header p { color:#d7d7ff; max-width:800px; margin:0 auto; }
        .actions-bar { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin:18px 0 6px; }
        .btn-pill { padding:12px 18px; border-radius:999px; border:none; text-decoration:none; font-weight:700; }
        .btn-main { background:linear-gradient(135deg,#ff4df0,#7b2ff7); color:#fff; }
        .btn-ghost { border:2px solid #ff4df0; color:#ffb8ff; background:transparent; }
        .search-wrap { max-width:700px; margin:0 auto 26px; padding:0 18px; position:relative; }
        .search-wrap input { width:100%; padding:12px 14px 12px 40px; border-radius:12px; border:1px solid rgba(255,255,255,0.12); background:rgba(8,22,36,0.9); color:#fff; }
        .search-wrap i { position:absolute; left:28px; top:50%; transform:translate(-50%,-50%); color:#ff4df0; }
        .container-pubs { max-width:1000px; margin:0 auto 60px; padding:0 18px; }
        .pub-card { background:rgba(8,22,36,0.9); border:1px solid rgba(255,255,255,0.12); border-radius:16px; padding:18px; margin-bottom:14px; box-shadow:0 16px 32px rgba(0,0,0,0.4); }
        .pub-head { display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; }
        .pub-author { font-weight:800; color:#ffb8ff; }
        .pub-meta { color:#cbd5e1; font-size:13px; }
        .pub-body { margin-top:10px; line-height:1.6; color:#e5e7eb; }
        .pub-actions { display:flex; flex-wrap:wrap; gap:8px; margin-top:12px; }
        .btn-small { padding:8px 12px; border-radius:10px; border:none; font-weight:700; text-decoration:none; display:inline-flex; gap:6px; align-items:center; }
        .btn-report { background:linear-gradient(135deg,#ffca5f,#ff9d00); color:#2b163b; }
        .btn-delete { background:linear-gradient(135deg,#ff4b5c,#ff6b7a); color:#fff; }
        .btn-edit { background:linear-gradient(135deg,#00d0ff,#7b42ff); color:#fff; }
        .btn-disabled { opacity:0.4; pointer-events:none; }
        .empty { text-align:center; padding:40px 12px; color:#cbd5e1; }
        .sort-group { display:flex; align-items:center; gap:8px; }
        .sort-group select { background:rgba(8,22,36,0.9); color:#fff; border:1px solid rgba(255,255,255,0.12); border-radius:999px; padding:10px 14px; }
        .sort-group button { background:rgba(255,255,255,0.06); color:#fff; border:1px solid rgba(255,255,255,0.12); border-radius:999px; padding:10px 14px; cursor:pointer; }
        @media(max-width:768px){ .pub-head{flex-direction:column;align-items:flex-start;} }
    </style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<div class="header">
    <h1><?= htmlspecialchars($forum['title']) ?></h1>
    <?php if (!empty($forum['description'])): ?>
        <p><?= nl2br(htmlspecialchars($forum['description'])) ?></p>
    <?php endif; ?>
    <div class="actions-bar">
        <a class="btn-pill btn-ghost" href="<?= $BASE ?>/index.php?action=forums"><i class="fa fa-arrow-left"></i> Retour Forums</a>
        <a class="btn-pill btn-main" href="<?= $currentUser ? $BASE . '/index.php?action=add-publication&forum_id=' . (int)$forum['id'] : $BASE . '/index.php?action=login' ?>"><i class="fa fa-plus"></i> Nouvelle publication</a>
        <form method="get" class="sort-group">
            <input type="hidden" name="action" value="publications">
            <input type="hidden" name="forum_id" value="<?= (int)$forum['id'] ?>">
            <label for="sort">Trier par</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="date" <?= ($sort ?? 'date') === 'date' ? 'selected' : '' ?>>Date</option>
                <option value="title" <?= ($sort ?? '') === 'title' ? 'selected' : '' ?>>Titre</option>
                <option value="author" <?= ($sort ?? '') === 'author' ? 'selected' : '' ?>>Auteur</option>
            </select>
            <select name="dir" onchange="this.form.submit()">
                <option value="desc" <?= ($dir ?? 'desc') === 'desc' ? 'selected' : '' ?>>Desc</option>
                <option value="asc" <?= ($dir ?? '') === 'asc' ? 'selected' : '' ?>>Asc</option>
            </select>
        </form>
    </div>
    </div>

<?php if (!empty($publications)): ?>
    <div class="search-wrap">
        <i class="fa fa-search"></i>
        <input type="text" id="publicationSearch" placeholder="Rechercher une publication...">
    </div>
<?php endif; ?>

<div class="container-pubs" id="publicationsList">
    <?php if (empty($publications)): ?>
        <div class="empty">Aucune publication pour le moment.</div>
    <?php else: ?>
        <?php foreach ($publications as $p): ?>
            <?php $isOwner = $currentUser && strcasecmp($currentUser, (string)($p['author'] ?? '')) === 0; ?>
            <article class="pub-card" data-author="<?= strtolower(htmlspecialchars($p['author'])) ?>" data-content="<?= strtolower(htmlspecialchars($p['content'])) ?>">
                <div class="pub-head">
                    <div>
                        <div class="pub-author"><?= htmlspecialchars($p['author']) ?></div>
                        <div class="pub-meta"><?= date("d/m/Y à H:i", strtotime($p['created_at'])) ?></div>
                    </div>
                    <div class="pub-meta">#<?= (int)$p['id'] ?></div>
                </div>
                <div class="pub-body"><?= nl2br(htmlspecialchars($p['content'])) ?></div>
                <?php $reports = (int)($p['report_count'] ?? 0); $canAdminAct = $isAdmin && $reports > 0; ?>
                <div class="pub-actions">
                    <a href="<?= $BASE ?>/index.php?action=report&target_type=publication&target_id=<?= (int)$p['id'] ?>" class="btn-small btn-report"><i class="fa fa-flag"></i> Signaler</a>
                    <?php if ($isOwner): ?>
                        <a href="<?= $BASE ?>/index.php?action=edit-publication&id=<?= (int)$p['id'] ?>" class="btn-small btn-edit"><i class="fa fa-edit"></i> Modifier</a>
                        <a href="<?= $BASE ?>/index.php?action=delete-publication-confirm&id=<?= (int)$p['id'] ?>&forum_id=<?= (int)$forum['id'] ?>" class="btn-small btn-delete"><i class="fa fa-trash"></i> Supprimer</a>
                    <?php else: ?>
                        <a href="<?= $canAdminAct ? $BASE . '/index.php?action=edit-publication&id=' . (int)$p['id'] : '#' ?>" class="btn-small btn-edit <?= $canAdminAct ? '' : 'btn-disabled' ?>"><i class="fa fa-edit"></i> Modifier (admin, report)</a>
                        <a href="<?= $canAdminAct ? $BASE . '/index.php?action=delete-publication-confirm&id=' . (int)$p['id'] . '&forum_id=' . (int)$forum['id'] : '#' ?>" class="btn-small btn-delete <?= $canAdminAct ? '' : 'btn-disabled' ?>"><i class="fa fa-trash"></i> Supprimer (admin, report)</a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('publicationSearch');
    const cards = document.querySelectorAll('#publicationsList .pub-card');
    if (searchInput && cards.length) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.toLowerCase().trim();
            cards.forEach(card => {
                const author = card.dataset.author || '';
                const content = card.dataset.content || '';
                card.style.display = (author.includes(q) || content.includes(q)) ? '' : 'none';
            });
        });
    }
    window.MA_CHAT_CONTEXT = 'publications';
});

</script>

<?php include __DIR__ . '/chatbot.php'; ?>

</body>
</html>

