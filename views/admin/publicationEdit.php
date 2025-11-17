<h1 class="mb-4">Modifier Publication</h1>

<div class="card-dark">
<form method="POST" action="admin.php?action=publication-edit&id=<?= $pub['id'] ?>">

    <label>Forum</label>
    <select class="form-control mb-3" name="forum_id">
        <?php foreach ($forums as $f): ?>
            <option value="<?= $f['id'] ?>" <?= ($pub['forum_id']==$f['id'])?'selected':'' ?>>
                <?= htmlspecialchars($f['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Auteur</label>
    <input class="form-control mb-3" name="author" value="<?= htmlspecialchars($pub['author']) ?>" required>

    <label>Contenu</label>
    <textarea class="form-control mb-3" name="content" rows="4" required><?= htmlspecialchars($pub['content']) ?></textarea>

    <button class="btn-create">Enregistrer</button>

</form>
</div>
