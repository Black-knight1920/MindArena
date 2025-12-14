<?php
// $publications : liste
// $forum (optionnel) : forum filtré
$sort = $sort ?? ($_GET['sort'] ?? 'date');
$dir  = $dir ?? ($_GET['dir'] ?? 'desc');
?>
<style>
    .page-header {
        margin-bottom: 24px;
    }
    .page-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 4px;
        color: #fff;
    }
    body.light .page-title {
        color: #1a1a1a;
    }
    .page-subtitle {
        font-size: 13px;
        opacity: .75;
        color: #c7c7ff;
    }
    body.light .page-subtitle {
        color: #6b7280;
    }
    .card-shell {
        background: #1b1b30;
        border-radius: 16px;
        padding: 18px;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 0 18px rgba(0,0,0,0.35);
        transition: .25s;
    }
    .card-shell:hover {
        box-shadow: 0 0 25px rgba(120,60,255,0.3);
    }
    body.light .card-shell {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 0 18px rgba(0,0,0,0.1);
    }
    body.light .card-shell:hover {
        box-shadow: 0 0 25px rgba(0,0,0,0.15);
    }
    .text-muted-soft {
        color: #c7c7ff;
        opacity: .7;
    }
    body.light .text-muted-soft {
        color: #6b7280;
    }
    .table-glass tbody td.small {
        color: #efefff;
    }
    body.light .table-glass tbody td.small {
        color: #1a1a1a;
    }
    .btn-pill {
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: .18s;
    }
    .btn-outline-secondary {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.2);
        color: #c7c7ff;
    }
    .btn-outline-secondary:hover {
        background: rgba(255,255,255,0.1);
        border-color: rgba(255,255,255,0.3);
    }
    body.light .btn-outline-secondary {
        color: #6b7280;
        border-color: rgba(0,0,0,0.2);
    }
    .btn-primary-soft {
        background: linear-gradient(135deg,#6e3bff,#a678ff);
        color: #fff;
        border: none;
        box-shadow: 0 10px 25px rgba(110,59,255,.45);
    }
    .btn-primary-soft:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg,#8358ff,#c49aff);
        box-shadow: 0 16px 32px rgba(110,59,255,.55);
    }
    .btn-danger-soft {
        background: rgba(220,38,38,0.2);
        color: #fca5a5;
        border: 1px solid rgba(220,38,38,0.4);
    }
    .btn-danger-soft:hover {
        background: rgba(220,38,38,0.3);
        color: #fff;
    }
    body.light .btn-danger-soft {
        color: #dc2626;
        background: rgba(220,38,38,0.1);
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
    .btn-disabled{
        opacity:.45;
        pointer-events:none;
    }
</style>
<div class="page-header">
    <div>
        <?php if (isset($forum['title'])): ?>
            <p class="page-subtitle mb-1" style="font-size: 12px;">
                <i class="ri-arrow-left-line"></i>
                <a href="admin.php?action=forums" style="opacity: 0.7;">Forums</a>
                <span style="opacity: 0.5;"> / </span>
                <span><?= htmlspecialchars($forum['title']) ?></span>
            </p>
        <?php endif; ?>
        <h1 class="page-title mb-1">
            <?= isset($forum['title']) ? htmlspecialchars($forum['title']) : 'Publications' ?>
        </h1>
        <p class="page-subtitle mb-0">
            <?= isset($forum['title'])
                ? 'Toutes les publications de ce forum.'
                : 'Toutes les publications de tous les forums.'
            ?>
        </p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <form method="get" class="d-flex align-items-center gap-2 flex-wrap">
            <input type="hidden" name="action" value="publications">
            <?php if (!empty($forum['id'])): ?>
                <input type="hidden" name="forum_id" value="<?= (int)$forum['id'] ?>">
            <?php endif; ?>
            <label class="form-label mb-0" for="sort">Trier par</label>
            <select name="sort" id="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="date" <?= $sort === 'date' ? 'selected' : '' ?>>Date</option>
                <option value="title" <?= $sort === 'title' ? 'selected' : '' ?>>Titre</option>
                <option value="author" <?= $sort === 'author' ? 'selected' : '' ?>>Auteur</option>
            </select>
            <select name="dir" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="desc" <?= $dir === 'desc' ? 'selected' : '' ?>>Desc</option>
                <option value="asc" <?= $dir === 'asc' ? 'selected' : '' ?>>Asc</option>
            </select>
        </form>
        <?php if (isset($forum['id'])): ?>
            <a href="admin.php?action=forums" class="btn btn-outline-secondary btn-pill">
                <i class="ri-arrow-left-line me-1"></i> Retour aux forums
            </a>
        <?php endif; ?>
        <a href="admin.php?action=publication-add<?= isset($forum['id']) ? '&forum_id='.(int)$forum['id'] : '' ?>"
           class="btn btn-primary-soft btn-pill">
            <i class="ri-add-line me-1"></i> Nouvelle publication
        </a>
    </div>
</div>

<div class="card-shell">
    <?php if (empty($publications)): ?>
        <p class="text-muted-soft mb-0">Aucune publication pour le moment.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table-glass">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php if (!isset($forum['title'])): ?>
                            <th>Forum</th>
                        <?php endif; ?>
                        <th>Auteur</th>
                        <th>Contenu</th>
                        <th>Créée le</th>
                        <th>Signalements</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($publications as $p): ?>
                    <?php $reportCount = (int)($p['reports_count'] ?? 0); $hasReports = $reportCount > 0; ?>
                    <tr>
                        <td class="small">#<?= (int)$p['id'] ?></td>
                        <?php if (!isset($forum['title'])): ?>
                            <td class="small">
                                <?= htmlspecialchars($p['forum_title'] ?? 'Forum supprimé') ?>
                            </td>
                        <?php endif; ?>
                        <td class="small">
                            <?php 
                                $author = $p['author'] ?? '';
                                if (empty($author) || $author === 'Anonyme') {
                                    echo '<span class="text-muted-soft">Anonyme</span>';
                                } else {
                                    echo htmlspecialchars($author);
                                }
                            ?>
                        </td>
                        <td class="small">
                            <?php
                                $content = $p['content'] ?? '';
                                $snippet = mb_substr($content, 0, 80);
                                if (mb_strlen($content) > 80) $snippet .= '…';
                                echo nl2br(htmlspecialchars($snippet));
                            ?>
                        </td>
                        <td class="small">
                            <?= !empty($p['created_at'])
                                ? htmlspecialchars($p['created_at'])
                                : '<span class="text-muted-soft">—</span>' ?>
                        </td>
                        <td class="small">
                            <span class="badge-soft"><?= $reportCount ?></span>
                        </td>
                        <td class="text-end">
                            <!-- Voir côté front -->
                            <?php if (!empty($p['forum_id'])): ?>
                                <a href="index.php?action=publications&amp;forum_id=<?= (int)$p['forum_id'] ?>"
                                   target="_blank"
                                   class="btn btn-outline-secondary btn-pill btn-sm me-1">
                                    <i class="ri-external-link-line me-1"></i> Voir front
                                </a>
                            <?php endif; ?>

                            <!-- Modifier -->
                            <a href="<?= $hasReports ? 'admin.php?action=publication-edit&amp;id='.(int)$p['id'] : '#' ?>"
                               class="btn btn-primary-soft btn-pill btn-sm me-1 <?= $hasReports ? '' : 'btn-disabled' ?>"
                               title="<?= $hasReports ? '' : 'Modification possible seulement après signalement' ?>">
                                <i class="ri-edit-2-line"></i>
                            </a>

                            <!-- Supprimer -->
                            <form action="admin.php?action=publication-delete" method="post" class="d-inline-block me-1" onsubmit="return confirm('Supprimer cette publication ?');">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="btn btn-danger-soft btn-pill btn-sm <?= $hasReports ? '' : 'btn-disabled' ?>" <?= $hasReports ? '' : 'disabled' ?> title="<?= $hasReports ? '' : 'Suppression possible seulement après signalement' ?>">
                                    <i class="ri-delete-bin-6-line"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

