<h1 class="mb-4">Créer Publication</h1>

<div class="card-dark">
<form method="POST" action="admin.php?action=publication-add">

    <label>Forum</label>
    <select class="form-control mb-3" name="forum_id">
        <?php foreach ($forums as $f): ?>
            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['title']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Auteur</label>
    <input class="form-control mb-3" name="author" required>

    <label>Contenu</label>
    <textarea class="form-control mb-3" name="content" rows="4" required></textarea>

    <button class="btn-create">Créer</button>

</form>
</div>
