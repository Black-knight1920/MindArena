<?php
// $publications : liste
// $forum (optionnel) : forum filtré
?>
<div class="page-header">
    <div>
        <h1 class="page-title mb-1">
            Publications<?= isset($forum['title']) ? ' – ' . htmlspecialchars($forum['title']) : '' ?>
        </h1>
        <p class="page-subtitle mb-0">
            <?= isset($forum['title'])
                ? 'Toutes les publications du forum sélectionné.'
                : 'Toutes les publications des forums.'
            ?>
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="admin.php?action=forums" class="btn btn-outline-secondary btn-pill">
            <i class="ri-arrow-left-line me-1"></i> Forums
        </a>
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
                        <th>Forum</th>
                        <th>Auteur</th>
                        <th>Contenu</th>
                        <th>Créée le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($publications as $p): ?>
                    <tr>
                        <td class="small">#<?= (int)$p['id'] ?></td>
                        <td class="small">
                            <?= htmlspecialchars($p['forum_title'] ?? 'Forum supprimé') ?>
                        </td>
                        <td class="small">
                            <?= htmlspecialchars($p['author'] ?? 'Anonyme') ?>
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
                        <td class="text-end">
                            <!-- Voir côté front -->
                            <?php if (!empty($p['forum_id'])): ?>
                                <a href="front/publications?forum_id=<?= (int)$p['forum_id'] ?>"
                                   target="_blank"
                                   class="btn btn-outline-secondary btn-pill btn-sm me-1">
                                    <i class="ri-external-link-line me-1"></i> Voir front
                                </a>
                            <?php endif; ?>

                            <!-- Modifier -->
                            <a href="admin.php?action=publication-edit&amp;id=<?= (int)$p['id'] ?>"
                               class="btn btn-primary-soft btn-pill btn-sm me-1">
                                <i class="ri-edit-2-line"></i>
                            </a>

                            <!-- Supprimer -->
                            <a href="admin.php?action=publication-delete&amp;id=<?= (int)$p['id'] ?>"
                               class="btn btn-danger-soft btn-pill btn-sm"
                               onclick="return confirm('Supprimer cette publication ?');">
                                <i class="ri-delete-bin-6-line"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
