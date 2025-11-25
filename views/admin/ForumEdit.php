<?php
$title       = $forum['title']       ?? '';
$description = $forum['description'] ?? '';
$createdBy   = $forum['created_by']  ?? '';
$errors      = $errors ?? [];
?>
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h5 mb-1">Modifier le forum</h1>
                <p class="text-muted small mb-4">
                    Mets à jour les informations de cet espace de discussion.
                </p>

                <form method="post" id="forumEditForm" novalidate>
                    <div class="mb-3">
                        <label for="forumTitle" class="form-label">
                            Titre du forum <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="forumTitle"
                            class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                            value="<?= htmlspecialchars($title) ?>"
                            required
                        >
                        <div class="invalid-feedback">
                            <?= $errors['title'] ?? "Le titre est obligatoire." ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="forumDescription" class="form-label">
                            Description
                        </label>
                        <textarea
                            name="description"
                            id="forumDescription"
                            class="form-control"
                            rows="3"
                        ><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="forumCreatedBy" class="form-label">
                            Créé par
                        </label>
                        <input
                            type="text"
                            name="created_by"
                            id="forumCreatedBy"
                            class="form-control"
                            value="<?= htmlspecialchars($createdBy) ?>"
                            placeholder="Admin, Modérateur…"
                        >
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="admin.php?action=forums" class="btn btn-link text-muted p-0">
                            <i class="ri-arrow-left-line"></i> Retour à la liste
                        </a>
                        <div class="d-flex gap-2">
                            <a href="admin.php?action=forum-delete&id=<?= (int)$forum['id'] ?>"
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Supprimer ce forum ?');">
                                Supprimer
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ri-save-3-line"></i> Enregistrer
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validation dynamique côté client
document.addEventListener('DOMContentLoaded', () => {
    const form  = document.getElementById('forumEditForm');
    const title = document.getElementById('forumTitle');

    function validateTitle() {
        if (title.value.trim() === '') {
            title.classList.add('is-invalid');
            return false;
        } else {
            title.classList.remove('is-invalid');
            return true;
        }
    }

    title.addEventListener('input', validateTitle);

    form.addEventListener('submit', (e) => {
        if (!validateTitle()) {
            e.preventDefault();
        }
    });
});
</script>
