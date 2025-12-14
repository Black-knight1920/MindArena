<?php
// $publication et $forums fournis par AdminController::publicationEdit()
?>
<style>
    .page-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #fff;
    }
    body.light .page-title {
        color: #1a1a1a;
    }
    .text-muted {
        color: #c7c7ff;
        opacity: .7;
    }
    body.light .text-muted {
        color: #6b7280;
    }
</style>
<h1 class="page-title mb-3">Modifier la publication</h1>
<p class="text-muted mb-4">
    Ajustez le contenu ou le forum cible de cette publication.
</p>

<style>
    .form-shell {
        max-width: 900px;
        margin: 0 auto 40px;
    }
    .form-card {
        border-radius: 16px;
        background: #1b1b30;
        box-shadow: 0 0 18px rgba(0,0,0,0.35);
        border: 1px solid rgba(255,255,255,0.08);
        padding: 26px 26px 22px;
        transition: .25s;
    }
    .form-card:hover {
        box-shadow: 0 0 25px rgba(120,60,255,0.3);
    }
    body.light .form-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 0 18px rgba(0,0,0,0.1);
    }
    body.light .form-card:hover {
        box-shadow: 0 0 25px rgba(0,0,0,0.15);
    }
    .form-card-header {
        display:flex;justify-content:space-between;align-items:center;
        margin-bottom:18px;
    }
    .form-card-header-left {
        display:flex;align-items:center;gap:14px;
    }
    .form-icon {
        width:40px;height:40px;border-radius:999px;
        background:radial-gradient(circle at 30% 20%,#fff,transparent 40%),
                   linear-gradient(135deg,#00d0ff,#7b42ff);
        display:flex;align-items:center;justify-content:center;
        color:#fff;box-shadow:0 10px 25px rgba(0,0,0,0.35);
    }
    .form-chip {
        display:inline-flex;align-items:center;gap:6px;
        border-radius:999px;padding:4px 10px;font-size:12px;
        background:rgba(0,0,0,0.22);color:rgba(255,255,255,0.82);
    }
    body.light .form-chip {
        background:rgba(0,0,0,0.04);color:#555;
    }
    .floating-label { font-size:14px;font-weight:600;margin-bottom:6px; }
    .field-hint { font-size:12px;opacity:.75; }
    .counter-badge {
        font-size:12px;padding:2px 8px;border-radius:999px;
        background:rgba(0,0,0,0.2);
    }
    body.light .counter-badge {
        background:rgba(0,0,0,0.05);
    }
</style>

<div class="form-shell">
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-header-left">
                <div class="form-icon">
                    <i class="ri-edit-2-line"></i>
                </div>
                <div>
                    <div class="fw-semibold">Édition de la publication</div>
                    <div class="text-muted" style="font-size: 13px;">
                        Corrigez le texte ou changez de forum si nécessaire.
                    </div>
                </div>
            </div>
            <span class="form-chip">
                ID #<?= (int)$publication['id'] ?>
            </span>
        </div>

        <form action="admin.php?action=publication-edit&id=<?= (int)$publication['id'] ?>" method="post" id="pubEditForm" novalidate>
            <?php if (!empty($errors['general'])): ?>
                <div class="mb-3">
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($errors['general']) ?>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Forum -->
            <div class="mb-3">
                <label for="forum_id" class="floating-label">
                    Forum concerné <span class="text-danger">*</span>
                </label>
                <select name="forum_id" id="forum_id" class="form-select <?= isset($errors['forum_id']) ? 'is-invalid' : '' ?>" required>
                    <?php foreach ($forums as $f): ?>
                        <option value="<?= (int)$f['id'] ?>"
                            <?= ($f['id'] == ($forum_id ?? $publication['forum_id'])) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="field-hint mt-1">
                    Vous pouvez déplacer cette publication vers un autre forum.
                </div>
                <div class="invalid-feedback" id="forumError"><?= isset($errors['forum_id']) ? htmlspecialchars($errors['forum_id']) : '' ?></div>
            </div>

            <!-- Auteur -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="author" class="floating-label">Auteur</label>
                    <span id="authorCount" class="counter-badge">0 / 40</span>
                </div>
                <input
                    type="text"
                    name="author"
                    id="author"
                    class="form-control <?= isset($errors['author']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($author ?? $publication['author'] ?? '') ?>"
                    placeholder="Nom ou pseudo (laissez vide pour anonyme)"
                >
                <div class="field-hint mt-1">
                    Maximum 40 caractères. Laissez vide pour publier en tant qu’auteur anonyme.
                </div>
                <div class="invalid-feedback" id="authorError"><?= isset($errors['author']) ? htmlspecialchars($errors['author']) : '' ?></div>
            </div>

            <!-- Contenu -->
            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="content" class="floating-label">
                        Contenu <span class="text-danger">*</span>
                    </label>
                    <span id="contentCount" class="counter-badge">0 / 1000</span>
                </div>
                <textarea
                    name="content"
                    id="content"
                    rows="5"
                    class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>"
                    required
                ><?= htmlspecialchars($content ?? $publication['content']) ?></textarea>
                <div class="field-hint mt-1">
                    Entre 10 et 1000 caractères. Restez clair et respectueux.
                </div>
                <div class="invalid-feedback" id="contentError"><?= isset($errors['content']) ? htmlspecialchars($errors['content']) : '' ?></div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="admin.php?action=publications<?= isset($forum_id) && $forum_id > 0 ? '&amp;forum_id='.(int)$forum_id : '' ?>" class="btn btn-outline-secondary">
                    ← Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-3-line me-1"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const form          = document.getElementById('pubEditForm');
    const forumSelect   = document.getElementById('forum_id');
    const author        = document.getElementById('author');
    const content       = document.getElementById('content');

    const forumError    = document.getElementById('forumError');
    const authorError   = document.getElementById('authorError');
    const contentError  = document.getElementById('contentError');

    const authorCount   = document.getElementById('authorCount');
    const contentCount  = document.getElementById('contentCount');

    function validateForum() {
        if (!forumSelect.value) {
            forumSelect.classList.add('is-invalid');
            forumError.textContent = 'Veuillez sélectionner un forum.';
            return false;
        }
        forumSelect.classList.remove('is-invalid');
        forumError.textContent = '';
        return true;
    }

    function validateAuthor() {
        const value = author.value.trim();
        const len   = value.length;
        authorCount.textContent = len + ' / 40';

        if (len > 40) {
            author.classList.add('is-invalid');
            authorError.textContent = 'Le nom de l’auteur ne doit pas dépasser 40 caractères.';
            return false;
        }
        // If server sent an error, keep it shown until user corrects it
        if (author.classList.contains('is-invalid') && authorError.textContent === '') {
            author.classList.remove('is-invalid');
        }
        if (!author.classList.contains('is-invalid')) authorError.textContent = '';
        return true;
    }

    function validateContent() {
        const value = content.value.trim();
        const len   = value.length;
        contentCount.textContent = len + ' / 1000';

        let msg = '';
        if (len === 0) {
            msg = 'Le contenu est obligatoire.';
        } else if (len < 10) {
            msg = 'Le message doit contenir au moins 10 caractères.';
        } else if (len > 1000) {
            msg = 'Le message ne doit pas dépasser 1000 caractères.';
        }

        if (msg) {
            content.classList.add('is-invalid');
            contentError.textContent = msg;
            return false;
        }
        if (content.classList.contains('is-invalid') && contentError.textContent === '') {
            content.classList.remove('is-invalid');
        }
        if (!content.classList.contains('is-invalid')) contentError.textContent = '';
        return true;
    }

    forumSelect.addEventListener('change', validateForum);
    author.addEventListener('input', validateAuthor);
    content.addEventListener('input', validateContent);

    form.addEventListener('submit', function (e) {
        const ok =
            validateForum() &
            // Initialize counters based on current values (may come from server)
            authorCount.textContent = (author.value || '').trim().length + ' / 40';
            contentCount.textContent = (content.value || '').trim().length + ' / 1000';
            validateAuthor();
            validateContent();

        if (!ok) e.preventDefault();
    });

    validateAuthor();
    validateContent();
})();
</script>

