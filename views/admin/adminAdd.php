<?php
// views/admin/adminAdd.php
?>
<div class="content-wrapper">
    <div class="content-header">
        <h1>Créer un administrateur</h1>
        <p>Ajouter un nouveau compte admin (nom + mot de passe).</p>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="name" class="form-control" required minlength="3" maxlength="50">
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" class="form-control" required minlength="4" maxlength="100">
                </div>
                <button type="submit" class="btn btn-primary">Créer</button>
            </form>
        </div>
    </div>
</div>

