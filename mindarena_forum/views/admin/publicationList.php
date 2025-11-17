<h1 class="mb-4">Gestion des Publications</h1>

<a href="admin.php?action=publication-add" class="btn-create mb-3">➕ Nouvelle Publication</a>

<div class="card-dark">
<table class="table-glass">
    <thead>
        <tr>
            <th>ID</th>
            <th>Forum</th>
            <th>Auteur</th>
            <th>Contenu</th>
            <th style="width:160px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($publications as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['forum_title']) ?></td>
            <td><?= htmlspecialchars($p['author']) ?></td>
            <td><?= htmlspecialchars(substr($p['content'], 0, 50)) ?>...</td>
            <td>
                <a href="admin.php?action=publication-edit&id=<?= $p['id'] ?>" class="btn-edit">Modifier</a>
                <a href="admin.php?action=publication-delete&id=<?= $p['id'] ?>" onclick="return confirm('Supprimer ?')" class="btn-delete">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
