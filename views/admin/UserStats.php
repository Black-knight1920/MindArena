<?php
// views/admin/userStats.php
// $overview et $topContributors sont fournis par AdminController::userStats()

$overview = $overview ?? [
    'total_users'        => 0,
    'total_forums'       => 0,
    'total_publications' => 0,
];

$topContributors = $topContributors ?? [];
?>

<style>
    .user-stats-header {
        margin-bottom: 24px;
    }
    .user-stats-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #fff;
    }
    body.light .user-stats-header h1 {
        color: #1a1a1a;
    }
    .user-stats-header p {
        opacity: .75;
        margin: 0;
        font-size: 14px;
        color: #c7c7ff;
    }
    body.light .user-stats-header p {
        color: #6b7280;
    }
    .user-stats-card-label {
        font-size: 13px;
        opacity: .8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #c7c7ff;
        margin-bottom: 8px;
    }
    body.light .user-stats-card-label {
        color: #6b7280;
    }
    .user-stats-card-value {
        font-size: 32px;
        font-weight: 800;
        margin-top: 4px;
        margin-bottom: 4px;
        color: #fff;
        line-height: 1;
    }
    body.light .user-stats-card-value {
        color: #1a1a1a;
    }
    .user-stats-card-sub {
        font-size: 13px;
        opacity: .7;
        margin-top: 6px;
        color: #c7c7ff;
    }
    body.light .user-stats-card-sub {
        color: #6b7280;
    }
    .user-stats-table-title {
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 4px;
    }
    body.light .user-stats-table-title {
        color: #1a1a1a;
    }
    .user-stats-table-subtitle {
        font-size: 13px;
        opacity: .75;
        color: #c7c7ff;
    }
    body.light .user-stats-table-subtitle {
        color: #6b7280;
    }
    .badge.rounded-pill {
        padding: 6px 14px;
        font-weight: 600;
        font-size: 13px;
    }
    body.light .table-glass tbody td {
        color: #1a1a1a;
    }
    body.light .table-glass tbody td strong {
        color: #1a1a1a;
    }
</style>

<div class="user-stats-header">
    <h1>Utilisateurs</h1>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p>
            Vue globale de l'activité de la communauté (créateurs de forums et de publications).
        </p>
    </div>
</div>

<!-- CARTES STATS, STYLE COMME DASHBOARD/FORUMLIST -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="user-stats-card-label">
                        Contributeurs actifs
                    </div>
                    <div class="user-stats-card-value">
                        <?= (int)$overview['total_users'] ?>
                    </div>
                    <div class="user-stats-card-sub">
                        Utilisateurs ayant publié au moins un contenu.
                    </div>
                </div>
                <div style="font-size:30px;opacity:.25;">
                    <i class="ri-user-star-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="user-stats-card-label">
                        Forums créés
                    </div>
                    <div class="user-stats-card-value" style="color:#a5b4ff;">
                        <?= (int)$overview['total_forums'] ?>
                    </div>
                    <div class="user-stats-card-sub">
                        Total des espaces de discussion.
                    </div>
                </div>
                <div style="font-size:30px;opacity:.25;">
                    <i class="ri-folder-3-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="user-stats-card-label">
                        Publications
                    </div>
                    <div class="user-stats-card-value" style="color:#f9a8d4;">
                        <?= (int)$overview['total_publications'] ?>
                    </div>
                    <div class="user-stats-card-sub">
                        Messages et réponses postés.
                    </div>
                </div>
                <div style="font-size:30px;opacity:.25;">
                    <i class="ri-file-text-line"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE TOP CONTRIBUTEURS, MÊME STYLE QUE LES AUTRES LISTES -->
<div class="card-dark">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1 user-stats-table-title">Top contributeurs</h5>
            <p class="mb-0 user-stats-table-subtitle">
                Score = forums × 3 + publications
            </p>
        </div>
    </div>

    <?php if (empty($topContributors)): ?>
        <p style="opacity:.7;margin:0;color:#c7c7ff;">
            Aucun contributeur pour le moment.
        </p>
        <style>
            body.light p[style*="opacity:.7"] {
                color: #6b7280;
            }
        </style>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table-glass">
                <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th class="text-center">Forums</th>
                    <th class="text-center">Publications</th>
                    <th class="text-center">Score</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($topContributors as $row): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($row['name']) ?></strong>
                        </td>
                        <td class="text-center">
                            <?= (int)$row['forums_count'] ?>
                        </td>
                        <td class="text-center">
                            <?= (int)$row['publications_count'] ?>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-primary">
                                <?= (int)$row['score'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
