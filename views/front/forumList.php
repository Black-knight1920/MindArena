<?php
// views/front/forumList.php
$forums = $forums ?? [];
$currentUser = $currentUser ?? null;
$isAdmin = $isAdmin ?? false;

$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MindArena - Forums</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?= BASE_URL ?>/ENDGAME/img/favicon.ico" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/style.css">
    <style>
        body { margin:0; font-family:"Roboto",sans-serif; background: radial-gradient(ellipse at top, #3a1158 0%, #2a0f4a 25%, #0a0615 50%, #05030b 100%); color:#fff; padding-top:110px; min-height:100vh; }
        .page-header { max-width:1100px; margin:0 auto 10px; padding:50px 20px 24px; text-align: center; }
        .page-header h1 { font-size: 2.5rem; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; background: linear-gradient(135deg, #ff4df0, #ffb8ff, #7b2ff7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 12px; }
        .page-header p { color:#d7d7ff; opacity: 0.9; }
        .forums-wrapper { max-width: 1200px; margin: 0 auto 32px; padding: 0 15px; display:block; }
        .forum-toolbar { display:flex; flex-wrap:wrap; justify-content:center; align-items:center; gap:14px; margin:0 auto 18px; max-width:1100px; }
        .forum-search { flex:1 1 520px; min-width:280px; position: relative; }
        .forum-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #ff4df0; }
        .forum-search input { width:100%; padding:14px 18px 14px 42px; border-radius:12px; border:2px solid rgba(255,255,255,0.08); background:rgba(9,15,30,0.92); color:#fff; box-shadow: 0 12px 22px rgba(0,0,0,0.35); }
        .btn-neon { padding: 13px 22px; border-radius: 12px; border:none; text-decoration:none; color:#fff; font-weight:700; background:linear-gradient(135deg,#ff4df0,#7b2ff7); box-shadow: 0 0 16px rgba(255,77,240,0.55); transition: transform .15s ease, box-shadow .15s ease; }
        .btn-neon:hover { transform: translateY(-1px); box-shadow: 0 0 22px rgba(255,77,240,0.8); }
        .forum-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:18px; max-width:1100px; margin:0 auto; }
        .forum-card { background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9)); border-radius: 16px; padding:16px; border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 16px 32px rgba(0,0,0,0.55); transition: transform .12s ease, box-shadow .12s ease; width:100%; }
        .forum-card:hover { transform: translateY(-2px); box-shadow: 0 20px 36px rgba(0,0,0,0.65); }
        .forum-title { font-size:1.15rem; font-weight:700; margin-bottom:8px; color:#ff9cff; display:flex; align-items:center; gap:8px; }
        .forum-desc { font-size:.9rem; color:#e1d7ff; opacity:.9; margin-bottom:10px; }
        .forum-meta { font-size:.8rem; opacity:.85; margin-bottom:12px; color:#d4c5ff; display:flex; flex-direction:column; gap:4px; }
        .forum-badges { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:12px; }
        .pill { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:700; }
        .pill-reports { background:rgba(255,99,132,0.1); color:#ff8aa1; border:1px solid rgba(255,99,132,0.35); }
        .pill-owner { background:rgba(0,255,204,0.08); color:#76ffd6; border:1px solid rgba(0,255,204,0.3); }
        .forum-actions { display:flex; flex-wrap:wrap; gap:8px; }
        .btn-small { font-size:.8rem; border-radius:999px; padding:8px 14px; border:none; text-decoration:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px; font-weight:600; letter-spacing:0.3px; }
        .btn-view { background:linear-gradient(135deg,#ff4df0,#7b2ff7); color:#fff; }
        .btn-report { background:linear-gradient(135deg, #ffca5f, #ff9d00); color:#2b163b; }
        .btn-delete { background:linear-gradient(135deg, #ff4b5c, #ff6b7a); color:#fff; }
        .btn-disabled { opacity:0.4; pointer-events:none; }
        .sort-group { display:flex; align-items:center; gap:8px; }
        .sort-group label { color:#d6d6ff; font-weight:600; }
        .sort-group select { background:rgba(9,15,30,0.95); color:#fff; border:1px solid rgba(255,255,255,0.12); border-radius:999px; padding:10px 14px; }
        .sort-group button { background:rgba(255,255,255,0.06); color:#fff; border:1px solid rgba(255,255,255,0.1); border-radius:999px; padding:10px 14px; cursor:pointer; }
        @media (max-width: 768px) { .forum-grid { grid-template-columns: 1fr; } .forum-toolbar { flex-direction: column; } .forum-search { width: 100%; } }
    </style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<section class="page-header">
    <h1>Forums</h1>
    <p>Structure et espaces de discussion de la communauté.</p>
</section>

<div class="forums-wrapper">
    <div class="forum-toolbar">
        <div class="forum-search">
            <i class="fa fa-search"></i>
            <input type="text" id="forumSearch" placeholder="Rechercher un forum (titre ou description)...">
        </div>
        <div class="forum-actions" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:flex-end;">
            <form method="get" style="display:flex; gap:8px; align-items:center;">
                <input type="hidden" name="action" value="forums">
                <label for="sort" style="color:#d7d7ff; font-weight:700;">Trier par</label>
                <select id="sort" name="sort" onchange="this.form.submit()" style="padding:10px 12px; border-radius:10px; border:1px solid rgba(255,255,255,0.12); background:rgba(9,15,30,0.95); color:#fff;">
                    <?php $s = $_GET['sort'] ?? 'date'; ?>
                    <option value="date" <?= $s === 'date' ? 'selected' : '' ?>>Date</option>
                    <option value="title" <?= $s === 'title' ? 'selected' : '' ?>>Titre</option>
                    <option value="author" <?= $s === 'author' ? 'selected' : '' ?>>Auteur</option>
                </select>
                <select name="dir" onchange="this.form.submit()" style="padding:10px 12px; border-radius:10px; border:1px solid rgba(255,255,255,0.12); background:rgba(9,15,30,0.95); color:#fff;">
                    <?php $d = $_GET['dir'] ?? 'desc'; ?>
                    <option value="desc" <?= $d === 'desc' ? 'selected' : '' ?>>Desc</option>
                    <option value="asc" <?= $d === 'asc' ? 'selected' : '' ?>>Asc</option>
                </select>
            </form>
            <a class="btn-neon" href="<?= $isAdmin || $currentUser ? $BASE . '/index.php?action=add-forum' : $BASE . '/index.php?action=login' ?>"><i class="fa fa-plus"></i> + Nouveau forum</a>
            <a class="btn-small btn-ghost" href="<?= $BASE ?>/index.php?action=home"><i class="fa fa-home"></i> Accueil</a>
        </div>
        </div>
    </div>

    <?php if (empty($forums)): ?>
        <p style="opacity:.8;">Aucun forum pour l'instant.</p>
    <?php else: ?>
        <div class="forum-grid" id="forumGrid">
            <?php foreach ($forums as $f): ?>
                <?php
                    $isOwner = $currentUser && strcasecmp($currentUser, (string)($f['created_by'] ?? '')) === 0;
                ?>
                <div class="forum-card"
                     data-title="<?= strtolower(htmlspecialchars($f['title'])) ?>"
                     data-desc="<?= strtolower(htmlspecialchars($f['description'] ?? '')) ?>">

                    <div class="forum-title"><?= htmlspecialchars($f['title']) ?></div>

                    <?php if (!empty($f['description'])): ?>
                        <div class="forum-desc"><?= nl2br(htmlspecialchars($f['description'])) ?></div>
                    <?php endif; ?>

                    <div class="forum-badges">
                        <?php if (!empty($f['created_by'])): ?>
                            <span class="pill pill-owner"><i class="fa fa-user"></i> <?= htmlspecialchars($f['created_by']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($f['report_count'])): ?>
                            <span class="pill pill-reports"><i class="fa fa-flag"></i> <?= (int)$f['report_count'] ?> signalement<?= (int)$f['report_count'] > 1 ? 's' : '' ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="forum-meta">
                        <span>Forum #<?= (int)$f['id'] ?></span>
                        <?php if (!empty($f['created_by'])): ?>
                            <span>Créé par <strong><?= htmlspecialchars($f['created_by']) ?></strong></span>
                        <?php endif; ?>
                        <?php if (!empty($f['created_at'])): ?>
                            <span>le <?= htmlspecialchars($f['created_at']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="forum-actions">
                        <a class="btn-small btn-view"
                           href="<?= $BASE ?>/index.php?action=publications&forum_id=<?= (int)$f['id'] ?>">
                            Voir les publications
                        </a>

                        <?php
                            $reports = (int)($f['report_count'] ?? 0);
                            $canAdminAct = $isAdmin && $reports > 0;
                        ?>
                        <?php if ($isOwner): ?>
                            <a class="btn-small"
                               href="<?= $BASE ?>/index.php?action=edit-forum&id=<?= (int)$f['id'] ?>"
                               style="background:linear-gradient(135deg, #00d0ff, #7b42ff); color:#fff;">
                                <i class="fa fa-edit"></i> Modifier
                            </a>
                            <a class="btn-small btn-delete"
                               href="<?= $BASE ?>/index.php?action=delete-forum-confirm&id=<?= (int)$f['id'] ?>">
                                <i class="fa fa-trash"></i> Supprimer
                            </a>
                        <?php else: ?>
                            <a class="btn-small <?= $canAdminAct ? '' : 'btn-disabled' ?>"
                               href="<?= $canAdminAct ? $BASE . '/index.php?action=edit-forum&id=' . (int)$f['id'] : '#' ?>"
                               style="background:linear-gradient(135deg, #00d0ff, #7b42ff); color:#fff;">
                                <i class="fa fa-edit"></i> Modifier (admin/report)
                            </a>
                            <a class="btn-small btn-delete <?= $canAdminAct ? '' : 'btn-disabled' ?>"
                               href="<?= $canAdminAct ? $BASE . '/index.php?action=delete-forum-confirm&id=' . (int)$f['id'] : '#' ?>">
                                Supprimer (admin/report)
                            </a>
                        <?php endif; ?>

                        <a class="btn-small btn-report"
                           href="<?= $BASE ?>/index.php?action=report&target_type=forum&target_id=<?= (int)$f['id'] ?>">
                            Signaler
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('forumSearch');
    const cards = document.querySelectorAll('#forumGrid .forum-card');
    if (!input || !cards.length) return;
    input.addEventListener('input', () => {
        const q = input.value.toLowerCase().trim();
        cards.forEach(card => {
            const t = card.dataset.title || '';
            const d = card.dataset.desc || '';
            card.style.display = (t.includes(q) || d.includes(q)) ? '' : 'none';
        });
    });
    window.MA_CHAT_CONTEXT = 'forums';
});

</script>

<?php include __DIR__ . '/chatbot.php'; ?>

</body>
</html>

