<?php
// views/front/profile.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$BASE = isset($BASE) ? $BASE : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';

// Redirect to login if not authenticated
if (!isset($_SESSION['user'])) {
    header('Location: ' . $BASE . '/index.php?action=login');
    exit;
}

$user = $_SESSION['user'];
$username = $user['username'] ?? '';

// Normalize optional data provided by the controller
$forumsByUser = $forumsByUser ?? [];
$pubsByUser   = $pubsByUser ?? [];
$userRank     = $userRank ?? null;
$score        = isset($score) ? $score : ($userRank['score'] ?? 0);
$firstForumId = $firstForumId ?? (!empty($forumsByUser) ? (int)($forumsByUser[0]['id'] ?? 0) : null);
$accountData = $accountData ?? null;
$profileData = $profileData ?? null;

// Activity timeline (forums + pubs)
$activities = [];
foreach ($forumsByUser as $f) {
    $activities[] = [
        'type' => 'forum',
        'title' => $f['title'] ?? 'Forum',
        'created_at' => $f['created_at'] ?? null
    ];
}
foreach ($pubsByUser as $p) {
    $activities[] = [
        'type' => 'publication',
        'title' => isset($p['forum_title']) ? ($p['forum_title'] . ' • post') : 'Publication',
        'created_at' => $p['created_at'] ?? null,
        'content' => $p['content'] ?? ''
    ];
}
usort($activities, function ($a, $b) {
    return strtotime($b['created_at'] ?? '') <=> strtotime($a['created_at'] ?? '');
});
$activities = array_slice($activities, 0, 8);

$score = $userRank['score'] ?? ((count($forumsByUser) * 3) + count($pubsByUser));
$rankLabel = $userRank ? '#' . (int)$userRank['rank'] : 'New';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil - <?= htmlspecialchars($user['username'] ?? 'Utilisateur') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= BASE_URL ?>/ENDGAME/img/favicon.ico" rel="shortcut icon" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="<?= BASE_URL ?>/ENDGAME/css/style.css"/>
    <style>
        :root {
            --glass: rgba(10,14,28,0.78);
            --border: rgba(255,255,255,0.12);
            --accent: #ff4df0;
            --accent2: #7b2ff7;
            --cyan: #22d3ee;
            --muted: #cbd5f5;
            --bg: radial-gradient(ellipse at top, #5b1f9c 0%, #2a0f4a 25%, #0f0a1f 55%, #070512 100%);
        }
        body {
            background: var(--bg);
            background-attachment: fixed;
            color: #fff;
            padding-top: 110px;
            overflow-x: hidden;
            font-family: "Roboto", sans-serif;
        }
        .layered {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }
        .orb {
            position: absolute;
            width: 360px; height: 360px;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.35;
            animation: float 12s ease-in-out infinite alternate;
            mix-blend-mode: screen;
        }
        .orb.one { background: #7b2ff7; top: -90px; left: -80px; }
        .orb.two { background: #ff4df0; top: 20%; right: -160px; animation-delay: 2s; }
        .orb.three { background: #22d3ee; bottom: -160px; left: 15%; animation-delay: 1s; }
        @keyframes float {
            from { transform: translateY(0) scale(1); }
            to   { transform: translateY(-25px) scale(1.04); }
        }
        .profile-hero { padding: 120px 0 80px; position: relative; }
        .glass {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 18px 38px rgba(0,0,0,0.42), 0 0 0 1px rgba(255,255,255,0.03);
            backdrop-filter: blur(8px);
        }
        .profile-card { padding: 28px; }
        .profile-panel { padding: 18px; }
        .content-section { padding: 16px; }
        .profile-top { display:grid; grid-template-columns:1.15fr 0.85fr; gap:20px; align-items:center; }
        .badge-chip { display:inline-flex; gap:8px; padding:8px 12px; border-radius:12px; background:linear-gradient(135deg, rgba(255,77,240,0.2), rgba(123,47,247,0.22)); border:1px solid rgba(255,255,255,0.12); font-weight:700; letter-spacing:0.25px; box-shadow: 0 10px 24px rgba(255,77,240,0.25); }
        .meta-chips { display:flex; flex-wrap:wrap; gap:8px; margin:10px 0 6px; }
        .meta-chip { padding:6px 10px; border-radius:10px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); font-size:12px; color:#dce1ff; }
        .mini-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px; margin-top:10px; }
        .mini-card { padding:14px 16px; border-radius:14px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.12); position:relative; overflow:hidden; }
        .mini-card::after { content:''; position:absolute; right:-24px; top:-24px; width:70px; height:70px; background:linear-gradient(135deg, rgba(255,77,240,0.2), rgba(123,47,247,0.14)); border-radius:50%; filter: blur(12px); opacity:0.55; }
        .mini-card label { display:block; font-size:12px; letter-spacing:0.3px; color:#b8c0d1; margin-bottom:6px; font-weight:700; }
        .mini-card .value { font-size:24px; font-weight:900; color:#fff; }
        .halo-badge {
            width: 240px;
            max-width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 26px;
            background: radial-gradient(circle at 30% 30%, rgba(255,77,240,0.4), transparent 50%), radial-gradient(circle at 70% 60%, rgba(34,211,238,0.35), transparent 55%), rgba(10,16,31,0.9);
            border: 1px solid rgba(255,255,255,0.14);
            display: grid;
            place-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.05);
        }
        .halo-ring { width: 86%; height: 86%; border-radius: 50%; border: 1px dashed rgba(255,255,255,0.28); display: grid; place-items: center; animation: pulse 4s ease-in-out infinite; }
        @keyframes pulse { 0% { transform: scale(1); opacity: 0.85; } 50% { transform: scale(1.05); opacity: 1; } 100% { transform: scale(1); opacity: 0.85; } }
        .halo-rank { text-align: center; }
        .halo-rank .rank-number { font-size: 36px; font-weight: 900; color: #fff; }
        .halo-rank small { display:block; color:#cbd5f5; letter-spacing:1px; text-transform:uppercase; }
        .stat-pill { padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.12); background:rgba(255,255,255,0.05); color:#fff; font-weight:800; margin-top: 12px; }
        .profile-actions .site-btn { min-width:160px; }
        .quick-actions { display:flex; flex-wrap:wrap; gap:10px; margin-top:12px; }
        .quick-actions a { padding:10px 14px; border-radius:12px; text-decoration:none; font-weight:700; font-size:13px; color:#fff; }
        .qa-primary { background:linear-gradient(135deg,#ff4df0,#7b2ff7); box-shadow:0 8px 18px rgba(255,77,240,0.35); }
        .qa-secondary { background:linear-gradient(135deg,#0f172a,#111827); border:1px solid rgba(255,255,255,0.12); color:#d1d5db; }
        .content-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:16px; margin-top:16px; }
        .panel-header { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:6px; }
        .panel-kicker { padding:4px 10px; border-radius:8px; background:rgba(255,255,255,0.06); font-size:12px; color:#cbd5f5; }
        .pill-count { padding:6px 10px; border-radius:10px; background:rgba(34,211,238,0.14); border:1px solid rgba(34,211,238,0.35); color: #a5f3fc; font-weight:700; font-size:12px; }
        .profile-item { padding:12px; border-radius:12px; border:1px solid rgba(255,255,255,0.07); background:rgba(255,255,255,0.03); }
        .empty-state { padding:16px; border-radius:12px; border:1px dashed rgba(255,255,255,0.2); color:#b8c0d1; text-align:center; background:rgba(255,255,255,0.02); }
        .timeline { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px; }
        .timeline-item { padding:12px; border-radius:12px; border:1px solid rgba(255,255,255,0.08); background:rgba(255,255,255,0.03); }
        .timeline-item .type { font-size:12px; color:#cbd5f5; text-transform:uppercase; letter-spacing:0.4px; }
        .timeline-item .title { font-weight:700; color:#f5e7ff; }
        .timeline-item .date { font-size:12px; color:#94a3b8; }
        .progress-wrap { margin-top: 8px; }
        .progress-label { display:flex; justify-content:space-between; font-size:12px; color:#cbd5f5; }
        .progress-bar { width:100%; height:8px; border-radius:999px; background:rgba(255,255,255,0.08); overflow:hidden; }
        .progress-bar span { display:block; height:100%; background:linear-gradient(135deg,#ff4df0,#22d3ee); }
    </style>
</head>
<body>

<?php @include __DIR__ . '/_header.php'; ?>

<section class="profile-hero">
    <div class="layered">
        <div class="orb one"></div>
        <div class="orb two"></div>
        <div class="orb three"></div>
    </div>
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="glass profile-card mb-4">
                    <div class="profile-top">
                        <div>
                            <div class="badge-chip mb-2">Profil</div>
                            <h3 class="mb-1" style="color:#fff;"><?= htmlspecialchars($user['username'] ?? '') ?></h3>
                            <div class="meta-chips">
                                <span class="meta-chip">Rôle : <?= htmlspecialchars($user['role'] ?? '') ?></span>
                                <span class="meta-chip">Score : <?= (int)$score ?></span>
                                <span class="meta-chip">Forums : <?= count($forumsByUser) ?></span>
                                <span class="meta-chip">Pubs : <?= count($pubsByUser) ?></span>
                            </div>
                            <div class="mini-stats mt-2">
                                <div class="mini-card"><label>Forums</label><div class="value"><?= count($forumsByUser) ?></div></div>
                                <div class="mini-card"><label>Publications</label><div class="value"><?= count($pubsByUser) ?></div></div>
                                <div class="mini-card"><label>Score</label><div class="value"><?= (int)$score ?></div></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="halo-badge">
                                <div class="halo-ring">
                                    <div class="halo-rank">
                                        <div class="rank-number"><?= htmlspecialchars($rankLabel) ?></div>
                                        <small><?= $userRank ? 'Classement' : 'Bienvenue' ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-pill mb-3 mt-3">
                        <?= $userRank ? 'Rang #' . (int)$userRank['rank'] : 'Pas encore classé' ?>
                        <span style="display:block;color:#b8c0d1;font-size:13px;font-weight:500;">Score: <?= (int)$score ?> • Forums: <?= count($forumsByUser) ?> • Pubs: <?= count($pubsByUser) ?></span>
                    </div>

                    <div class="profile-actions d-flex flex-wrap gap-2 mt-2">
                        <a class="site-btn sb-gradients" href="<?= $BASE ?>/logout.php">Déconnexion</a>
                        <a class="site-btn alt" href="<?= $BASE ?>/index.php?action=home">Retour Accueil</a>
                        <?php if (($user['role'] ?? '') === 'admin'): ?>
                            <a class="site-btn alt" style="background:#1d4ed8;" href="<?= $BASE ?>/admin.php?action=dashboard">Admin</a>
                        <?php endif; ?>
                    </div>
                    <div class="quick-actions">
                        <a class="qa-primary" href="<?= $BASE ?>/index.php?action=add-forum"><i class="fa fa-plus"></i> Nouveau forum</a>
                        <a class="qa-secondary" href="<?= $BASE ?>/index.php?action=forums"><i class="fa fa-list"></i> Voir forums</a>
                        <?php $firstForumId = isset($forumsByUser[0]['id']) ? (int)$forumsByUser[0]['id'] : null; ?>
                        <a class="qa-secondary" href="<?= $firstForumId ? $BASE . '/index.php?action=publications&forum_id=' . $firstForumId : '#' ?>" style="<?= $firstForumId ? '' : 'opacity:0.6;pointer-events:none;' ?>"><i class="fa fa-comments"></i> Mes publications</a>
                    </div>

                    <?php if (!empty($accountData) || !empty($profileData)): ?>
                        <div class="glass profile-panel mt-3">
                            <div class="d-flex flex-wrap gap-3">
                                <div><div class="text-muted" style="font-size:13px;">Email</div><div style="font-weight:700;"><?= htmlspecialchars($profileData['email'] ?? $accountData['email'] ?? '') ?></div></div>
                                <div><div class="text-muted" style="font-size:13px;">Nais.</div><div style="font-weight:700;"><?= htmlspecialchars($accountData['birth_date'] ?? '—') ?></div></div>
                                <div><div class="text-muted" style="font-size:13px;">Inscrit le</div><div style="font-weight:700;"><?= htmlspecialchars($accountData['signup_date'] ?? '—') ?></div></div>
                                <div><div class="text-muted" style="font-size:13px;">Donations</div><div style="font-weight:700;"><?= isset($accountData['donation']) ? (int)$accountData['donation'] : 0 ?> pts</div></div>
                            </div>
                            <?php if (!empty($profileData['bio'])): ?>
                                <div class="mt-2" style="color:#cbd5f5;"><?= nl2br(htmlspecialchars($profileData['bio'])) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="content-grid">
                        <div class="glass content-section">
                            <div class="panel-header mb-2">
                                <h4><span class="panel-kicker">Forums</span> Forums créés</h4>
                                <span class="pill-count"><?= count($forumsByUser) ?></span>
                            </div>
                            <?php if (!empty($forumsByUser)): ?>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($forumsByUser as $forum): ?>
                                        <li class="profile-item mb-3">
                                            <a href="<?= $BASE ?>/index.php?action=forums#forum-<?= (int)$forum['id'] ?>" style="color:#f4e9ff; font-weight:700;"><?= htmlspecialchars($forum['title']) ?></a>
                                            <div><small style="color:#b8c0d1;">Créé le <?= htmlspecialchars($forum['created_at'] ?? '') ?></small></div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="empty-state">Aucun forum créé.</div>
                            <?php endif; ?>
                        </div>

                        <div class="glass content-section">
                            <div class="panel-header mb-2">
                                <h4><span class="panel-kicker">Posts</span> Publications</h4>
                                <span class="pill-count"><?= count($pubsByUser) ?></span>
                            </div>
                            <?php if (!empty($pubsByUser)): ?>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($pubsByUser as $pub): ?>
                                        <?php $content = (string)($pub['content'] ?? ''); $snippet = strlen($content) > 120 ? substr($content, 0, 120) . '...' : $content; ?>
                                        <li class="profile-item mb-3">
                                            <div style="color:#e5e7eb;"><?= htmlspecialchars($snippet) ?></div>
                                            <div><small style="color:#b8c0d1;">Forum: <?= htmlspecialchars($pub['forum_title'] ?? '') ?> • Publié le <?= htmlspecialchars($pub['created_at'] ?? '') ?></small></div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="empty-state">Aucune publication.</div>
                            <?php endif; ?>
                        </div>

                        <div class="glass content-section">
                            <div class="panel-header mb-2">
                                <h4><span class="panel-kicker">Timeline</span> Activité récente</h4>
                            </div>
                            <?php if (!empty($activities)): ?>
                                <ul class="timeline">
                                    <?php foreach ($activities as $act): ?>
                                        <li class="timeline-item">
                                            <div class="type"><?= htmlspecialchars($act['type']) ?></div>
                                            <div class="title"><?= htmlspecialchars($act['title']) ?></div>
                                            <?php if (!empty($act['created_at'])): ?>
                                                <div class="date"><?= htmlspecialchars($act['created_at']) ?></div>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="empty-state">Aucune activité récente.</div>
                            <?php endif; ?>
                        </div>

                        <div class="glass content-section">
                            <div class="panel-header mb-2">
                                <h4><span class="panel-kicker">Progress</span> Engagement</h4>
                            </div>
                            <div class="progress-wrap">
                                <div class="progress-label"><span>Forums</span><span><?= count($forumsByUser) ?></span></div>
                                <div class="progress-bar"><span style="width: <?= min(100, count($forumsByUser) * 4) ?>%;"></span></div>
                            </div>
                            <div class="progress-wrap">
                                <div class="progress-label"><span>Publications</span><span><?= count($pubsByUser) ?></span></div>
                                <div class="progress-bar"><span style="width: <?= min(100, count($pubsByUser) * 3) ?>%;"></span></div>
                            </div>
                            <div class="progress-wrap">
                                <div class="progress-label"><span>Score</span><span><?= (int)$score ?></span></div>
                                <div class="progress-bar"><span style="width: <?= min(100, $score) ?>%;"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php @include __DIR__ . '/_footer.php'; ?>

<script src="<?= BASE_URL ?>/ENDGAME/js/jquery-3.2.1.min.js"></script>
<script src="<?= BASE_URL ?>/ENDGAME/js/bootstrap.min.js"></script>
<script src="<?= BASE_URL ?>/ENDGAME/js/jquery.slicknav.js"></script>
<script src="<?= BASE_URL ?>/ENDGAME/js/owl.carousel.min.js"></script>
<script src="<?= BASE_URL ?>/ENDGAME/js/main.js"></script>
</body>
</html>

