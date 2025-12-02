<?php
// views/front/publicationList.php
// $forum et $publications fournis par PublicationController::listFront()
if (!isset($forum) || !$forum) {
    die("<h2 style='color:white;text-align:center;margin-top:50px'>❌ Forum introuvable</h2>");
}
$publications = $publications ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($forum['title']) ?> - Publications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="/mindarena_forum/ENDGAME/img/favicon.ico" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/font-awesome.min.css">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/animate.css">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/style.css">

    <style>
        :root {
            --ma-bg: #160820;
            --ma-card: rgba(8,22,36,0.95);
            --ma-border: rgba(255,255,255,0.10);
            --ma-accent: #ff4df0;
            --ma-accent-soft: #b01ba5;
            --ma-warning: #ffca5f;
            --ma-danger: #ff4b5c;
            --ma-primary-glow: rgba(255,77,240,0.6);
            --ma-secondary-glow: rgba(123,47,247,0.4);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Roboto", sans-serif;
            background: radial-gradient(ellipse at top, #4d1b7d 0%, #2a0f4a 25%, #160820 50%, #0a0515 100%);
            background-attachment: fixed;
            color: #fff;
            padding-top: 110px;
            overflow-x: hidden;
        }

        /* FORUM HEADER SECTION */
        .forum-header-section {
            text-align: center;
            padding: 60px 20px 40px;
            position: relative;
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
        .forum-header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--ma-accent), transparent);
            border-radius: 2px;
        }
        .forum-header-section h1 {
            font-size: 3rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #ff4df0, #ffb8ff, #7b2ff7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(255,77,240,0.3);
            line-height: 1.2;
        }
        .forum-header-section .forum-description {
            font-size: 1.1rem;
            color: #e1d7ff;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto 30px;
            line-height: 1.6;
        }
        .forum-actions-bar {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        /* BUTTONS */
        .btn-neon {
            padding: 14px 32px;
            border-radius: 999px;
            border: none;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #ff4df0, #b01ba5);
            box-shadow: 0 0 20px rgba(255,77,240,0.6),
                        0 4px 15px rgba(255,77,240,0.3),
                        inset 0 1px 0 rgba(255,255,255,0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 0 30px rgba(255,77,240,0.9),
                        0 8px 25px rgba(255,77,240,0.4);
        }
        .btn-neon-outline {
            padding: 14px 32px;
            border-radius: 999px;
            border: 2px solid #ff4df0;
            text-decoration: none;
            color: #ffb8ff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            background: rgba(8,22,36,0.7);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 15px rgba(255,77,240,0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-neon-outline:hover {
            background: linear-gradient(135deg, #ff4df0, #b01ba5);
            color: #160819;
            transform: translateY(-3px);
            box-shadow: 0 0 25px rgba(255,77,240,0.7);
        }

        /* SEARCH BAR */
        .search-bar-wrapper {
            max-width: 1000px;
            margin: 0 auto 30px;
            padding: 0 20px;
        }
        .search-bar {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        .search-bar input {
            width: 100%;
            padding: 14px 20px 14px 50px;
            border-radius: 999px;
            border: 2px solid rgba(255,255,255,0.2);
            background: rgba(8,22,36,0.95);
            backdrop-filter: blur(10px);
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .search-bar input:focus {
            border-color: #ff4df0;
            box-shadow: 0 0 25px rgba(255,77,240,0.5),
                        0 4px 20px rgba(0,0,0,0.3);
            background: rgba(8,22,36,1);
        }
        .search-bar input::placeholder {
            color: rgba(222,215,255,0.6);
        }
        .search-bar i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #ff4df0;
            font-size: 1rem;
            pointer-events: none;
        }
        .search-bar:focus-within i {
            color: #ffb8ff;
        }

        /* PUBLICATIONS CONTAINER */
        .publications-container {
            max-width: 1000px;
            margin: 0 auto 80px;
            padding: 0 20px;
        }

        /* PUBLICATION CARD */
        .pub-card {
            background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
            border-radius: 20px;
            padding: 24px 22px 22px;
            margin-bottom: 24px;
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: 0 25px 50px rgba(5,0,20,0.7),
                        inset 0 1px 0 rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }
        .pub-card:nth-child(1) { animation-delay: 0.1s; }
        .pub-card:nth-child(2) { animation-delay: 0.2s; }
        .pub-card:nth-child(3) { animation-delay: 0.3s; }
        .pub-card:nth-child(4) { animation-delay: 0.4s; }
        .pub-card:nth-child(5) { animation-delay: 0.5s; }
        .pub-card::before {
            content: '';
            position: absolute;
            inset: -30%;
            background: radial-gradient(circle at top left, rgba(255,77,240,0.3), transparent 60%),
                        radial-gradient(circle at bottom right, rgba(123,47,247,0.25), transparent 60%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .pub-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff4df0, #7b2ff7, #ff4df0);
            background-size: 200% 100%;
            opacity: 0;
            transition: opacity 0.3s ease;
            animation: shimmer 2s linear infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .pub-card:hover::before {
            opacity: 1;
        }
        .pub-card:hover::after {
            opacity: 1;
        }
        .pub-card:hover {
            transform: translateY(-6px) scale(1.01);
            border-color: rgba(255,77,240,0.4);
            box-shadow: 0 35px 70px rgba(0,0,0,0.8),
                        0 0 40px rgba(255,77,240,0.4),
                        inset 0 1px 0 rgba(255,255,255,0.15);
        }

        .pub-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            gap: 12px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }
        .pub-author-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .pub-author-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff4df0, #b01ba5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
            box-shadow: 0 4px 15px rgba(255,77,240,0.4);
        }
        .pub-author-info {
            display: flex;
            flex-direction: column;
        }
        .pub-author {
            font-weight: 700;
            font-size: 1.05rem;
            color: #ff9cff;
            margin-bottom: 2px;
            transition: color 0.3s;
        }
        .pub-card:hover .pub-author {
            color: #ffb8ff;
            text-shadow: 0 0 10px rgba(255,77,240,0.5);
        }
        .pub-date {
            font-size: 0.85rem;
            opacity: 0.75;
            color: #d4c5ff;
        }
        .pub-id-badge {
            font-size: 0.75rem;
            opacity: 0.75;
            background: rgba(255,255,255,0.1);
            padding: 4px 10px;
            border-radius: 10px;
            font-weight: 600;
        }

        .pub-content {
            font-size: 1rem;
            line-height: 1.7;
            color: #e1d7ff;
            opacity: 0.95;
            margin-bottom: 18px;
            position: relative;
            z-index: 1;
            word-wrap: break-word;
        }

        .pub-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
            position: relative;
            z-index: 1;
        }
        .btn-small {
            font-size: 0.8rem;
            border-radius: 999px;
            padding: 8px 16px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .btn-report {
            background: linear-gradient(135deg, var(--ma-warning), #ffb84d);
            color: #2b163b;
            box-shadow: 0 4px 12px rgba(255,202,95,0.3);
        }
        .btn-report:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(255,202,95,0.5);
        }
        .btn-delete {
            background: linear-gradient(135deg, var(--ma-danger), #ff6b7a);
            color: #fff;
            box-shadow: 0 4px 12px rgba(255,75,92,0.4);
        }
        .btn-delete:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(255,75,92,0.5);
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(135deg, rgba(8,22,36,0.6), rgba(15,30,50,0.4));
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.1);
            margin-top: 40px;
            animation: fadeInUp 0.8s ease-out;
        }
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.8;
        }
        .empty-state p {
            font-size: 1.2rem;
            color: #d4c5ff;
            margin-bottom: 24px;
            opacity: 0.9;
        }

        /* FOOTER */
        footer {
            background: linear-gradient(180deg, transparent, rgba(8,22,36,0.8));
            text-align: center;
            padding: 40px 10px;
            margin-top: 60px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        footer p {
            color: #999;
            margin: 0;
            font-size: 0.9rem;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .forum-header-section h1 {
                font-size: 2.2rem;
                letter-spacing: 1px;
            }
            .forum-actions-bar {
                flex-direction: column;
                width: 100%;
            }
            .btn-neon, .btn-neon-outline {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
            .publications-container {
                padding: 0 15px;
            }
            .pub-card {
                padding: 20px 18px 18px;
            }
            .pub-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .forum-header-section h1 {
                font-size: 1.8rem;
            }
            .pub-author-wrapper {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<!-- FORUM HEADER -->
<section class="forum-header-section">
    <h1><?= htmlspecialchars($forum['title']) ?></h1>
    
    <?php if (!empty($forum['description'])): ?>
        <p class="forum-description"><?= nl2br(htmlspecialchars($forum['description'])) ?></p>
    <?php endif; ?>

    <div class="forum-actions-bar">
        <a href="index.php?action=forums" class="btn-neon-outline">
            <i class="fa fa-arrow-left"></i>
            Retour aux Forums
        </a>
        <a href="index.php?action=add-publication&forum_id=<?= (int)$forum['id'] ?>" class="btn-neon">
            <i class="fa fa-plus"></i>
            Nouvelle publication
        </a>
    </div>
</section>

<!-- SEARCH BAR -->
<?php if (!empty($publications)): ?>
    <div class="search-bar-wrapper">
        <div class="search-bar">
            <i class="fa fa-search"></i>
            <input type="text" id="publicationSearch" placeholder="Rechercher dans les publications...">
        </div>
    </div>
<?php endif; ?>

<!-- PUBLICATIONS LIST -->
<div class="publications-container" id="publicationsList">
    <?php if (empty($publications)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">💬</div>
            <p>Aucune publication pour le moment.</p>
            <p style="font-size: 1rem; margin-top: -10px;">Sois le premier à participer à la discussion !</p>
            <a href="index.php?action=add-publication&forum_id=<?= (int)$forum['id'] ?>" class="btn-neon" style="margin-top: 20px;">
                <i class="fa fa-plus"></i>
                Créer la première publication
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($publications as $index => $p): ?>
            <article class="pub-card" 
                     data-author="<?= strtolower(htmlspecialchars($p['author'])) ?>"
                     data-content="<?= strtolower(htmlspecialchars($p['content'])) ?>"
                     style="animation-delay: <?= $index * 0.1 ?>s;">
                <div class="pub-header">
                    <div class="pub-author-wrapper">
                        <div class="pub-author-avatar">
                            <?= strtoupper(mb_substr(htmlspecialchars($p['author']), 0, 1)) ?>
                        </div>
                        <div class="pub-author-info">
                            <div class="pub-author"><?= htmlspecialchars($p['author']) ?></div>
                            <div class="pub-date">
                                <i class="fa fa-clock-o"></i>
                                <?= date("d/m/Y à H:i", strtotime($p['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                    <div class="pub-id-badge">
                        #<?= (int)$p['id'] ?>
                    </div>
                </div>

                <div class="pub-content">
                    <?= nl2br(htmlspecialchars($p['content'])) ?>
                </div>

                <div class="pub-actions">
                    <?php if (!empty($p['author'])): ?>
                        <a href="index.php?action=edit-publication&id=<?= (int)$p['id'] ?>"
                           class="btn-small"
                           style="background:linear-gradient(135deg, #00d0ff, #7b42ff); color:#fff; box-shadow: 0 4px 12px rgba(0,208,255,0.4);">
                            <i class="fa fa-edit"></i>
                            Modifier
                        </a>
                    <?php endif; ?>
                    <a href="index.php?action=report&target_type=publication&target_id=<?= (int)$p['id'] ?>"
                       class="btn-small btn-report">
                        <i class="fa fa-flag"></i>
                        Signaler
                    </a>
                    <?php if (!empty($p['author'])): ?>
                        <a href="index.php?action=delete-publication-confirm&id=<?= (int)$p['id'] ?>&forum_id=<?= (int)$forum['id'] ?>"
                           class="btn-small btn-delete">
                            <i class="fa fa-trash"></i>
                            Supprimer
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<footer>
    <p>© <?= date('Y') ?> MindArena — Built for gamers, by gamers.</p>
</footer>

<script>
// Search functionality
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('publicationSearch');
    const cards = document.querySelectorAll('#publicationsList .pub-card');

    if (searchInput && cards.length) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            cards.forEach(card => {
                const author = card.dataset.author || '';
                const content = card.dataset.content || '';
                const match = author.includes(query) || content.includes(query);
                card.style.display = match ? '' : 'none';
            });
        });
    }

    window.MA_CHAT_CONTEXT = 'publications';
});
</script>

<?php include __DIR__ . '/chatbot.php'; ?>

</body>
</html>
