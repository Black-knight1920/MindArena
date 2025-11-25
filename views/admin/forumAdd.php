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
    }
    .page-head-simple p{
        margin:0;
        font-size:13px;
        opacity:.75;
    }

    .form-shell{
        max-width:640px;
        margin:0 auto;
    }
    .form-card{
        background:rgba(15,23,42,.9);
        border-radius:18px;
        padding:22px 22px 18px;
        border:1px solid rgba(148,163,184,.35);
        box-shadow:0 20px 45px rgba(15,23,42,.7);
    }
    body.light .form-card{
        background:#ffffff;
        border-color:rgba(148,163,184,.25);
        box-shadow:0 18px 40px rgba(15,23,42,.08);
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
    }
    .hint{
        font-size:11px;
        opacity:.7;
    }

    .field-input,
    .field-textarea{
        width:100%;
        border-radius:12px;
        border:1px solid rgba(148,163,184,.45);
        background:rgba(15,23,42,.9);
        color:#e5e7eb;
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
        border-color:#8b5cf6;
        box-shadow:0 0 0 1px rgba(139,92,246,.4);
    }
    body.light .field-input,
    body.light .field-textarea{
        background:#f9fafb;
        color:#111827;
        border-color:rgba(148,163,184,.6);
    }

    .error-text{
        font-size:11px;
        color:#fca5a5;
        margin-top:2px;
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
    }
    .btn-secondary{
        background:transparent;
        border:1px solid rgba(148,163,184,.7);
        color:#e5e7eb;
    }
    .btn-secondary:hover{
        background:rgba(148,163,184,.15);
    }
    body.light .btn-secondary{
        color:#111827;
    }

    .badge-counter{
        font-size:11px;
        opacity:.7;
    }
</style>

<div class="page-head-simple">
    <h1>Nouveau forum</h1>
    <p>Crée une nouvelle catégorie de discussion pour la communauté.</p>
</div>

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
                <div class="hint">Un titre clair et concis (3 à 80 caractères).</div>
                <div class="error-text" id="titleError">
                    <?= isset($errors['title']) ? htmlspecialchars($errors['title']) : '' ?>
                </div>
            </div>

            <!-- créé par -->
            <div class="form-row">
                <label for="created_by">
                    <span>Créé par</span>
                </label>
                <input
                    type="text"
                    id="created_by"
                    name="created_by"
                    class="field-input"
                    maxlength="100"
                    value="<?= htmlspecialchars($createdBy) ?>"
                    placeholder="Nom de l’admin ou du modérateur"
                >
                <div class="hint">Laisser vide pour utiliser la valeur par défaut (par ex. “Admin”).</div>
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
                    placeholder="Décrivez brièvement le rôle de ce forum…"
                ><?= htmlspecialchars($description) ?></textarea>
                <div class="hint">
                    S’affichera sur la page du forum pour guider les utilisateurs.
                </div>
            </div>

            <div class="form-footer">
                <a href="admin.php?action=forums" class="btn-secondary">
                    ← Retour à la liste
                </a>

                <button type="submit" class="btn-primary-pill" id="submitBtn">
                    <span class="ri-check-line"></span> Créer le forum
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validation dynamique du titre
document.addEventListener('DOMContentLoaded', () => {
    const titleInput   = document.getElementById('title');
    const titleError   = document.getElementById('titleError');
    const titleCounter = document.getElementById('titleCounter');
    const form         = document.getElementById('forumCreateForm');
    const submitBtn    = document.getElementById('submitBtn');

    if (!titleInput) return;

    const validateTitle = () => {
        const val = titleInput.value.trim();
        const len = val.length;
        titleCounter.textContent = len + ' / 80';

        let message = '';
        if (len === 0) {
            message = 'Le titre est obligatoire.';
        } else if (len < 3) {
            message = 'Le titre doit contenir au moins 3 caractères.';
        }

        titleError.textContent = message;
        const ok = message === '';
        if (submitBtn) submitBtn.disabled = !ok;
        return ok;
    };

    titleInput.addEventListener('input', validateTitle);
    validateTitle();

    form.addEventListener('submit', (e) => {
        if (!validateTitle()) {
            e.preventDefault();
            titleInput.focus();
        }
    });
});
</script>
