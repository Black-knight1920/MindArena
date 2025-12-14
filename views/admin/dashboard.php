<?php
// Safety + derived stats
$totalForums            = $totalForums            ?? 0;
$totalPublications      = $totalPublications      ?? 0;
$totalReports           = $totalReports           ?? 0;
$pendingReports         = $pendingReports         ?? 0;
$resolvedReports        = $resolvedReports        ?? max(0, $totalReports - $pendingReports);
$totalRegisteredUsers   = $totalRegisteredUsers   ?? 0;
$newUsers               = $newUsers               ?? 0;
$activeDonors           = $activeDonors           ?? 0;
$recentUsers            = $recentUsers            ?? [];
$usersCount             = $usersCount             ?? 0;

if ($totalReports > 0) {
    $moderationRate = round(($resolvedReports / $totalReports) * 100);
} else {
    $moderationRate = 100;
}
?>
<style>
/* ========= DASHBOARD 2.0 ========= */

.dashboard-wrapper {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* top title row (optional – layout already has title) */
.dashboard-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 4px;
}
.dashboard-header-row small {
    opacity: .7;
    font-size: 12px;
    color: #c7c7ff;
}
body.light .dashboard-header-row small {
    color: #6b7280;
}
.dashboard-header-row h2 {
    color: #fff;
}
body.light .dashboard-header-row h2 {
    color: #1a1a1a;
}

/* generic card */
.admin-card {
    border-radius: 16px;
    background: #1b1b30;
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 0 18px rgba(0,0,0,0.35);
    padding: 16px 18px;
    transition: .25s;
}

.admin-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 25px rgba(120,60,255,0.3);
}

/* light mode adaptation */
body.light .admin-card {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.1);
    box-shadow: 0 0 18px rgba(0,0,0,0.1);
}

body.light .admin-card:hover {
    box-shadow: 0 0 25px rgba(0,0,0,0.15);
}

/* moderation health card */
.dash-card-health {
    padding: 18px 20px;
}
.dash-card-health-title {
    font-weight: 600;
    margin-bottom: 6px;
    color: #fff;
}
body.light .dash-card-health-title {
    color: #1a1a1a;
}
.dash-card-health-sub {
    font-size: 12px;
    opacity: .75;
    margin-bottom: 10px;
    color: #c7c7ff;
}
body.light .dash-card-health-sub {
    color: #6b7280;
}
.dash-health-bar {
    position: relative;
    height: 10px;
    border-radius: 999px;
    background: rgba(255,255,255,0.1);
    overflow: hidden;
}
body.light .dash-health-bar {
    background: #e5e7eb;
}
.dash-health-bar-inner {
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, #22c55e, #a3e635);
    box-shadow: 0 0 12px rgba(34,197,94,0.75);
    transition: width .4s ease;
}
.dash-health-meta {
    margin-top: 8px;
    font-size: 12px;
    display: flex;
    justify-content: space-between;
    opacity: .8;
    color: #c7c7ff;
}
body.light .dash-health-meta {
    color: #6b7280;
}

/* small KPI cards */
.dash-kpi-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}
@media (max-width: 1100px) {
    .dash-kpi-row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width: 700px) {
    .dash-kpi-row {
        grid-template-columns: minmax(0,1fr);
    }
}
.dash-kpi {
    padding: 12px 14px;
    position: relative;
    overflow: hidden;
}
.dash-kpi-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .11em;
    opacity: .78;
    margin-bottom: 4px;
    color: #c7c7ff;
}
body.light .dash-kpi-label {
    color: #6b7280;
}
.dash-kpi-value {
    font-size: 22px;
    font-weight: 800;
    color: #fff;
}
body.light .dash-kpi-value {
    color: #1a1a1a;
}
.dash-kpi-sub {
    font-size: 12px;
    opacity: .7;
    color: #c7c7ff;
}
body.light .dash-kpi-sub {
    color: #6b7280;
}
.dash-kpi-icon {
    position: absolute;
    right: 10px;
    bottom: 8px;
    font-size: 22px;
    opacity: .18;
    color: #6e3bff;
}
body.light .dash-kpi-icon {
    opacity: .1;
}

/* grid for main charts row */
.dash-main-grid {
    margin-top: 10px;
    display: grid;
    grid-template-columns: minmax(0, 2.1fr) minmax(0, 1.3fr);
    gap: 18px;
}
@media (max-width: 1100px) {
    .dash-main-grid {
        grid-template-columns: minmax(0,1fr);
    }
}

.dash-card-chart {
    padding: 16px 18px;
}
.dash-card-title {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #fff;
}
body.light .dash-card-title {
    color: #1a1a1a;
}
.dash-card-sub {
    font-size: 12px;
    opacity: .7;
    margin-bottom: 6px;
    color: #c7c7ff;
}
body.light .dash-card-sub {
    color: #6b7280;
}

/* make charts not huge */
.dash-card-chart canvas {
    max-height: 260px;
}

/* center donut chart */
#chartReports {
    max-width: 100%;
}

/* secondary grid */
.dash-secondary-grid {
    margin-top: 12px;
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr);
    gap: 18px;
}
@media (max-width: 1100px) {
    .dash-secondary-grid {
        grid-template-columns: minmax(0,1fr);
    }
}
.dash-mini-kpis {
    display: grid;
    grid-template-columns: repeat(3, minmax(0,1fr));
    gap: 10px;
    margin-top: 10px;
}
@media (max-width: 720px) {
    .dash-mini-kpis {
        grid-template-columns: repeat(2, minmax(0,1fr));
    }
}
.mini-kpi {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 10px 12px;
}
body.light .mini-kpi {
    background: rgba(0,0,0,0.02);
    border-color: rgba(0,0,0,0.08);
}
.mini-kpi .label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #c7c7ff;
    opacity: .8;
}
body.light .mini-kpi .label {
    color: #6b7280;
}
.mini-kpi .value {
    font-size: 20px;
    font-weight: 800;
    color: #fff;
}
body.light .mini-kpi .value {
    color: #1a1a1a;
}
.mini-kpi .hint {
    font-size: 12px;
    opacity: .7;
    color: #c7c7ff;
}
body.light .mini-kpi .hint {
    color: #6b7280;
}

.dash-recent-list {
    list-style: none;
    margin: 8px 0 0;
    padding: 0;
}
.dash-recent-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.dash-recent-list li:last-child { border-bottom: none; }
.dash-recent-badge {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg,#6e3bff,#a678ff);
    color: #fff;
    font-weight: 700;
    box-shadow: 0 10px 20px rgba(110,59,255,.35);
}
.dash-recent-text {
    display: flex;
    flex-direction: column;
}
.dash-recent-text strong {
    color: #fff;
    font-size: 14px;
}
body.light .dash-recent-text strong {
    color: #1a1a1a;
}
.dash-recent-text span {
    font-size: 12px;
    color: #c7c7ff;
    opacity: .75;
}
body.light .dash-recent-text span {
    color: #6b7280;
}
</style>

<div class="dashboard-wrapper">

    <!-- Optional heading text -->
    <div class="dashboard-header-row">
        <div>
            <h2 style="margin:0;font-size:20px;font-weight:600;">Dashboard</h2>
            <small>Vue globale de l’activité MindArena.</small>
        </div>
    </div>

    <!-- Santé de la modération -->
    <div class="admin-card dash-card-health">
        <div class="dash-card-health-title">Santé de la modération</div>
        <div class="dash-card-health-sub">
            Pourcentage de signalements résolus par rapport au total.
        </div>
        <div class="dash-health-bar">
            <div class="dash-health-bar-inner" style="width: <?= $moderationRate ?>%;"></div>
        </div>
        <div class="dash-health-meta">
            <span><?= $resolvedReports ?> résolus</span>
            <span><?= $pendingReports ?> en attente • <?= $moderationRate ?>%</span>
        </div>
    </div>

    <!-- KPIs -->
    <div class="dash-kpi-row">
        <div class="admin-card dash-kpi">
            <div class="dash-kpi-label">Forums</div>
            <div class="dash-kpi-value"><?= (int)$totalForums ?></div>
            <div class="dash-kpi-sub">Espaces de discussion créés</div>
            <div class="dash-kpi-icon"><i class="ri-layout-3-line"></i></div>
        </div>
        <div class="admin-card dash-kpi">
            <div class="dash-kpi-label">Publications</div>
            <div class="dash-kpi-value"><?= (int)$totalPublications ?></div>
            <div class="dash-kpi-sub">Messages postés par la communauté</div>
            <div class="dash-kpi-icon"><i class="ri-message-3-line"></i></div>
        </div>
        <div class="admin-card dash-kpi">
            <div class="dash-kpi-label">Signalements</div>
            <div class="dash-kpi-value"><?= (int)$totalReports ?></div>
            <div class="dash-kpi-sub">Contenus marqués par les utilisateurs</div>
            <div class="dash-kpi-icon"><i class="ri-flag-2-line"></i></div>
        </div>
        <div class="admin-card dash-kpi">
            <div class="dash-kpi-label">En attente</div>
            <div class="dash-kpi-value"><?= (int)$pendingReports ?></div>
            <div class="dash-kpi-sub">À traiter par les modérateurs</div>
            <div class="dash-kpi-icon"><i class="ri-time-line"></i></div>
        </div>
        <div class="admin-card dash-kpi">
            <div class="dash-kpi-label">Utilisateurs inscrits</div>
            <div class="dash-kpi-value"><?= (int)$totalRegisteredUsers ?></div>
            <div class="dash-kpi-sub">Comptes créés dans la base</div>
            <div class="dash-kpi-icon"><i class="ri-user-3-line"></i></div>
        </div>
        <div class="admin-card dash-kpi">
            <div class="dash-kpi-label">Nouveaux (7j)</div>
            <div class="dash-kpi-value"><?= (int)$newUsers ?></div>
            <div class="dash-kpi-sub">Inscrits cette semaine</div>
            <div class="dash-kpi-icon"><i class="ri-calendar-event-line"></i></div>
        </div>
    </div>

    <!-- Main charts row -->
    <div class="dash-main-grid">

        <!-- Forums vs Publications -->
        <div class="admin-card dash-card-chart">
            <div class="dash-card-title">Forums vs Publications</div>
            <div class="dash-card-sub">Volume global de contenu.</div>
            <div style="position:relative;height:260px;">
                <canvas id="chartForumsPubs"></canvas>
            </div>
        </div>

        <!-- Reports donut -->
        <div class="admin-card dash-card-chart">
            <div class="dash-card-title">Signalements</div>
            <div class="dash-card-sub">Répartition entre en attente et résolus.</div>
            <div style="position:relative;height:260px;">
                <canvas id="chartReports"></canvas>
            </div>
        </div>

    </div>

    <div class="dash-secondary-grid">
        <div class="admin-card dash-card-chart">
            <div class="dash-card-title">Utilisateurs</div>
            <div class="dash-card-sub">Indicateurs combinés du backoffice et des espaces forums.</div>
            <div class="dash-mini-kpis">
                <div class="mini-kpi">
                    <div class="label">Inscrits</div>
                    <div class="value"><?= (int)$totalRegisteredUsers ?></div>
                    <div class="hint">Comptes créés</div>
                </div>
                <div class="mini-kpi">
                    <div class="label">Nouveaux (7j)</div>
                    <div class="value"><?= (int)$newUsers ?></div>
                    <div class="hint">Arrivées récentes</div>
                </div>
                <div class="mini-kpi">
                    <div class="label">Actifs / donateurs</div>
                    <div class="value"><?= (int)$activeDonors ?></div>
                    <div class="hint">Don > 0 ou activité</div>
                </div>
                <div class="mini-kpi">
                    <div class="label">Contributeurs</div>
                    <div class="value"><?= (int)$usersCount ?></div>
                    <div class="hint">Auteurs et créateurs uniques</div>
                </div>
            </div>
        </div>

        <div class="admin-card dash-card-chart">
            <div class="dash-card-title">Nouveaux inscrits</div>
            <div class="dash-card-sub">5 derniers comptes créés.</div>
            <?php if (empty($recentUsers)): ?>
                <p style="opacity:.75;font-size:13px;margin:8px 0 0;">Aucun nouvel inscrit pour le moment.</p>
            <?php else: ?>
                <ul class="dash-recent-list">
                    <?php foreach ($recentUsers as $user): ?>
                        <?php
                            $name = $user['name'] ?? 'Utilisateur';
                            $initials = strtoupper(substr($name, 0, 2));
                        ?>
                        <li>
                            <span class="dash-recent-badge"><?= htmlspecialchars($initials) ?></span>
                            <div class="dash-recent-text">
                                <strong><?= htmlspecialchars($name) ?></strong>
                                <span>Inscription le <?= htmlspecialchars($user['signup_date'] ?? '') ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js + charts config -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stats = {
        forums:       <?= (int)$totalForums ?>,
        publications: <?= (int)$totalPublications ?>,
        reports:      <?= (int)$totalReports ?>,
        pending:      <?= (int)$pendingReports ?>,
        resolved:     <?= (int)$resolvedReports ?>
    };

    const isLight = document.body.classList.contains('light');

    const colors = {
        barBg:      isLight ? 'rgba(129,140,248,0.80)' : 'rgba(96,165,250,0.80)',
        barBorder:  isLight ? '#4f46e5' : '#60a5fa',
        gridColor:  isLight ? 'rgba(148,163,184,0.45)' : 'rgba(148,163,184,0.35)',
        donutPending:  '#facc15',
        donutResolved: '#22c55e'
    };

    // ---- Bar chart Forums vs Publications ----
    const ctx1 = document.getElementById('chartForumsPubs');
    if (ctx1) {
        new Chart(ctx1.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Forums', 'Publications'],
                datasets: [{
                    data: [stats.forums, stats.publications],
                    backgroundColor: colors.barBg,
                    borderColor: colors.barBorder,
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 80
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)',
                        borderColor: 'rgba(148,163,184,0.6)',
                        borderWidth: 1,
                        padding: 8,
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 }
                    }
                },
                layout: {
                    padding: { top: 10, right: 10, left: 5, bottom: 5 }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: colors.gridColor },
                        ticks: {
                            font: { size: 11 },
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // ---- Donut chart Reports ----
    const ctx2 = document.getElementById('chartReports');
    if (ctx2) {
        new Chart(ctx2.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'Résolus'],
                datasets: [{
                    data: [stats.pending, stats.resolved],
                    backgroundColor: [
                        colors.donutPending,
                        colors.donutResolved
                    ],
                    borderColor: isLight ? '#ffffff' : '#020617',
                    borderWidth: 2
                }]
            },
            options: {
                cutout: '68%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)',
                        borderColor: 'rgba(148,163,184,0.6)',
                        borderWidth: 1,
                        padding: 8,
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 }
                    }
                }
            }
        });
    }
});
</script>

