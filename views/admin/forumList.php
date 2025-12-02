<?php
// views/admin/forumList.php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../../config/constants.php';
}
$forums = $forums ?? [];
$totalForums = count($forums);
?>
<style>
    .page-head {
        display:flex;
        justify-content:space-between;
        align-items:flex-end;
        gap:16px;
        margin-bottom:18px;
    }
    .page-title-block h1{
        font-size:24px;
        font-weight:700;
        margin:0 0 4px;
        color:#fff;
    }
    body.light .page-title-block h1 {
        color:#1a1a1a;
    }
    .page-title-block p{
        margin:0;
        font-size:13px;
        opacity:.75;
        color:#c7c7ff;
    }
    body.light .page-title-block p {
        color:#6b7280;
    }

    .page-head-actions{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        align-items:center;
    }

    .chip {
        padding:6px 10px;
        border-radius:999px;
        font-size:12px;
        border:1px solid rgba(255,255,255,0.15);
        opacity:.85;
        background:rgba(255,255,255,0.05);
        color:#c7c7ff;
    }
    body.light .chip {
        border:1px solid rgba(0,0,0,0.15);
        background:rgba(0,0,0,0.03);
        color:#6b7280;
    }

    .btn-primary-pill{
        border:none;
        border-radius:999px;
        padding:9px 16px;
        font-size:13px;
        font-weight:600;
        cursor:pointer;
        text-decoration:none;
        background:linear-gradient(135deg,#6e3bff,#a678ff);
        color:#fff;
        box-shadow:0 10px 25px rgba(110,59,255,.45);
        transition:.18s;
        display:inline-flex;
        align-items:center;
        gap:6px;
    }
    .btn-primary-pill:hover{
        transform:translateY(-1px);
        background:linear-gradient(135deg,#8358ff,#c49aff);
        box-shadow:0 16px 32px rgba(110,59,255,.55);
    }

    .surface-card{
        background:#1b1b30;
        border-radius:16px;
        padding:18px 18px 12px;
        border:1px solid rgba(255,255,255,0.08);
        box-shadow:0 0 18px rgba(0,0,0,0.35);
        overflow:hidden;
        transition:.25s;
    }
    .surface-card:hover {
        transform: translateY(-2px);
        box-shadow:0 0 25px rgba(120,60,255,0.3);
    }
    body.light .surface-card{
        background:#ffffff;
        border:1px solid rgba(0,0,0,0.1);
        box-shadow:0 0 18px rgba(0,0,0,0.1);
    }
    body.light .surface-card:hover {
        box-shadow:0 0 25px rgba(0,0,0,0.15);
    }

    .toolbar-row{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        margin-bottom:12px;
        flex-wrap:wrap;
    }

    .search-input-wrap{
        position:relative;
        flex:1;
        min-width:220px;
    }
    .search-input-wrap input{
        width:100%;
        padding:8px 34px 8px 12px;
        border-radius:999px;
        border:1px solid rgba(255,255,255,0.15);
        background:rgba(255,255,255,0.08);
        color:#fff;
        font-size:13px;
        outline:none;
        transition:.15s;
    }
    .search-input-wrap input:focus{
        border-color:#6e3bff;
        box-shadow:0 0 0 1px rgba(110,59,255,.4);
        background:rgba(255,255,255,0.12);
    }
    body.light .search-input-wrap input{
        background:rgba(0,0,0,0.05);
        color:#1a1a1a;
        border-color:rgba(0,0,0,0.15);
    }
    body.light .search-input-wrap input:focus{
        background:rgba(0,0,0,0.08);
    }
    .search-input-wrap i{
        position:absolute;
        right:10px;
        top:50%;
        transform:translateY(-50%);
        font-size:14px;
        opacity:.7;
    }

    /* table */
    .forum-table{
        width:100%;
        border-collapse:collapse;
        font-size:13px;
    }
    .forum-table th,
    .forum-table td{
        padding:10px 10px;
        border-bottom:1px solid rgba(255,255,255,0.06);
    }
    .forum-table th{
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.13em;
        opacity:.7;
        text-align:left;
        color:#d4cfff;
    }
    body.light .forum-table th {
        color:#6b7280;
    }
    .forum-table td {
        color:#efefff;
    }
    body.light .forum-table td {
        color:#1a1a1a;
    }
    .forum-table tbody tr{
        transition:background .16s,transform .16s;
    }
    .forum-table tbody tr:hover{
        background:rgba(255,255,255,0.08);
        transform:translateY(-1px);
    }
    body.light .forum-table tbody tr:hover{
        background:rgba(110,59,255,0.05);
    }

    .col-id{
        width:50px;
        font-variant-numeric:tabular-nums;
        opacity:.8;
    }
    .forum-title-cell{
        font-weight:600;
        color:#fff;
    }
    body.light .forum-title-cell {
        color:#1a1a1a;
    }
    .forum-desc-cell{
        max-width:320px;
        color:#c7c7ff;
        opacity:.9;
    }
    body.light .forum-desc-cell{
        color:#6b7280;
    }
    .forum-meta-small{
        font-size:11px;
        opacity:.7;
    }

    /* colonne créé par */
    .creator-cell{
        white-space:nowrap;
    }
    .creator-name{
        font-size:13px;
        font-weight:600;
    }
    .creator-tag{
        font-size:11px;
        opacity:.65;
    }

    .badge-soft{
        display:inline-flex;
        align-items:center;
        padding:3px 8px;
        border-radius:999px;
        font-size:11px;
        background:rgba(16,185,129,.15);
        color:#6ee7b7;
    }
    body.light .badge-soft{
        background:rgba(16,185,129,.08);
        color:#047857;
    }

    .table-actions{
        display:flex;
        justify-content:flex-end;
        gap:6px;
        flex-wrap:wrap;
    }
    .btn-xs{
        border-radius:999px;
        border:none;
        padding:5px 9px;
        font-size:11px;
        cursor:pointer;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        gap:4px;
        transition:.16s;
    }
    .btn-xs span{
        font-size:12px;
    }

    .btn-outline{
        border:1px solid rgba(129,140,248,.7);
        color:#c7d2fe;
        background:transparent;
    }
    .btn-outline:hover{
        background:rgba(129,140,248,.18);
    }

    .btn-outline-danger{
        border:1px solid rgba(248,113,113,.8);
        color:#fecaca;
        background:transparent;
    }
    .btn-outline-danger:hover{
        background:rgba(248,113,113,.18);
    }

    .btn-outline-secondary{
        border:1px solid rgba(148,163,184,.8);
        color:#e5e7eb;
        background:transparent;
    }
    .btn-outline-secondary:hover{
        background:rgba(148,163,184,.18);
    }
    body.light .btn-outline,
    body.light .btn-outline-danger,
    body.light .btn-outline-secondary{
        color:#111827;
    }
</style>

<div class="page-head">
    <div class="page-title-block">
        <h1>Forums</h1>
        <p>Gestion des catégories de discussion visibles sur MindArena.</p>
    </div>

    <div class="page-head-actions">
        <div class="chip">
            Total forums : <strong><?= (int)$totalForums ?></strong>
        </div>
        <a href="admin.php?action=forum-add" class="btn-primary-pill">
            <span>+</span> Nouveau forum
        </a>
    </div>
</div>

<div class="surface-card">
    <div class="toolbar-row">
        <div class="search-input-wrap">
            <input type="text" id="forumSearchAdmin" placeholder="Rechercher un forum (titre, description, créé par)…">
            <i class="ri-search-line"></i>
        </div>
    </div>

    <?php if (empty($forums)): ?>
        <p style="opacity:.75;font-size:13px;margin:6px 4px 14px;">
            Aucun forum n’a été créé pour le moment.
        </p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="forum-table" id="forumTableAdmin">
                <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Forum</th>
                    <th>Description</th>
                    <th>Créé par</th>
                    <th>Infos</th>
                    <th style="width:210px;text-align:right;">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($forums as $f): ?>
                    <?php
                    $creator = $f['created_by'] ?? 'Admin';
                    ?>
                    <tr data-search="<?= strtolower(
                            htmlspecialchars(
                                ($f['title'] ?? '').' '.
                                ($f['description'] ?? '').' '.
                                $creator
                            )
                        ) ?>">
                        <td class="col-id">#<?= (int)$f['id'] ?></td>

                        <td class="forum-title-cell">
                            <?= htmlspecialchars($f['title']) ?>
                        </td>

                        <td class="forum-desc-cell">
                            <?php if (!empty($f['description'])): ?>
                                <?= nl2br(htmlspecialchars(mb_strimwidth($f['description'],0,120,'…'))) ?>
                            <?php else: ?>
                                <span style="opacity:.55;">Aucune description.</span>
                            <?php endif; ?>
                        </td>

                        <!-- COLONNE CRÉÉ PAR -->
                        <td class="creator-cell">
                            <div class="creator-name">
                                <?= htmlspecialchars($creator) ?>
                            </div>
                            <div class="creator-tag">
                                Créateur du forum
                            </div>
                        </td>

                        <td>
                            <span class="forum-meta-small">
                                Créé le <?= htmlspecialchars($f['created_at'] ?? '') ?>
                            </span><br>
                            <span class="badge-soft">Actif</span>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a class="btn-xs btn-outline-secondary"
                                   href="admin.php?action=publications&amp;forum_id=<?= (int)$f['id'] ?>">
                                    <span class="ri-article-line"></span> Publications
                                </a>

                                <a class="btn-xs btn-outline"
                                   href="admin.php?action=forum-edit&amp;id=<?= (int)$f['id'] ?>">
                                    <span class="ri-edit-line"></span> Modifier
                                </a>

                                <form action="admin.php?action=forum-delete" method="post" class="d-inline-block" onsubmit="return confirm('Supprimer ce forum et toutes ses publications ?');">
                                    <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                                    <button type="submit" class="btn-xs btn-outline-danger">
                                        <span class="ri-delete-bin-6-line"></span> Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
// filtre côté admin (titre, description, créé par)
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('forumSearchAdmin');
    const rows  = document.querySelectorAll('#forumTableAdmin tbody tr');
    if (!input || !rows.length) return;

    input.addEventListener('input', () => {
        const q = input.value.toLowerCase().trim();
        rows.forEach(row => {
            const txt = (row.dataset.search || '').toLowerCase();
            row.style.display = txt.includes(q) ? '' : 'none';
        });
    });
});
</script>
