<h1 class="mb-4">Gestion des Forums</h1>

<a href="admin.php?action=forum-add" class="btn-create mb-3">➕ Nouveau Forum</a>

<div class="card-dark">
<table class="table-glass">
    <thead>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Description</th>
            <th style="width:160px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($forums as $f): ?>
        <tr>
            <td><?= $f['id'] ?></td>
            <td><?= htmlspecialchars($f['title']) ?></td>
            <td><?= htmlspecialchars($f['description']) ?></td>
            <td>
                <a href="admin.php?action=forum-edit&id=<?= $f['id'] ?>" class="btn-edit">Modifier</a>
                <a href="admin.php?action=forum-delete&id=<?= $f['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
