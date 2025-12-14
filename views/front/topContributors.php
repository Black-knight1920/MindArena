<?php
// views/front/topContributors.php
// Page affichant les top contributeurs

$topContributors = $topContributors ?? [];
$overview = $overview ?? [
    'total_users' => 0,
    'total_forums' => 0,
    'total_publications' => 0
];

// Base URL calculée automatiquement
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Top Contributeurs - MindArena</title>
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
            --ma-bg: #160820;
            --ma-card: rgba(8,22,36,0.95);
            --ma-border: rgba(255,255,255,0.10);
            --ma-accent: #ff4df0;
            --ma-accent-soft: #b01ba5;
            --ma-primary-glow: rgba(255,77,240,0.6);
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

        .page-header {
            text-align: center;
            padding: 60px 15px 40px;
            position: relative;
        }

        .page-header::before {
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

        .page-header h1 {
            font-size: 3rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            background: linear-gradient(135deg, #ff4df0, #ffb8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            text-shadow: 0 0 30px rgba(255,77,240,0.3);
        }

        .page-header p {
            color: #e1d7ff;
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .contributors-wrapper {
            max-width: 1000px;
            margin: 0 auto 60px;
            padding: 0 15px;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        @media (max-width: 768px) {
            .stats-cards {
                grid-template-columns: 1fr;
            }
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
            border-radius: 18px;
            padding: 24px 20px;
            border: 1px solid var(--ma-border);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.8), 0 0 40px rgba(255,77,240,0.3);
        }

        .stat-card-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #c7c7ff;
            opacity: 0.8;
            margin-bottom: 8px;
        }

        .stat-card-value {
            font-size: 2.5rem;
            font-weight: 900;
            color: #ff4df0;
            margin-bottom: 4px;
        }

        .stat-card-sub {
            font-size: 0.9rem;
            color: #d4c5ff;
            opacity: 0.7;
        }

        .contributors-table-card {
            background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
            border-radius: 18px;
            padding: 30px;
            border: 1px solid var(--ma-border);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        .table-header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid rgba(255,77,240,0.3);
        }

        .table-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff9cff;
            margin-bottom: 6px;
        }

        .table-header p {
            color: #c7c7ff;
            font-size: 0.95rem;
            opacity: 0.8;
            margin: 0;
        }

        .contributors-table {
            width: 100%;
            border-collapse: collapse;
        }

        .contributors-table thead tr {
            background: rgba(255,77,240,0.1);
            border-bottom: 2px solid rgba(255,77,240,0.3);
        }

        .contributors-table th {
            padding: 14px 12px;
            text-align: left;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffb8ff;
            font-weight: 700;
        }

        .contributors-table th.rank {
            width: 60px;
            text-align: center;
        }

        .contributors-table th.score {
            text-align: right;
        }

        .contributors-table tbody tr {
            border-bottom: 1px solid rgba(255,255,255,0.05);
            transition: all 0.2s;
        }

        .contributors-table tbody tr:hover {
            background: rgba(255,77,240,0.08);
            transform: translateX(4px);
        }

        .contributors-table td {
            padding: 16px 12px;
            color: #e1d7ff;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-weight: 900;
            font-size: 1rem;
            background: linear-gradient(135deg, #ff4df0, #b01ba5);
            color: #fff;
            box-shadow: 0 4px 12px rgba(255,77,240,0.5);
        }

        .rank-badge.gold {
            background: linear-gradient(135deg, #ffd700, #ffb84d);
            box-shadow: 0 4px 12px rgba(255,215,0,0.5);
        }

        .rank-badge.silver {
            background: linear-gradient(135deg, #c0c0c0, #a0a0a0);
            box-shadow: 0 4px 12px rgba(192,192,192,0.5);
        }

        .rank-badge.bronze {
            background: linear-gradient(135deg, #cd7f32, #b87333);
            box-shadow: 0 4px 12px rgba(205,127,50,0.5);
        }

        .contributor-name {
            font-weight: 700;
            font-size: 1.05rem;
            color: #ff9cff;
        }

        .contributor-stats {
            display: flex;
            gap: 16px;
            font-size: 0.9rem;
            color: #c7c7ff;
        }

        .contributor-stats span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .contributor-stats i {
            color: #ff4df0;
        }

        .score-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 1.1rem;
            background: linear-gradient(135deg, rgba(255,77,240,0.2), rgba(176,27,165,0.2));
            border: 1px solid rgba(255,77,240,0.4);
            color: #ffb8ff;
            box-shadow: 0 4px 12px rgba(255,77,240,0.3);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 30px;
            color: #ff9cff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: #ffb8ff;
            transform: translateX(-4px);
        }

        footer {
            background: linear-gradient(180deg, transparent, rgba(8,22,36,0.8));
            text-align: center;
            padding: 40px 10px;
            margin-top: 60px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        footer p {
            margin: 0;
            color: #999;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<div class="page-header">
    <h1>🏆 Top Contributeurs</h1>
    <p>Découvre les membres les plus actifs de la communauté MindArena</p>
</div>

<div class="contributors-wrapper">
    <a href="<?= $BASE ?>/index.php?action=home" class="back-link">
        <i class="fa fa-arrow-left"></i> Retour à l'accueil
    </a>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-card-label">Contributeurs actifs</div>
            <div class="stat-card-value"><?= (int)$overview['total_users'] ?></div>
            <div class="stat-card-sub">Membres ayant publié</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Forums créés</div>
            <div class="stat-card-value"><?= (int)$overview['total_forums'] ?></div>
            <div class="stat-card-sub">Espaces de discussion</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Publications</div>
            <div class="stat-card-value"><?= (int)$overview['total_publications'] ?></div>
            <div class="stat-card-sub">Messages postés</div>
        </div>
    </div>

    <!-- Contributors Table -->
    <div class="contributors-table-card">
        <div class="table-header">
            <h2>Classement des contributeurs</h2>
            <p>Score = (Forums × 3) + Publications</p>
        </div>

        <?php if (empty($topContributors)): ?>
            <p style="text-align: center; color: #c7c7ff; opacity: 0.7; padding: 40px;">
                Aucun contributeur pour le moment. Sois le premier à créer un forum ou publier un message !
            </p>
        <?php else: ?>
            <table class="contributors-table">
                <thead>
                    <tr>
                        <th class="rank">#</th>
                        <th>Contributeur</th>
                        <th>Activité</th>
                        <th class="score">Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topContributors as $index => $contributor): ?>
                        <?php
                        $rank = $index + 1;
                        $rankClass = '';
                        if ($rank === 1) $rankClass = 'gold';
                        elseif ($rank === 2) $rankClass = 'silver';
                        elseif ($rank === 3) $rankClass = 'bronze';
                        ?>
                        <tr>
                            <td style="text-align: center;">
                                <span class="rank-badge <?= $rankClass ?>">
                                    <?= $rank ?>
                                </span>
                            </td>
                            <td>
                                <div class="contributor-name">
                                    <?= htmlspecialchars($contributor['name']) ?>
                                </div>
                            </td>
                            <td>
                                <div class="contributor-stats">
                                    <span>
                                        <i class="fa fa-folder"></i>
                                        <?= (int)$contributor['forums_count'] ?> forum<?= (int)$contributor['forums_count'] > 1 ? 's' : '' ?>
                                    </span>
                                    <span>
                                        <i class="fa fa-comment"></i>
                                        <?= (int)$contributor['publications_count'] ?> publication<?= (int)$contributor['publications_count'] > 1 ? 's' : '' ?>
                                    </span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <span class="score-badge">
                                    <?= (int)$contributor['score'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>© <?= date('Y') ?> MindArena — Built for gamers, by gamers.</p>
</footer>

<?php include __DIR__ . '/chatbot.php'; ?>

</body>
</html>








