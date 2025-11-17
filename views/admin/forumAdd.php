<h1 class="mb-4">Ajouter un Forum</h1>

<div class="card-dark">
<form method="POST" action="admin.php?action=forum-add">

    <label>Titre</label>
    <input class="form-control mb-3" name="title" required>

    <label>Description</label>
    <textarea class="form-control mb-3" name="description" rows="4" required></textarea>

    <button class="btn-create">Créer</button>

</form>
</div>
