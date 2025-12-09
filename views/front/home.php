<?php
// views/front/home.php
// Page d'accueil MindArena + liste des forums

$forums = $forums ?? []; // sécurité

// Base URL calculée automatiquement (ex: /mindarena_forum)
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MindArena - Accueil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Assets ENDGAME -->
    <link href="<?= BASE_URL ?>/ENDGAME/img/favicon.ico" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/animate.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/style.css">

    <style>
        :root {
            --ma-bg: #0b0b1a;
            --ma-card: rgba(8,22,36,0.95);
            --ma-border: rgba(255,255,255,0.10);
            --ma-accent: #ff4df0;
            --ma-accent-soft: #b01ba5;
            --ma-warning: #ffca5f;
            --ma-danger: #ff4b5c;
            --ma-primary-glow: rgba(255,77,240,0.55);
            --ma-secondary-glow: rgba(123,47,247,0.35);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Roboto", sans-serif;
            background:
                radial-gradient(ellipse at top, #4d1b7d 0%, #2a0f4a 25%, #160820 50%, #0a0515 100%),
                linear-gradient(135deg, rgba(255,77,240,0.05), rgba(123,47,247,0.05));
            background-attachment: fixed;
            color: #fff;
            padding-top: 110px;
            overflow-x: hidden;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255,77,240,0.06) 0, transparent 25%),
                radial-gradient(circle at 80% 20%, rgba(123,47,247,0.08) 0, transparent 22%),
                radial-gradient(circle at 60% 80%, rgba(0,209,255,0.04) 0, transparent 30%);
            pointer-events: none;
            z-index: 0;
        }
        body::after {
            content: "";
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(
                135deg,
                rgba(255,255,255,0.02) 0,
                rgba(255,255,255,0.02) 1px,
                transparent 1px,
                transparent 12px
            );
            opacity: 0.35;
            mix-blend-mode: screen;
            pointer-events: none;
            z-index: 0;
        }

        /* HERO ******************************************************/
        .hero {
            min-height: 75vh;
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
            background:url('<?= BASE_URL ?>/ENDGAME/img/slider-bg-1.jpg') center/cover no-repeat;
            position:relative;
            overflow: hidden;
        }
        .hero::before {
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(135deg,rgba(6,14,26,0.9),rgba(80,23,85,0.78),rgba(22,8,32,0.95));
        }
        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 50%, rgba(255,77,240,0.15), transparent 50%),
                        radial-gradient(circle at 70% 50%, rgba(123,47,247,0.12), transparent 50%);
            animation: pulse 8s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }
        .hero-inner {
            position:relative;
            z-index:1;
            max-width:800px;
            padding:40px 20px;
            animation: fadeInUp 0.8s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 0 0 20px rgba(176,27,165,0.8),
                         0 0 40px rgba(255,77,240,0.5),
                         0 4px 8px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #ff4df0, #ffb8ff, #b01ba5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
            margin-bottom: 20px;
        }
        .hero-sub {
            margin-top: 20px;
            font-size: 1.15rem;
            color: #e1d7ff;
            opacity: 0.95;
            line-height: 1.6;
        }
        .hero-btns {
            margin-top: 32px;
            display:flex;
            gap:16px;
            justify-content:center;
            flex-wrap:wrap;
        }
        .btn-neon {
            padding: 14px 32px;
            border-radius: 999px;
            border:none;
            text-decoration:none;
            color:#fff;
            font-weight:700;
            text-transform:uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            background:linear-gradient(135deg,#ff4df0,#b01ba5);
            box-shadow: 0 0 20px rgba(255,77,240,0.6),
                        0 4px 15px rgba(255,77,240,0.3),
                        inset 0 1px 0 rgba(255,255,255,0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-neon::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        .btn-neon:hover::before {
            left: 100%;
        }
        .btn-neon:hover {
            transform:translateY(-3px) scale(1.02);
            box-shadow: 0 0 30px rgba(255,77,240,0.9),
                        0 8px 25px rgba(255,77,240,0.4);
        }
        .btn-neon-outline {
            padding: 14px 32px;
            border-radius: 999px;
            border: 2px solid #ff4df0;
            text-decoration:none;
            color:#ffb8ff;
            font-weight:700;
            text-transform:uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            background:rgba(8,22,36,0.7);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 15px rgba(255,77,240,0.3);
        }
        .btn-neon-outline:hover {
            background:linear-gradient(135deg, #ff4df0, #b01ba5);
            color:#160819;
            transform: translateY(-3px);
            box-shadow: 0 0 25px rgba(255,77,240,0.7);
        }

        /* SECTION FORUMS ********************************************/
        .section-title {
            text-align:center;
            padding:60px 15px 20px;
            position: relative;
        }
        .section-title::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--ma-accent), transparent);
            border-radius: 2px;
        }
        .section-title h2 {
            font-size: 2.5rem;
            text-transform: uppercase;
            font-weight: 900;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #ff4df0, #ffb8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
            text-shadow: 0 0 30px rgba(255,77,240,0.3);
        }
        .section-title p {
            color:#ddd;
            opacity:.9;
            max-width: 650px;
            margin: 0 auto;
            font-size: 1rem;
            line-height: 1.6;
        }

        .forums-wrapper {
            max-width: 1200px;
            margin: 0 auto 60px;
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
            min-width:240px;
        }
        .forum-search {
            position: relative;
        }
        .forum-search i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #ff4df0;
            font-size: 0.95rem;
            pointer-events: none;
        }
        .forum-search input {
            width:100%;
            padding:14px 18px 14px 42px;
            border-radius:999px;
            border:2px solid var(--ma-border);
            background:rgba(9,15,30,0.95);
            backdrop-filter: blur(10px);
            color:#fff;
            font-size: 0.9rem;
            outline:none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .forum-search input:focus {
            border-color: #ff4df0;
            box-shadow: 0 0 20px rgba(255,77,240,0.5),
                        0 4px 15px rgba(0,0,0,0.3);
            background: rgba(9,15,30,1);
        }
        .forum-search input::placeholder {
            color: rgba(222,215,255,0.6);
        }

        .forum-grid {
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
            gap:22px;
        }
        .forum-card {
            background: linear-gradient(135deg, rgba(8,22,36,0.94), rgba(15,30,50,0.9));
            border-radius: 18px;
            padding:20px 18px 18px;
            border: 1px solid var(--ma-border);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6),
                        inset 0 1px 0 rgba(255,255,255,0.08);
            position:relative;
            overflow:hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.6s ease-out both;
        }
        .forum-card::before {
            content:'';
            position:absolute;
            inset:-30%;
            opacity:0;
            background:radial-gradient(circle at top left,rgba(255,77,240,0.28),transparent 60%),
                        radial-gradient(circle at bottom right,rgba(0,208,255,0.22),transparent 60%);
            transition:opacity 0.3s ease;
        }
        .forum-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff4df0, #7b2ff7, #00d0ff, #ff4df0);
            background-size: 200% 100%;
            opacity: 0;
            transition: opacity 0.3s ease;
            animation: shimmer 2s linear infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .forum-card:hover::before { opacity:1; }
        .forum-card:hover::after { opacity:1; }
        .forum-card:hover {
            transform:translateY(-6px) scale(1.02);
            border-color: rgba(255,77,240,0.4);
            box-shadow: 0 30px 60px rgba(0,0,0,0.8),
                        0 0 40px rgba(255,77,240,0.35),
                        inset 0 1px 0 rgba(255,255,255,0.12);
        }

        .forum-title {
            font-size:1.15rem;
            font-weight:700;
            margin-bottom:8px;
            color:#ff9cff;
            line-height: 1.3;
            transition: color 0.3s;
            position: relative;
            z-index: 1;
        }
        .forum-card:hover .forum-title {
            color: #ffb8ff;
            text-shadow: 0 0 10px rgba(255,77,240,0.5);
        }
        .forum-desc {
            font-size:.9rem;
            color:#e1d7ff;
            opacity:.9;
            margin-bottom:12px;
            line-height: 1.5;
            position: relative;
            z-index: 1;
        }
        .forum-meta {
            font-size:.8rem;
            opacity:.85;
            margin-bottom:14px;
            color:#d4c5ff;
            position: relative;
            z-index: 1;
        }
        .forum-meta span { margin-right:10px; }
        .forum-meta strong {
            color: #ffd9ff;
            font-weight: 600;
        }

        .forum-actions {
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            position: relative;
            z-index: 1;
        }
        .btn-small {
            font-size:.8rem;
            border-radius:999px;
            padding:8px 14px;
            border:none;
            text-decoration:none;
            cursor:pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .btn-view {
            background:linear-gradient(135deg,var(--ma-primary),var(--ma-secondary));
            color:#fff;
            box-shadow: 0 4px 12px rgba(255,77,240,0.4);
        }
        .btn-report {
            background:linear-gradient(135deg, var(--ma-warning), #ffb84d);
            color:#2b163b;
            box-shadow: 0 4px 12px rgba(255,202,95,0.3);
        }
        .btn-delete {
            background:linear-gradient(135deg, var(--ma-danger), #ff6b7a);
            color:#fff;
            box-shadow: 0 4px 12px rgba(255,75,92,0.4);
        }
        .btn-small:hover {
            transform:translateY(-2px) scale(1.05);
            filter:brightness(1.1);
        }
        .btn-view:hover {
            box-shadow: 0 6px 20px rgba(255,77,240,0.5);
        }
        .btn-report:hover {
            box-shadow: 0 6px 20px rgba(255,202,95,0.5);
        }
        .btn-delete:hover {
            box-shadow: 0 6px 20px rgba(255,75,92,0.5);
        }

        /* FOOTER ****************************************************/
        footer {
            background: linear-gradient(180deg, transparent, rgba(8,22,36,0.8));
            text-align:center;
            padding:40px 10px;
            margin-top:60px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        footer p {
            margin:0;
            color:#999;
            font-size: 0.9rem;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.4rem;
                letter-spacing: 1px;
            }
            .section-title h2 {
                font-size: 2rem;
            }
            .forums-wrapper {
                padding: 0 15px;
            }
            .forum-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .hero-btns {
                flex-direction: column;
                width: 100%;
            }
            .btn-neon, .btn-neon-outline {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>

<?php
// header du front (chemin simple, qui chez toi fonctionnait)
@include __DIR__ . '/_header.php';
?>

<!-- HERO -->
<section class="hero" id="home">
    <div class="hero-inner">
        <h1 class="hero-title">Welcome to MindArena</h1>
        <p class="hero-sub">
            L'arene communautaire ou les joueurs debattent, partagent, s'entraident et construisent ensemble des espaces de discussion uniques.
        </p>
        <div class="hero-btns">
            <a href="<?= $BASE ?>/index.php?action=forums" class="btn-neon">Parcourir les forums</a>
            <a href="<?= isset($_SESSION['user']) ? $BASE . '/index.php?action=add-forum' : $BASE . '/index.php?action=login' ?>" class="btn-neon-outline">Créer un forum</a>
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
                <i class="fa fa-search"></i>
                <input type="text" id="forumSearch" placeholder="Rechercher un forum (titre ou description)...">
            </div>
            <!-- ✅ bouton vers ajout de forum via index.php -->
            <a href="<?= $BASE ?>/index.php?action=add-forum" class="btn-neon" style="font-size:.85rem;">
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
                            Forum #<?= (int)$f['id'] ?>
                            <?php if (!empty($f['created_by'])): ?>
                                · Créé par <strong><?= htmlspecialchars($f['created_by']) ?></strong>
                            <?php endif; ?>
                        </div>

                        <div class="forum-actions">
                            <!-- 🔹 on laisse publications sur ton ancien script front -->
                            <a class="btn-small btn-view"
                               href="<?= $BASE ?>/index.php?action=publications&forum_id=<?= (int)$f['id'] ?>">
                                Voir les publications
                            </a>

                            <?php if (!empty($f['created_by'])): ?>
                                <a class="btn-small" 
                                   href="<?= $BASE ?>/index.php?action=edit-forum&id=<?= (int)$f['id'] ?>"
                                   style="background:linear-gradient(135deg, #00d0ff, #7b42ff); color:#fff; box-shadow: 0 4px 12px rgba(0,208,255,0.4);">
                                    Modifier
                                </a>
                            <?php endif; ?>

                            <!-- 🔹 signalement front -->
                            <a class="btn-small btn-report"
                               href="<?= $BASE ?>/index.php?action=report&target_type=forum&target_id=<?= (int)$f['id'] ?>">
                                Signaler
                            </a>

                            <!-- 🔹 suppression via index.php (ForumController->delete) -->
                            <?php if (!empty($f['created_by'])): ?>
                                <a class="btn-small btn-delete"
                                   href="<?= $BASE ?>/index.php?action=delete-forum-confirm&id=<?= (int)$f['id'] ?>">
                                    Supprimer
                                </a>
                            <?php endif; ?>
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

// Contexte pour le chatbot si tu l’utilises
window.MA_CHAT_CONTEXT = 'default';
</script>

<?php
// chatbot si tu l’utilises
@include __DIR__ . '/chatbot.php';
?>

</body>
</html>

