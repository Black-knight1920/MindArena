<?php
// views/front/forumEdit.php
// Édition d'un forum par son créateur

$forum = $forum ?? null;
$errors = $errors ?? [];
$title = $title ?? '';
$description = $description ?? '';
$createdBy = $createdBy ?? '';

if (!$forum) {
    die("<h2 style='color:white;text-align:center;margin-top:50px'>❌ Forum introuvable</h2>");
}

// Base URL calculée automatiquement
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le forum - MindArena</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="/mindarena_forum/ENDGAME/img/favicon.ico" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/font-awesome.min.css">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/animate.css">
    <link rel="stylesheet" href="/mindarena_forum/ENDGAME/css/style.css">

    <style>
        :root {
            --ma-bg: #160820;
            --ma-card: rgba(8,22,36,0.95);
            --ma-border: rgba(255,255,255,0.10);
            --ma-accent: #ff4df0;
            --ma-accent-soft: #b01ba5;
            --ma-primary-glow: rgba(255,77,240,0.6);
        }

        body {
            margin: 0;
            font-family: "Roboto", sans-serif;
            background: radial-gradient(ellipse at top, #4d1b7d 0%, #2a0f4a 25%, #160820 50%, #0a0515 100%);
            background-attachment: fixed;
            color: #fff;
            padding-top: 110px;
            overflow-x: hidden;
        }

        .page-header {
            text-align: center;
            padding: 60px 20px 40px;
            position: relative;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--ma-accent), transparent);
            border-radius: 2px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #ff4df0, #ffb8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }

        .page-header p {
            color: #e1d7ff;
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .form-container {
            max-width: 700px;
            margin: 0 auto 60px;
            padding: 0 20px;
        }

        .form-card {
            background: linear-gradient(135deg, rgba(8,22,36,0.95), rgba(15,30,50,0.9));
            border-radius: 20px;
            padding: 35px 30px;
            border: 1px solid var(--ma-border);
            box-shadow: 0 25px 50px rgba(0,0,0,0.7);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 0.95rem;
            font-weight: 600;
            color: #ff9cff;
            margin-bottom: 8px;
        }

        .form-group label .required {
            color: #ff4b5c;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border-radius: 12px;
            border: 2px solid var(--ma-border);
            background: rgba(8,22,36,0.8);
            color: #fff;
            font-size: 0.95rem;
            font-family: "Roboto", sans-serif;
            outline: none;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #ff4df0;
            box-shadow: 0 0 20px rgba(255,77,240,0.4);
            background: rgba(8,22,36,1);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-group .hint {
            font-size: 0.85rem;
            color: #c7c7ff;
            opacity: 0.7;
            margin-top: 6px;
        }

        .error-message {
            color: #ff4b5c;
            font-size: 0.9rem;
            margin-top: 6px;
            display: block;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 999px;
            border: none;
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff4df0, #b01ba5);
            color: #fff;
            box-shadow: 0 0 20px rgba(255,77,240,0.6);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(255,77,240,0.9);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.2);
        }

        .security-note {
            background: rgba(255,202,95,0.15);
            border: 1px solid rgba(255,202,95,0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .security-note p {
            margin: 0;
            font-size: 0.9rem;
            color: #ffca5f;
        }

        .security-note strong {
            display: block;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<div class="page-header">
    <h1>Modifier le forum</h1>
    <p>Mets à jour les informations de ton forum</p>
</div>

<div class="form-container">
    <div class="form-card">
        <?php if (isset($errors['general'])): ?>
            <div class="error-message" style="margin-bottom: 20px;">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <div class="security-note">
            <strong> Vérification de sécurité</strong>
            <p>Pour modifier ce forum, tu dois confirmer que tu es bien le créateur en entrant ton nom exact : <strong><?= htmlspecialchars($forum['created_by']) ?></strong></p>
        </div>

        <form method="post">
            <!-- Nom du créateur pour vérification -->
            <div class="form-group">
                <label for="creator_name">
                    Ton nom (pour vérification) <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="creator_name"
                    name="creator_name"
                    placeholder="Entre ton nom exact pour confirmer"
                    required
                >
                <?php if (isset($errors['creator_name'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['creator_name']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Titre -->
            <div class="form-group">
                <label for="title">
                    Titre du forum <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?= htmlspecialchars($title ?: $forum['title']) ?>"
                    maxlength="80"
                    required
                >
                <?php if (isset($errors['title'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['title']) ?></span>
                <?php endif; ?>
                <div class="hint">Entre 3 et 80 caractères</div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Description (optionnel)</label>
                <textarea
                    id="description"
                    name="description"
                    maxlength="500"
                ><?= htmlspecialchars($description ?: ($forum['description'] ?? '')) ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['description']) ?></span>
                <?php endif; ?>
                <div class="hint">Maximum 500 caractères</div>
            </div>

            <div class="form-actions">
                <a href="<?= $BASE ?>/index.php?action=forums" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/chatbot.php'; ?>

</body>
</html>







