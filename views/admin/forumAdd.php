<?php
// views/admin/forumAdd.php
$title       = $title       ?? '';
$description = $description ?? '';
$createdBy   = $createdBy   ?? '';
$errors      = $errors      ?? [];
?>
<style>
    .page-head-simple{
        margin-bottom:18px;
    }
    .page-head-simple h1{
        font-size:22px;
        font-weight:700;
        margin:0 0 4px;
        color:#fff;
    }
    body.light .page-head-simple h1 {
        color:#1a1a1a;
    }
    .page-head-simple p{
        margin:0;
        font-size:13px;
        opacity:.75;
        color:#c7c7ff;
    }
    body.light .page-head-simple p {
        color:#6b7280;
    }

    .form-shell{
        max-width:640px;
        margin:0 auto;
    }
    .form-card{
        background:#1b1b30;
        border-radius:16px;
        padding:22px 22px 18px;
        border:1px solid rgba(255,255,255,0.08);
        box-shadow:0 0 18px rgba(0,0,0,0.35);
        transition:.25s;
    }
    .form-card:hover {
        box-shadow:0 0 25px rgba(120,60,255,0.3);
    }
    body.light .form-card{
        background:#ffffff;
        border:1px solid rgba(0,0,0,0.1);
        box-shadow:0 0 18px rgba(0,0,0,0.1);
    }
    body.light .form-card:hover {
        box-shadow:0 0 25px rgba(0,0,0,0.15);
    }

    .form-row{
        margin-bottom:14px;
    }
    .form-row label{
        display:flex;
        justify-content:space-between;
        font-size:13px;
        margin-bottom:5px;
        font-weight:500;
        color:#fff;
    }
    body.light .form-row label {
        color:#1a1a1a;
    }
    .hint{
        font-size:11px;
        opacity:.7;
        color:#c7c7ff;
    }
    body.light .hint {
        color:#6b7280;
    }

    .field-input,
    .field-textarea{
        width:100%;
        border-radius:12px;
        border:1px solid rgba(255,255,255,0.15);
        background:rgba(255,255,255,0.08);
        color:#fff;
        font-size:14px;
        padding:9px 11px;
        outline:none;
        transition:.15s;
        resize:vertical;
        min-height:90px;
    }
    .field-input{
        min-height:auto;
    }
    .field-input:focus,
    .field-textarea:focus{
        border-color:#6e3bff;
        box-shadow:0 0 0 1px rgba(110,59,255,.4);
        background:rgba(255,255,255,0.12);
    }
    body.light .field-input,
    body.light .field-textarea{
        background:rgba(0,0,0,0.05);
        color:#1a1a1a;
        border-color:rgba(0,0,0,0.15);
    }
    body.light .field-input:focus,
    body.light .field-textarea:focus{
        background:rgba(0,0,0,0.08);
    }

    .error-text{
        font-size:11px;
        color:#fca5a5;
        margin-top:2px;
        display:none;
    }

    .form-footer{
        margin-top:18px;
        display:flex;
        justify-content:space-between;
        flex-wrap:wrap;
        gap:10px;
        align-items:center;
    }

    .btn-secondary,
    .btn-primary-pill{
        border-radius:999px;
        padding:8px 16px;
        font-size:13px;
        font-weight:600;
        border:none;
        cursor:pointer;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        gap:6px;
        background:linear-gradient(135deg,#6e3bff,#a678ff);
        color:#fff;
        box-shadow:0 10px 25px rgba(110,59,255,.45);
        transition:.18s;
    }
    .btn-primary-pill:hover{
        transform:translateY(-1px);
        background:linear-gradient(135deg,#8358ff,#c49aff);
        box-shadow:0 16px 32px rgba(110,59,255,.55);
    }
    .btn-secondary{
        background:transparent;
        border:1px solid rgba(255,255,255,0.2);
        color:#c7c7ff;
    }
    .btn-secondary:hover{
        background:rgba(255,255,255,0.1);
        border-color:rgba(255,255,255,0.3);
    }
    body.light .btn-secondary{
        color:#6b7280;
        border-color:rgba(0,0,0,0.2);
    }
    body.light .btn-secondary:hover{
        background:rgba(0,0,0,0.05);
    }

    .badge-counter{
        font-size:11px;
        opacity:.7;
    }
</style>

<div class="page-head-simple">
    <h1>Nouveau forum</h1>
    <p>Creer une nouvelle categorie de discussion pour la communaute.</p>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
<?php endif; ?>

<div class="form-shell">
    <div class="form-card">
        <form method="post" novalidate id="forumCreateForm">

            <!-- titre -->
            <div class="form-row">
                <label for="title">
                    <span>Titre du forum <span style="color:#f97373">*</span></span>
                    <span class="badge-counter" id="titleCounter">0 / 80</span>
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="field-input"
                    maxlength="80"
                    value="<?= htmlspecialchars($title) ?>"
                    placeholder="Ex. : Annonces officielles"
                    required
                >
                <div class="hint">Un titre clair et concis (3 a 80 caracteres).</div>
                <div class="error-text" id="titleError" style="<?= empty($errors['title']) ? '' : 'display:block;' ?>">
                    <?= htmlspecialchars($errors['title'] ?? '') ?>
                </div>
            </div>

            <!-- cree par -->
            <div class="form-row">
                <label for="created_by">
                    <span>Cree par</span>
                </label>
                <input
                    type="text"
                    id="created_by"
                    name="created_by"
                    class="field-input"
                    maxlength="100"
                    value="<?= htmlspecialchars($createdBy) ?>"
                    placeholder="Nom de l'admin ou du moderateur"
                >
                <div class="hint">Laisser vide pour utiliser la valeur par defaut (par ex. Admin).</div>
                <div class="error-text" id="createdByError" style="<?= empty($errors['created_by']) ? '' : 'display:block;' ?>">
                    <?= htmlspecialchars($errors['created_by'] ?? '') ?>
                </div>
            </div>

            <!-- description -->
            <div class="form-row">
                <label for="description">
                    <span>Description (optionnel)</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    class="field-textarea"
                    rows="4"
                    placeholder="Decrivez brievement le role de ce forum"
                ><?= htmlspecialchars($description) ?></textarea>
                <div class="hint">
                    S'affichera sur la page du forum pour guider les utilisateurs.
                </div>
                <div class="error-text" id="descriptionError" style="<?= empty($errors['description']) ? '' : 'display:block;' ?>">
                    <?= htmlspecialchars($errors['description'] ?? '') ?>
                </div>
            </div>

            <div class="form-footer">
                <a href="admin.php?action=forums" class="btn-secondary">
                    ← Retour a la liste
                </a>

                <button type="submit" class="btn-primary-pill" id="submitBtn">
                    Creer le forum
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validation dynamique du formulaire de creation forum (admin)
document.addEventListener('DOMContentLoaded', () => {
    const titleInput   = document.getElementById('title');
    const titleError   = document.getElementById('titleError');
    const titleCounter = document.getElementById('titleCounter');
    const form         = document.getElementById('forumCreateForm');
    const submitBtn    = document.getElementById('submitBtn');
    const description  = document.getElementById('description');
    const descriptionError = document.getElementById('descriptionError');
    const createdBy    = document.getElementById('created_by');
    const createdByError = document.getElementById('createdByError');

    if (!titleInput) return;

    const showError = (el, message) => {
        el.textContent = message;
        el.style.display = message ? 'block' : 'none';
    };

    const validateTitle = () => {
        const val = titleInput.value.trim();
        const len = val.length;
        titleCounter.textContent = len + ' / 80';

        let message = '';
        if (len === 0) {
            message = 'Le titre est obligatoire.';
        } else if (len < 3) {
            message = 'Le titre doit contenir au moins 3 caracteres.';
        } else if (len > 80) {
            message = 'Le titre ne doit pas depasser 80 caracteres.';
        }

        showError(titleError, message);
        return message === '';
    };

    const validateDescription = () => {
        const val = description.value.trim();
        if (val.length > 500) {
            showError(descriptionError, 'La description ne doit pas depasser 500 caracteres.');
            return false;
        }
        showError(descriptionError, '');
        return true;
    };

    const validateCreatedBy = () => {
        const val = createdBy.value.trim();
        if (val.length > 50) {
            showError(createdByError, 'Le nom du createur ne doit pas depasser 50 caracteres.');
            return false;
        }
        showError(createdByError, '');
        return true;
    };

    titleInput.addEventListener('input', validateTitle);
    description.addEventListener('input', validateDescription);
    createdBy.addEventListener('input', validateCreatedBy);
    validateTitle();
    validateDescription();
    validateCreatedBy();

    form.addEventListener('submit', (e) => {
        let ok = true;
        if (!validateTitle()) ok = false;
        if (!validateDescription()) ok = false;
        if (!validateCreatedBy()) ok = false;

        if (!ok) {
            e.preventDefault();
            if (submitBtn) submitBtn.disabled = false;
        }
    });
});
</script>

