<h1 class="mb-4">Modifier Forum</h1>

<div class="card-dark">
<form method="POST" action="admin.php?action=forum-edit&id=<?= $forum['id'] ?>">

    <label>Titre</label>
    <input class="form-control mb-3" name="title" value="<?= htmlspecialchars($forum['title']) ?>" required>

    <label>Description</label>
    <textarea class="form-control mb-3" name="description" rows="4" required><?= htmlspecialchars($forum['description']) ?></textarea>

    <button class="btn-create">Enregistrer</button>

</form>
</div>
