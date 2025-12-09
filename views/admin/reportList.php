<?php
// reportList.php – vue admin des signalements

// Variables fournies par le contrôleur :
// $reports, $totalReports, $pendingReports

// Ensure variables are set
$reports = $reports ?? [];
$totalReports = $totalReports ?? 0;
$pendingReports = $pendingReports ?? 0;
$resolvedReports = max(0, $totalReports - $pendingReports);

// Base URL du projet (ex: /mindarena_forum)
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../../config/constants.php';
}
$baseUrl = BASE_URL;
$sort = $sort ?? ($_GET['sort'] ?? 'date');
$dir  = $dir ?? ($_GET['dir'] ?? 'desc');
?>

<style>
    /* Helper pour cibler le thème clair quelle que soit l’implémentation */
    body.theme-light,
    body[data-theme="light"],
    body.light {
        --_is-light: 1;
    }

    /* ===== STRUCTURE DE LA PAGE ===== */
    .reports-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
    }
    .reports-page-header h1 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
        color: #fff;
    }
    body.light .reports-page-header h1 {
        color: #1a1a1a;
    }
    .reports-page-header p {
        margin: 0;
        font-size: 13px;
        opacity: .7;
        color: #c7c7ff;
    }
    body.light .reports-page-header p {
        color: #6b7280;
    }
    .reports-page-header-meta {
        font-size: 12px;
        opacity: .7;
        text-align: right;
        color: #c7c7ff;
    }
    body.light .reports-page-header-meta {
        color: #6b7280;
    }

    /* ===== PETITES CARTES STATS ===== */
    .report-cards {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }
    @media (max-width: 900px) {
        .report-cards {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    .report-card {
        position: relative;
        border-radius: 16px;
        padding: 16px 18px;
        background: #1b1b30;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 0 18px rgba(0,0,0,0.35);
        overflow: hidden;
        transition: .25s;
    }
    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 25px rgba(120,60,255,0.3);
    }
    body.theme-light .report-card,
    body[data-theme="light"] .report-card,
    body.light .report-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 0 18px rgba(0,0,0,0.1);
    }
    body.light .report-card:hover {
        box-shadow: 0 0 25px rgba(0,0,0,0.15);
    }
    .report-card-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .12em;
        opacity: .75;
        margin-bottom: 4px;
        color: #c7c7ff;
    }
    body.light .report-card-label {
        color: #6b7280;
    }
    .report-card-value {
        font-size: 24px;
        font-weight: 800;
        color: #fff;
    }
    body.light .report-card-value {
        color: #1a1a1a;
    }
    .report-card-sub {
        font-size: 12px;
        opacity: .7;
        margin-top: 2px;
        color: #c7c7ff;
    }
    body.light .report-card-sub {
        color: #6b7280;
    }
    .report-card-icon {
        position: absolute;
        right: 14px;
        bottom: 10px;
        font-size: 24px;
        opacity: .18;
    }

    /* ===== FILTRES STATUT ===== */
.report-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 10px;
    align-items: center;
    justify-content: space-between;
}
.sort-form {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
}
.sort-form label {
    font-size: 12px;
    opacity: .75;
    margin-right: 2px;
}
.sort-form select {
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    background: transparent;
    color: inherit;
    border: 1px solid rgba(148,163,184,0.6);
}
.sort-form select:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(129,140,248,0.4);
}
    .report-filter-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .filter-label {
        font-size: 12px;
        opacity: .7;
        margin-right: 4px;
    }
    .chip-filter {
        border-radius: 999px;
        padding: 4px 12px;
        border: 1px solid rgba(148,163,184,0.6);
        font-size: 12px;
        cursor: pointer;
        background: transparent;
        color: inherit;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: background .15s ease, color .15s ease,
                    box-shadow .15s ease, border-color .15s ease;
    }
    .chip-filter span.dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
    }
    .chip-filter[data-filter="pending"] span.dot { background:#facc15; }
    .chip-filter[data-filter="seen"] span.dot { background:#60a5fa; }
    .chip-filter[data-filter="resolved"] span.dot { background:#4ade80; }
    .chip-filter.active {
        background: rgba(129,140,248,0.20);
        border-color: rgba(129,140,248,0.9);
        box-shadow: 0 6px 18px rgba(129,140,248,0.45);
    }
    body.theme-light .chip-filter.active,
    body[data-theme="light"] .chip-filter.active,
    body.light .chip-filter.active {
        background: rgba(129,140,248,0.10);
    }

    /* ===== CONTENEUR TABLE ===== */
    .reports-table-card {
        border-radius: 16px;
        background: #1b1b30;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 0 18px rgba(0,0,0,0.35);
        overflow: hidden;
        transition: .25s;
    }
    .reports-table-card:hover {
        box-shadow: 0 0 25px rgba(120,60,255,0.3);
    }
    body.theme-light .reports-table-card,
    body[data-theme="light"] .reports-table-card,
    body.light .reports-table-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 0 18px rgba(0,0,0,0.1);
    }
    body.light .reports-table-card:hover {
        box-shadow: 0 0 25px rgba(0,0,0,0.15);
    }

    .reports-table-card-header {
        padding: 16px 20px 8px;
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 10px;
    }
    .reports-table-card-header h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }
    .reports-table-card-header span {
        font-size: 12px;
        opacity: .7;
    }

    /* ===== TABLE ===== */
    .reports-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    table.report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 900px;
    }
    table.report-table thead tr {
        background: rgba(15,23,42,0.98);
        color: #e5e7eb;
    }
    body.theme-light table.report-table thead tr,
    body[data-theme="light"] table.report-table thead tr,
    body.light table.report-table thead tr {
        background: #f3f4ff;
        color: #111827;
    }
    table.report-table th,
    table.report-table td {
        padding: 10px 14px;
        text-align: left;
        border-bottom: 1px solid rgba(148,163,184,0.30);
        vertical-align: top;
    }
    table.report-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .12em;
        opacity: .85;
    }
    table.report-table tbody tr {
        transition: background .15s ease, transform .08s ease;
    }
    table.report-table tbody tr:hover {
        background: rgba(129,140,248,0.10);
    }
    body.theme-light table.report-table tbody tr:hover,
    body[data-theme="light"] table.report-table tbody tr:hover,
    body.light table.report-table tbody tr:hover {
        background: rgba(129,140,248,0.05);
    }

    /* ===== BADGES TYPE & STATUT ===== */
    .badge-type {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        border: 1px solid rgba(148,163,184,0.7);
        opacity: .95;
    }
    .badge-type i {
        font-size: 13px;
    }
    .badge-type.forum {
        background: rgba(59,130,246,0.14);
        border-color: rgba(59,130,246,0.68);
        color: #bfdbfe;
    }
    .badge-type.publication {
        background: rgba(236,72,153,0.16);
        border-color: rgba(236,72,153,0.68);
        color: #f9a8d4;
    }
    body.theme-light .badge-type.forum,
    body[data-theme="light"] .badge-type.forum,
    body.light .badge-type.forum {
        color: #1d4ed8;
        background: rgba(59,130,246,0.12);
    }
    body.theme-light .badge-type.publication,
    body[data-theme="light"] .badge-type.publication,
    body.light .badge-type.publication {
        color: #be185d;
        background: rgba(236,72,153,0.10);
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 600;
    }
    .badge-status.pending {
        background: rgba(234,179,8,0.16);
        color: #facc15;
    }
    .badge-status.seen {
        background: rgba(59,130,246,0.18);
        color: #93c5fd;
    }
    .badge-status.resolved {
        background: rgba(34,197,94,0.18);
        color: #6ee7b7;
    }

    /* ===== BOUTONS ACTIONS ===== */
    .btn-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        border: none;
        text-decoration: none;
        cursor: pointer;
        transition: transform .15s ease,
                    box-shadow .15s ease,
                    background .15s ease,
                    color .15s ease,
                    border-color .15s ease;
        white-space: nowrap;
        gap: 4px;
    }
    .btn-pill i {
        font-size: 14px;
    }
    .btn-outline-primary {
        background: transparent;
        color: #a855f7;
        border: 1px solid rgba(168,85,247,0.75);
    }
    .btn-outline-primary:hover {
        background: rgba(168,85,247,0.18);
        box-shadow: 0 8px 20px rgba(168,85,247,0.55);
        transform: translateY(-1px);
    }
    body.theme-light .btn-outline-primary:hover,
    body[data-theme="light"] .btn-outline-primary:hover,
    body.light .btn-outline-primary:hover {
        background: rgba(168,85,247,0.10);
    }

    .btn-ghost {
        background: transparent;
        color: #9ca3af;
        border: 1px dashed rgba(148,163,184,0.7);
    }
    .btn-ghost:hover {
        background: rgba(148,163,184,0.12);
        color: #e5e7eb;
    }

    .btn-soft-warning {
        background: rgba(234,179,8,0.18);
        color: #facc15;
        border: none;
    }
    .btn-soft-warning:hover {
        background: rgba(234,179,8,0.26);
        box-shadow: 0 5px 14px rgba(250,204,21,0.5);
        transform: translateY(-1px);
    }

    .btn-soft-success {
        background: rgba(34,197,94,0.20);
        color: #6ee7b7;
        border: none;
    }
    .btn-soft-success:hover {
        background: rgba(34,197,94,0.28);
        box-shadow: 0 5px 14px rgba(34,197,94,0.5);
        transform: translateY(-1px);
    }

    /* Prominent delete button */
    .btn-danger {
        background: linear-gradient(135deg,#ef4444,#dc2626);
        color: #fff;
        padding: 8px 14px;
        border-radius: 999px;
        font-weight: 700;
        box-shadow: 0 10px 24px rgba(220,38,38,0.28);
        border: none;
        transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
        margin-left: 8px;
    }
    .btn-danger i { font-size: 16px; }
    .btn-danger:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(220,38,38,0.32); opacity: 0.98 }

    .reports-actions {
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: flex-start;
    }
    @media (min-width: 1100px) {
        .reports-actions {
            flex-direction: row;
            align-items: center;
        }
    }
</style>

<div class="reports-page-header">
    <div>
        <h1>Signalements</h1>
        <p>Surveillez les contenus signalés par la communauté (forums &amp; publications).</p>
    </div>
    <div class="reports-page-header-meta">
        <div><strong><?= (int)$totalReports ?></strong> signalements au total</div>
        <div>Vue interne uniquement (actions locales).</div>
    </div>
</div>

<div class="report-cards">
    <div class="report-card">
        <div class="report-card-label">Total signalements</div>
        <div class="report-card-value"><?= (int)$totalReports ?></div>
        <div class="report-card-sub">Depuis le lancement</div>
        <div class="report-card-icon"><i class="ri-flag-2-line"></i></div>
    </div>
    <div class="report-card">
        <div class="report-card-label">En attente</div>
        <div class="report-card-value"><?= (int)$pendingReports ?></div>
        <div class="report-card-sub">À examiner / modérer</div>
        <div class="report-card-icon"><i class="ri-time-line"></i></div>
    </div>
    <div class="report-card">
        <div class="report-card-label">Clôturés</div>
        <div class="report-card-value"><?= (int)$resolvedReports ?></div>
        <div class="report-card-sub">Marqués comme résolus</div>
        <div class="report-card-icon"><i class="ri-check-double-line"></i></div>
    </div>
</div>

<div class="reports-table-card">

    <div class="reports-table-card-header">
        <div>
            <h2>Liste des signalements</h2>
            <span><?= count($reports) ?> lignes</span>
        </div>
        <form method="get" class="sort-form">
            <input type="hidden" name="action" value="reports">
            <label for="sort">Trier par</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="date" <?= $sort === 'date' ? 'selected' : '' ?>>Date</option>
                <option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>Statut</option>
                <option value="type" <?= $sort === 'type' ? 'selected' : '' ?>>Type</option>
            </select>
            <select name="dir" onchange="this.form.submit()">
                <option value="desc" <?= $dir === 'desc' ? 'selected' : '' ?>>Desc</option>
                <option value="asc" <?= $dir === 'asc' ? 'selected' : '' ?>>Asc</option>
            </select>
        </form>
    </div>

    <div class="report-filters">
        <div class="report-filter-group">
            <span class="filter-label">Filtrer par statut :</span>

            <button class="chip-filter active" data-filter="all">
                Tous
            </button>
            <button class="chip-filter" data-filter="pending">
                <span class="dot"></span> En attente
            </button>
            <button class="chip-filter" data-filter="seen">
                <span class="dot"></span> Vu
            </button>
            <button class="chip-filter" data-filter="resolved">
                <span class="dot"></span> Résolu
            </button>
        </div>
    </div>

    <div class="reports-table-wrapper">
        <table class="report-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Cible</th>
                <th>Raison</th>
                <th>Extrait</th>
                <th>Statut</th>
                <th>Date</th>
                <th style="width:270px;">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:24px;opacity:.7;">
                        Aucun signalement pour le moment. ✨
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($reports as $r): ?>
                    <?php
                    $status = $r['status'] ?? 'pending';
                    $type   = $r['target_type'] ?? '';
                    $id     = (int)$r['id'];

                    // Badge statut
                    $statusClass = 'pending';
                    $statusLabel = 'En attente';
                    if ($status === 'seen') {
                        $statusClass = 'seen';
                        $statusLabel = 'Vu';
                    } elseif ($status === 'resolved') {
                        $statusClass = 'resolved';
                        $statusLabel = 'Résolu';
                    }

                    // Type badge
                    $typeClass = '';
                    $typeLabel = ucfirst($type ?: '—');
                    $typeIcon  = 'ri-question-line';
                    if ($type === 'forum') {
                        $typeClass = 'forum';
                        $typeIcon  = 'ri-message-2-line';
                    } elseif ($type === 'publication') {
                        $typeClass = 'publication';
                        $typeIcon  = 'ri-file-text-line';
                    }

                    /*
                     * Construction du label et de l'extrait à partir
                     * des colonnes de la nouvelle jointure :
                     * - forum_title
                     * - publication_content
                     * - publication_author
                     * - publication_forum_id
                     */
                    $targetLabel   = '';
                    $targetExcerpt = '';

                    if ($type === 'forum') {
                        $forumId    = (int)($r['forum_id'] ?? 0);
                        $forumTitle = $r['forum_title'] ?? '';
                        $targetLabel = $forumTitle !== ''
                            ? htmlspecialchars($forumTitle) . " (Forum #{$forumId})"
                            : "Forum #{$forumId}";
                        $targetExcerpt = $r['forum_description'] ?? $r['details'] ?? '';
                    } elseif ($type === 'publication') {
                        $pubId    = (int)($r['publication_id'] ?? 0);
                        $author   = $r['publication_author'] ?? 'Auteur inconnu';
                        $targetLabel = "Publication #{$pubId} · " . htmlspecialchars($author);

                        if (!empty($r['publication_content'])) {
                            $targetExcerpt = $r['publication_content'];
                        } else {
                            $targetExcerpt = $r['details'] ?? '';
                        }
                    } else {
                        // Cas de secours
                        $targetLabel   = '—';
                        $targetExcerpt = $r['details'] ?? '';
                    }

                    // Limiter l'extrait
                    if ($targetExcerpt !== '') {
                        $targetExcerpt = trim($targetExcerpt);
                        if (mb_strlen($targetExcerpt) > 120) {
                            $targetExcerpt = mb_substr($targetExcerpt, 0, 120) . '…';
                        }
                    }

                    // Liens vers le front
                    $frontLabel = null;
                    $frontUrl   = null;

                    if ($type === 'forum') {
                        $forumId  = (int)($r['forum_id'] ?? 0);
                        if ($forumId > 0) {
                            $frontUrl   = $baseUrl . '/index.php?action=publications&forum_id=' . $forumId;
                            $frontLabel = 'Voir le forum →';
                        }
                    } elseif ($type === 'publication') {
                        $forumId = (int)($r['publication_forum_id'] ?? $r['forum_id'] ?? 0);
                        $pubId   = (int)($r['publication_id'] ?? 0);

                        if ($forumId > 0) {
                            $frontUrl   = $baseUrl . '/index.php?action=publications&forum_id=' . $forumId;
                            if ($pubId > 0) {
                                $frontUrl .= '#pub-' . $pubId; // optionnel si tu gères l'ancre
                            }
                            $frontLabel = 'Voir la publication →';
                        }
                    }
                    ?>
                    <tr data-status="<?= htmlspecialchars($status) ?>">
                        <td>#<?= $id ?></td>
                        <td>
                            <?php if ($type): ?>
                                <span class="badge-type <?= $typeClass ?>">
                                    <i class="<?= $typeIcon ?>"></i>
                                    <?= htmlspecialchars($typeLabel) ?>
                                </span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($targetLabel) ?></td>
                        <td><?= htmlspecialchars($r['reason'] ?? '') ?></td>
                        <td style="max-width:260px;">
                            <?php if ($targetExcerpt !== ''): ?>
                                <?= nl2br(htmlspecialchars($targetExcerpt)) ?>
                            <?php else: ?>
                                <span style="opacity:.7;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge-status <?= $statusClass ?>">
                                <?= $statusLabel ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($r['created_at'] ?? '') ?></td>
                        <td>
                            <div class="reports-actions">
                                <?php if ($frontUrl && $frontLabel): ?>
                                    <a href="<?= htmlspecialchars($frontUrl) ?>" target="_blank"
                                       class="btn-pill btn-outline-primary">
                                        <i class="ri-external-link-line"></i>
                                        <?= htmlspecialchars($frontLabel) ?>
                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn-pill btn-ghost" disabled>
                                        <i class="ri-link-unlink-m"></i> Aucune page liée
                                    </button>
                                <?php endif; ?>

                                <?php if ($status !== 'seen'): ?>
                                    <form method="POST" action="admin.php?action=report-status" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <input type="hidden" name="status" value="seen">
                                        <button type="submit" class="btn-pill btn-soft-warning">
                                            <i class="ri-eye-line"></i> Marquer vu
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($status !== 'resolved'): ?>
                                    <form method="POST" action="admin.php?action=report-status" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <input type="hidden" name="status" value="resolved">
                                        <button type="submit" class="btn-pill btn-soft-success">
                                            <i class="ri-check-line"></i> Résoudre
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Delete report (POST) -->
                                <form method="POST" action="admin.php?action=delete-report" style="display:inline;" onsubmit="return confirm('Confirmer la suppression du signalement ?');">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <button type="submit" class="btn-pill btn-danger" title="Supprimer le signalement">
                                        <i class="ri-delete-bin-6-line"></i>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Filtre simple côté client par statut
    document.addEventListener('DOMContentLoaded', function () {
        const filterButtons = document.querySelectorAll('.chip-filter');
        const rows = document.querySelectorAll('table.report-table tbody tr[data-status]');

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.getAttribute('data-filter');

                rows.forEach(row => {
                    const status = row.getAttribute('data-status');
                    row.style.display = (filter === 'all' || status === filter) ? '' : 'none';
                });
            });
        });
    });
</script>

