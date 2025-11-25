<?php
// Dashboard – vue admin

// Sécurisation & petites stats dérivées
$totalForums       = $totalForums       ?? 0;
$totalPublications = $totalPublications ?? 0;
$totalReports      = $totalReports      ?? 0;
$pendingReports    = $pendingReports    ?? 0;

$resolvedReports = max(0, $totalReports - $pendingReports);
$pendingRate     = $totalReports > 0 ? round(($pendingReports / $totalReports) * 100) : 0;
$resolvedRate    = $totalReports > 0 ? round(($resolvedReports / $totalReports) * 100) : 0;
?>
<style>
/* ---- HEADER ---- */
.ma-dash-header {
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap:12px;
    margin-bottom:22px;
}
.ma-dash-title {
    font-size:24px;
    font-weight:700;
    margin:0 0 6px;
}
.ma-dash-subtitle {
    margin:0;
    font-size:13px;
    opacity:.75;
}
.ma-dash-badge {
    display:inline-flex;
    align-items:center;
    gap:6px;
    font-size:12px;
    padding:4px 10px;
    border-radius:999px;
    border:1px solid rgba(148,163,184,.4);
    opacity:.85;
}

/* ---- STATS CARDS ---- */
.ma-stat-grid {
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:14px;
    margin-bottom:22px;
}
@media (max-width: 1100px) {
    .ma-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr));}
}
@media (max-width: 700px) {
    .ma-stat-grid{grid-template-columns:1fr;}
}

.ma-stat-card {
    background:var(--card-bg);
    border-radius:14px;
    padding:14px 16px;
    border:1px solid var(--border);
    box-shadow:0 10px 22px var(--shadow);
    display:flex;
    flex-direction:column;
    gap:6px;
    position:relative;
    overflow:hidden;
}
.ma-stat-card::after{
    content:"";
    position:absolute;
    inset:auto -40px  -40px auto;
    width:80px;
    height:80px;
    border-radius:999px;
    background:radial-gradient(circle at center,
        rgba(124,58,237,.45),
        transparent 60%);
    opacity:.65;
    pointer-events:none;
}
.ma-stat-label {
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.12em;
    opacity:.7;
}
.ma-stat-value {
    font-size:24px;
    font-weight:700;
}
.ma-stat-foot {
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:11px;
    opacity:.8;
}
.ma-stat-pill {
    padding:3px 9px;
    border-radius:999px;
    font-size:11px;
    background:rgba(148,163,184,.15);
}

/* ---- MAIN GRID ---- */
.ma-main-grid {
    display:grid;
    grid-template-columns:2.1fr 1.1fr;
    gap:16px;
    margin-bottom:18px;
}
@media (max-width:900px) {
    .ma-main-grid { grid-template-columns:1fr; }
}

/* ---- CARDS ---- */
.ma-card {
    background:var(--card-bg);
    border-radius:16px;
    padding:18px 18px 16px;
    border:1px solid var(--border);
    box-shadow:0 14px 30px var(--shadow);
}

.ma-card-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:10px;
}
.ma-card-title {
    font-size:15px;
    font-weight:600;
    margin:0;
}
.ma-card-sub {
    font-size:12px;
    opacity:.7;
    margin:2px 0 0;
}
.ma-card-select {
    border-radius:999px;
    padding:4px 10px;
    font-size:12px;
    border:1px solid rgba(148,163,184,.4);
    background:transparent;
    color:inherit;
}

/* ---- CHART CONTAINERS ---- */
.ma-chart-wrap {
    position:relative;
    width:100%;
    height:220px;
}
.ma-chart-wrap-small {
    position:relative;
    width:100%;
    height:190px;
}

/* ---- REPORTS SUMMARY ---- */
.ma-report-summary {
    display:flex;
    flex-direction:column;
    gap:8px;
    font-size:13px;
    margin-top:8px;
}
.ma-report-row {
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.ma-report-bar {
    position:relative;
    width:100%;
    height:7px;
    border-radius:999px;
    background:rgba(148,163,184,.2);
    overflow:hidden;
}
.ma-report-bar-inner-pending {
    position:absolute;
    inset:0;
    width:<?= $pendingRate ?>%;
    background:linear-gradient(90deg,#f97373,#f97316);
}
.ma-report-bar-inner-resolved {
    position:absolute;
    inset:0;
    width:<?= $resolvedRate ?>%;
    background:linear-gradient(90deg,#22c55e,#4ade80);
}

/* ---- MINI LIST ---- */
.ma-mini-list{
    margin-top:10px;
    padding:0;
    list-style:none;
    font-size:13px;
}
.ma-mini-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:6px 0;
    border-bottom:1px solid rgba(148,163,184,.18);
}
.ma-mini-item:last-child{
    border-bottom:none;
}
.ma-mini-label{
    opacity:.85;
}
.ma-mini-tag{
    font-size:11px;
    padding:2px 8px;
    border-radius:999px;
    background:rgba(148,163,184,.2);
}

/* ---- COLORS ADAPT LIGHT MODE ---- */
body.light .ma-stat-card::after{
    background:radial-gradient(circle at center,
        rgba(124,58,237,.28),
        transparent 60%);
}
body.light .ma-mini-tag{
    background:rgba(148,163,184,.15);
}
</style>

<div class="ma-dash-header">
    <div>
        <h1 class="ma-dash-title">Tableau de bord</h1>
        <p class="ma-dash-subtitle">
            Vue d’ensemble de l’activité forums & publications sur MindArena.
        </p>
    </div>
    <div class="ma-dash-badge">
        <i class="ri-shield-user-line"></i>
        <span>Connecté en tant qu’<strong>Admin</strong></span>
    </div>
</div>

<!-- STAT CARDS -->
<div class="ma-stat-grid">
    <div class="ma-stat-card">
        <div class="ma-stat-label">Forums</div>
        <div class="ma-stat-value"><?= $totalForums ?></div>
        <div class="ma-stat-foot">
            <span>Espaces de discussion créés</span>
            <span class="ma-stat-pill">
                <i class="ri-folder-3-line"></i> Structure
            </span>
        </div>
    </div>

    <div class="ma-stat-card">
        <div class="ma-stat-label">Publications</div>
        <div class="ma-stat-value"><?= $totalPublications ?></div>
        <div class="ma-stat-foot">
            <span>Messages publiés par la communauté</span>
            <span class="ma-stat-pill">
                <i class="ri-message-3-line"></i> Contenu
            </span>
        </div>
    </div>

    <div class="ma-stat-card">
        <div class="ma-stat-label">Signalements</div>
        <div class="ma-stat-value"><?= $totalReports ?></div>
        <div class="ma-stat-foot">
            <span>Total des contenus signalés</span>
            <span class="ma-stat-pill">
                <i class="ri-flag-2-line"></i> Modération
            </span>
        </div>
    </div>

    <div class="ma-stat-card">
        <div class="ma-stat-label">Signalements en attente</div>
        <div class="ma-stat-value"><?= $pendingReports ?></div>
        <div class="ma-stat-foot">
            <span><?= $pendingRate ?>% à traiter</span>
            <span class="ma-stat-pill" style="background:rgba(248,113,113,.18);">
                <i class="ri-time-line"></i> Urgent
            </span>
        </div>
    </div>
</div>

<!-- MAIN GRID: CHARTS + REPORTS -->
<div class="ma-main-grid">
    <!-- Activity chart -->
    <div class="ma-card">
        <div class="ma-card-header">
            <div>
                <h2 class="ma-card-title">Activité globale</h2>
                <p class="ma-card-sub">
                    Répartition indicative forums / publications (visuel).
                </p>
            </div>
            <select class="ma-card-select">
                <option>Vue globale</option>
                <option>Données simulées</option>
            </select>
        </div>

        <div class="ma-chart-wrap">
            <canvas id="maActivityChart"></canvas>
        </div>
    </div>

    <!-- Reports donut + summary -->
    <div class="ma-card">
        <div class="ma-card-header">
            <div>
                <h2 class="ma-card-title">Signalements</h2>
                <p class="ma-card-sub">
                    Statut des contenus signalés par les utilisateurs.
                </p>
            </div>
        </div>

        <div class="ma-chart-wrap-small">
            <canvas id="maReportsChart"></canvas>
        </div>

        <div class="ma-report-summary">
            <div class="ma-report-row">
                <span>En attente</span>
                <span><?= $pendingReports ?> (<?= $pendingRate ?>%)</span>
            </div>
            <div class="ma-report-bar">
                <div class="ma-report-bar-inner-pending"></div>
            </div>

            <div class="ma-report-row" style="margin-top:6px;">
                <span>Traités</span>
                <span><?= $resolvedReports ?> (<?= $resolvedRate ?>%)</span>
            </div>
            <div class="ma-report-bar">
                <div class="ma-report-bar-inner-resolved"></div>
            </div>
        </div>
    </div>
</div>

<!-- SECOND ROW: quick info -->
<div class="ma-main-grid" style="grid-template-columns:1.4fr 1.6fr;">
    <div class="ma-card">
        <div class="ma-card-header">
            <div>
                <h2 class="ma-card-title">Raccourcis modération</h2>
                <p class="ma-card-sub">Accède rapidement aux zones sensibles.</p>
            </div>
        </div>

        <ul class="ma-mini-list">
            <li class="ma-mini-item">
                <span class="ma-mini-label">
                    Forums & structure
                </span>
                <span>
                    <a href="admin.php?action=forums" class="ma-mini-tag">
                        <i class="ri-folder-3-line"></i> Gérer les forums
                    </a>
                </span>
            </li>

            <li class="ma-mini-item">
                <span class="ma-mini-label">
                    Publications (par forum)
                </span>
                <span>
                    <a href="admin.php?action=forums" class="ma-mini-tag">
                        <i class="ri-route-line"></i> Choisir un forum
                    </a>
                </span>
            </li>

            <li class="ma-mini-item">
                <span class="ma-mini-label">
                    Signalements récents
                </span>
                <span>
                    <a href="admin.php?action=reports" class="ma-mini-tag">
                        <i class="ri-flag-2-line"></i> Ouvrir la liste
                    </a>
                </span>
            </li>
        </ul>
    </div>

    <div class="ma-card">
        <div class="ma-card-header">
            <div>
                <h2 class="ma-card-title">État général</h2>
                <p class="ma-card-sub">
                    Résumé rapide de la santé de la communauté.
                </p>
            </div>
        </div>

        <ul class="ma-mini-list">
            <li class="ma-mini-item">
                <span class="ma-mini-label">
                    Ratio publications / forums
                </span>
                <span class="ma-mini-tag">
                    <?php
                    $ratio = $totalForums > 0
                        ? round($totalPublications / max(1,$totalForums), 1)
                        : 0;
                    echo $ratio . ' pub / forum';
                    ?>
                </span>
            </li>
            <li class="ma-mini-item">
                <span class="ma-mini-label">
                    Taux de signalement (approx.)
                </span>
                <span class="ma-mini-tag">
                    <?php
                    $rate = $totalPublications > 0
                        ? round(($totalReports / max(1,$totalPublications)) * 100, 1)
                        : 0;
                    echo $rate . '% des publications';
                    ?>
                </span>
            </li>
            <li class="ma-mini-item">
                <span class="ma-mini-label">
                    Charge de modération
                </span>
                <span class="ma-mini-tag">
                    <?php
                    if ($pendingReports === 0) {
                        echo 'Faible';
                    } elseif ($pendingReports < 5) {
                        echo 'Modérée';
                    } else {
                        echo 'Élevée';
                    }
                    ?>
                </span>
            </li>
        </ul>
    </div>
</div>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const textColor  = getComputedStyle(document.body).getPropertyValue('--text') || '#e6e6f0';
    const borderCol  = getComputedStyle(document.body).getPropertyValue('--border') || 'rgba(148,163,184,.35)';

    // Activity chart (forums vs publications)
    const ctxActivity = document.getElementById('maActivityChart');
    if (ctxActivity) {
        new Chart(ctxActivity, {
            type: 'line',
            data: {
                labels: ['Forums', 'Publications'],
                datasets: [{
                    label: 'Volume',
                    data: [<?= (int)$totalForums ?>, <?= (int)$totalPublications ?>],
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124,58,237,.25)',
                    borderWidth: 2,
                    tension: 0.25,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#a855f7'
                }]
            },
            options: {
                responsive:true,
                maintainAspectRatio:false,
                plugins: {
                    legend: { display:false }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(148,163,184,.15)' },
                        ticks:{ color:textColor }
                    },
                    y: {
                        beginAtZero:true,
                        grid: { color: 'rgba(148,163,184,.18)' },
                        ticks:{ color:textColor }
                    }
                }
            }
        });
    }

    // Reports chart (pending vs resolved)
    const ctxReports = document.getElementById('maReportsChart');
    if (ctxReports) {
        new Chart(ctxReports, {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'Traités'],
                datasets: [{
                    data: [<?= (int)$pendingReports ?>, <?= (int)$resolvedReports ?>],
                    backgroundColor: [
                        'rgba(248,113,113,.9)',
                        'rgba(34,197,94,.9)'
                    ],
                    borderColor: [
                        'rgba(248,113,113,1)',
                        'rgba(34,197,94,1)'
                    ],
                    borderWidth:1,
                    hoverOffset:4
                }]
            },
            options: {
                cutout:'62%',
                plugins: {
                    legend: {
                        position:'bottom',
                        labels:{ color:textColor, font:{ size:11 } }
                    }
                }
            }
        });
    }
});
</script>
