<?php
// views/front/forumDeleteConfirm.php
// Page de confirmation de suppression d'un forum

$forum = $forum ?? null;
$errors = $errors ?? [];

if (!$forum) {
    header('Location: index.php?action=forums');
    exit;
}

// Base URL calculée automatiquement
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($BASE === '') $BASE = '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmer la suppression - MindArena</title>
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
            --ma-danger: #ff4b5c;
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
            background: linear-gradient(90deg, transparent, var(--ma-danger), transparent);
            border-radius: 2px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #ff4b5c, #ff6b7a);
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
            max-width: 600px;
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

        .warning-box {
            background: rgba(255,75,92,0.15);
            border: 2px solid rgba(255,75,92,0.4);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            text-align: center;
        }

        .warning-box i {
            font-size: 3rem;
            color: #ff4b5c;
            margin-bottom: 12px;
            display: block;
        }

        .warning-box h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ff6b7a;
            margin-bottom: 12px;
        }

        .warning-box p {
            color: #ffb8c5;
            font-size: 1rem;
            margin: 8px 0;
        }

        .forum-info {
            background: rgba(255,77,240,0.1);
            border: 1px solid rgba(255,77,240,0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .forum-info p {
            margin: 4px 0;
            font-size: 0.95rem;
            color: #ff9cff;
        }

        .forum-info strong {
            color: #ffb8ff;
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

        .form-group input {
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

        .form-group input:focus {
            border-color: #ff4df0;
            box-shadow: 0 0 20px rgba(255,77,240,0.4);
            background: rgba(8,22,36,1);
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

        .btn-danger {
            background: linear-gradient(135deg, #ff4b5c, #ff6b7a);
            color: #fff;
            box-shadow: 0 0 20px rgba(255,75,92,0.6);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(255,75,92,0.9);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<div class="page-header">
    <h1>⚠️ Supprimer le forum</h1>
    <p>Cette action est irréversible</p>
</div>

<div class="form-container">
    <div class="form-card">
        <div class="warning-box">
            <i class="fa fa-exclamation-triangle"></i>
            <h2>Attention !</h2>
            <p>Vous êtes sur le point de supprimer ce forum ainsi que <strong>toutes ses publications</strong>.</p>
            <p>Cette action est <strong>définitive</strong> et ne peut pas être annulée.</p>
        </div>

        <div class="forum-info">
            <p><strong>Forum :</strong> <?= htmlspecialchars($forum['title']) ?></p>
            <?php if (!empty($forum['description'])): ?>
                <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($forum['description'])) ?></p>
            <?php endif; ?>
            <p><strong>Créé par :</strong> <?= htmlspecialchars($forum['created_by'] ?? 'Inconnu') ?></p>
        </div>

        <?php if (isset($errors['creator_name'])): ?>
            <div class="error-message" style="margin-bottom: 20px;">
                <?= htmlspecialchars($errors['creator_name']) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?action=delete-forum&id=<?= (int)$forum['id'] ?>">
            <div class="form-group">
                <label for="creator_name">
                    Entrez votre nom pour confirmer <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="creator_name"
                    name="creator_name"
                    placeholder="Nom exact du créateur"
                    required
                    autofocus
                >
                <div style="font-size: 0.85rem; color: #c7c7ff; opacity: 0.7; margin-top: 6px;">
                    Vous devez entrer exactement : <strong><?= htmlspecialchars($forum['created_by'] ?? 'Inconnu') ?></strong>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?= $BASE ?>/index.php?action=forums" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-danger">
                    <i class="fa fa-trash"></i> Supprimer définitivement
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/chatbot.php'; ?>

</body>
</html>







