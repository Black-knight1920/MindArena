<?php
// views/front/home.php
$forums = $forums ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MindArena - Accueil</title>
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
            --ma-bg-soft: #141026;
            --ma-card: rgba(8, 22, 36, 0.98);
            --ma-primary: #ff4df0;
            --ma-secondary: #b01ba5;
            --ma-accent: #ffca5f;
            --ma-danger: #ff4b5c;
            --ma-border: rgba(255,255,255,0.08);
            --ma-text-soft: #d7d7ff;
        }

        body {
            margin: 0;
            font-family: "Roboto", sans-serif;
            background: radial-gradient(circle at top, #3a1158 0, #0a0615 45%, #05030b 100%);
            color: #fff;
            padding-top: 110px; /* header fixe */
            min-height: 100vh;
        }

        /* HERO */
        .hero {
            min-height: 65vh;
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
            background:url('/mindarena_forum/ENDGAME/img/slider-bg-1.jpg') center/cover no-repeat;
            position:relative;
        }
        .hero::before {
            content:'';
            position:absolute;
            inset:0;
            background:
                radial-gradient(circle at top, rgba(255,77,240,0.25), transparent 60%),
                linear-gradient(135deg,rgba(8,22,36,0.95),rgba(80,23,85,0.7));
        }
        .hero-inner {
            position:relative;
            z-index:1;
            max-width:720px;
            padding:20px;
        }
        .hero-title {
            font-size: 3.2rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 18px rgba(176,27,165,0.9);
        }
        .hero-title span {
            color: var(--ma-primary);
        }
        .hero-sub {
            margin-top: 12px;
            font-size: 1.05rem;
            color: #e5defc;
        }
        .hero-btns {
            margin-top: 25px;
            display:flex;
            gap:12px;
            justify-content:center;
            flex-wrap:wrap;
        }
        .btn-neon {
            padding: 12px 24px;
            border-radius: 999px;
            border:none;
            text-decoration:none;
            color:#fff;
            font-weight:700;
            text-transform:uppercase;
            background:linear-gradient(135deg,var(--ma-primary),var(--ma-secondary));
            box-shadow:0 0 18px rgba(255,77,240,0.7);
            transition:0.25s;
            font-size:.9rem;
        }
        .btn-neon-outline {
            padding: 12px 24px;
            border-radius: 999px;
            border:1px solid var(--ma-primary);
            text-decoration:none;
            color:#ffb8ff;
            font-weight:700;
            text-transform:uppercase;
            background:rgba(0,0,0,0.2);
            transition:0.25s;
            font-size:.9rem;
        }
        .btn-neon:hover {
            transform:translateY(-2px);
            box-shadow:0 0 24px rgba(255,77,240,1);
        }
        .btn-neon-outline:hover {
            background:var(--ma-primary);
            color:#160819;
            box-shadow:0 0 18px rgba(255,77,240,0.7);
        }

        /* SECTION FORUMS */
        .section-title {
            text-align:center;
            padding:40px 15px 10px;
        }
        .section-title h2 {
            font-size: 2.3rem;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
            color:var(--ma-primary);
        }
        .section-title p {
            color:var(--ma-text-soft);
            opacity:.9;
        }

        .forums-wrapper {
            max-width: 1200px;
            margin: 0 auto 70px;
            padding: 0 15px;
        }

        .forum-toolbar {
            display:flex;
            flex-wrap:wrap;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            margin-bottom:20px;
        }
        .forum-search {
            flex:1;
            min-width:260px;
        }
        .forum-search input {
            width:100%;
            padding:11px 40px 11px 14px;
            border-radius:999px;
            border:1px solid var(--ma-border);
            background:rgba(9,15,30,0.85);
            color:#fff;
            outline:none;
            font-size:.9rem;
            box-shadow:0 0 0 1px rgba(255,255,255,0.02);
        }
        .forum-search input::placeholder {
            color:#8a87b3;
        }

        .forum-grid {
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
            gap:20px;
        }
        .forum-card {
            background: var(--ma-card);
            border-radius: 16px;
            padding:18px 16px 14px;
            border: 1px solid var(--ma-border);
            box-shadow: 0 20px 35px rgba(0,0,0,0.7);
            position:relative;
            overflow:hidden;
            transition:0.25s;
        }
        .forum-card::before {
            content:'';
            position:absolute;
            inset:0;
            opacity:0;
            background:
                radial-gradient(circle at top left,rgba(255,77,240,0.18),transparent 55%),
                radial-gradient(circle at bottom right,rgba(176,27,165,0.16),transparent 55%);
            transition:opacity 0.25s;
            pointer-events:none;
        }
        .forum-card:hover::before { opacity:1; }
        .forum-card:hover { transform:translateY(-4px); }

        .forum-title {
            font-size:1.1rem;
            font-weight:700;
            margin-bottom:6px;
            color:#ff9cff;
        }
        .forum-desc {
            font-size:.9rem;
            color:#ddd;
            opacity:.9;
            margin-bottom:12px;
        }
        .forum-meta {
            font-size:.78rem;
            opacity:.78;
            margin-bottom:12px;
            color:#b2aedc;
        }
        .forum-meta span {
            margin-right:10px;
        }

        .forum-actions {
            display:flex;
            flex-wrap:wrap;
            gap:8px;
        }
        .btn-small {
            font-size:.78rem;
            border-radius:999px;
            padding:6px 11px;
            border:none;
            text-decoration:none;
            cursor:pointer;
            transition:0.2s;
        }
        .btn-view {
            background:linear-gradient(135deg,var(--ma-primary),var(--ma-secondary));
            color:#fff;
        }
        .btn-report {
            background:var(--ma-accent);
            color:#2b163b;
        }
        .btn-delete {
            background:var(--ma-danger);
            color:#fff;
        }
        .btn-small:hover { filter:brightness(1.05); transform:translateY(-1px); }

        footer {
            background:#050814;
            text-align:center;
            padding:22px 10px;
            margin-top:30px;
            border-top:1px solid rgba(255,255,255,0.06);
        }
        footer p { color:#777; margin:0; font-size:.8rem; }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.3rem; }
            body { padding-top: 80px; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/layout/_header.php'; ?>

<!-- HERO -->
<section class="hero" id="home">
    <div class="hero-inner">
        <h1 class="hero-title">
            Welcome to <span>MindArena</span>
        </h1>
        <p class="hero-sub">
            L’arène communautaire où les joueurs débattent, partagent, s’entraident
            et construisent ensemble des espaces de discussion uniques 🎮
        </p>
        <div class="hero-btns">
            <a href="/mindarena_forum/front/forums" class="btn-neon">Parcourir les forums</a>
            <a href="/mindarena_forum/front/add-forum" class="btn-neon-outline">Créer un forum</a>
        </div>
    </div>
</section>

<!-- FORUMS DYNAMIQUES -->
<section id="forums">
    <div class="section-title">
        <h2>Community Forums</h2>
        <p>Explore les discussions de la communauté ou lance ton propre espace.</p>
    </div>

    <div class="forums-wrapper">
        <div class="forum-toolbar">
            <div class="forum-search">
                <input type="text" id="forumSearch" placeholder="Rechercher un forum (titre ou description)...">
            </div>
            <a href="/mindarena_forum/front/add-forum" class="btn-neon" style="font-size:.82rem;">
                + Nouveau forum
            </a>
        </div>

        <?php if (empty($forums)): ?>
            <p style="opacity:.8;">Aucun forum pour l’instant. Sois le premier à en créer un !</p>
        <?php else: ?>
            <div class="forum-grid" id="forumGrid">
                <?php foreach ($forums as $f): ?>
                    <div class="forum-card"
                         data-title="<?= strtolower(htmlspecialchars($f['title'])) ?>"
                         data-desc="<?= strtolower(htmlspecialchars($f['description'] ?? '')) ?>">

                        <div class="forum-title">
                            <?= htmlspecialchars($f['title']) ?>
                        </div>

                        <?php if (!empty($f['description'])): ?>
                            <div class="forum-desc">
                                <?= nl2br(htmlspecialchars($f['description'])) ?>
                            </div>
                        <?php endif; ?>

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
                               href="/mindarena_forum/front/publications?forum_id=<?= (int)$f['id'] ?>">
                                Voir les publications
                            </a>

                            <a class="btn-small btn-report"
                               href="/mindarena_forum/front/report?target_type=forum&target_id=<?= (int)$f['id'] ?>">
                                Signaler
                            </a>

                            <a class="btn-small btn-delete"
                               href="/mindarena_forum/front/delete-forum?id=<?= (int)$f['id'] ?>"
                               onclick="return confirm('Supprimer ce forum ainsi que ses publications ?');">
                                Supprimer
                            </a>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<footer>
    <p>© <?= date('Y') ?> MindArena — Built for gamers, by gamers.</p>
</footer>

<script>
// Filtre de forums en temps réel
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('forumSearch');
    const cards = document.querySelectorAll('#forumGrid .forum-card');

    if (!input || !cards.length) return;

    input.addEventListener('input', () => {
        const q = input.value.toLowerCase().trim();
        cards.forEach(card => {
            const t = card.dataset.title || '';
            const d = card.dataset.desc || '';
            const match = t.includes(q) || d.includes(q);
            card.style.display = match ? '' : 'none';
        });
    });
});

// Contexte pour le chatbot
window.MA_CHAT_CONTEXT = 'default';
</script>

<?php include __DIR__ . '/chatbot.php'; ?>

</body>
</html>
