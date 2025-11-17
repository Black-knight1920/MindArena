<?php
// Security check
if (!isset($forum) || !$forum) {
    die("<h2 style='color:white;text-align:center;margin-top:50px'>❌ Forum introuvable</h2>");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($forum['title']) ?> - Publications</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Roboto", sans-serif;
            background: linear-gradient(45deg, #501755, #2d1854);
            color: white;
        }

        /* HEADER TITLE */
        .section-title {
            text-align: center;
            padding: 60px 20px 30px;
        }

        .section-title h2 {
            font-size: 46px;
            text-transform: uppercase;
            font-weight: 700;
            color: #ff4df0;
            letter-spacing: 1px;
        }

        .section-title p {
            font-size: 18px;
            opacity: 0.85;
        }

        /* BUTTONS */
        .btn-back,
        .btn-create {
            display: inline-block;
            margin: 10px;
            padding: 14px 26px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            transition: 0.25s;
            text-decoration: none;
        }

        .btn-back {
            background: rgba(255,255,255,0.09);
            color: #ff95f5;
            border: 1px solid #ff95f5;
        }

        .btn-back:hover {
            background: #ff95f5;
            color: black;
        }

        .btn-create {
            background: linear-gradient(90deg, #b01ba5, #ff26d8);
            color: white;
            box-shadow: 0 0 12px rgba(255, 20, 180, 0.5);
        }

        .btn-create:hover {
            opacity: 0.85;
        }

        /* PUBLICATION LIST */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding-bottom: 100px;
        }

        .pub-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 25px;
            margin: 20px 0;
            border-radius: 12px;
            backdrop-filter: blur(3px);
            box-shadow: 0 0 12px rgba(0,0,0,0.35);
        }

        .pub-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .pub-author {
            font-weight: bold;
            font-size: 17px;
            color: #ff88f7;
        }

        .pub-date {
            font-size: 13px;
            opacity: 0.7;
        }

        .pub-content {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.92;
            margin-bottom: 10px;
        }

        .btn-delete {
            color: #ff3b7d;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
        }

        .btn-delete:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<section class="section-title">
    <h2><?= htmlspecialchars($forum['title']) ?></h2>

    <?php if (!empty($forum['description'])): ?>
        <p><?= htmlspecialchars($forum['description']) ?></p>
    <?php endif; ?>

    <a href="/mindarena_forum/front/forums" class="btn-back">← Retour aux Forums</a>

  <a href="/mindarena_forum/front/add-publication?forum_id=<?= $forum['id'] ?>"
   class="btn-create">
   + Créer une nouvelle publication
</a>


</section>

<div class="container">
    <?php if (empty($publications)): ?>
        <p style="text-align:center; font-size:20px; opacity:0.7;">
            Aucune publication pour le moment.
        </p>
    <?php else: ?>

        <?php foreach ($publications as $p): ?>
            <div class="pub-card">

                <div class="pub-header">
                    <span class="pub-author"><?= htmlspecialchars($p['author']) ?></span>
                    <span class="pub-date">
                        <?= date("d/m/Y H:i", strtotime($p['created_at'])) ?>
                    </span>
                </div>

                <div class="pub-content">
                    <?= nl2br(htmlspecialchars($p['content'])) ?>
                </div>

                <a href="/mindarena_forum/front/delete-publication?id=<?= $p['id'] ?>"
                   class="btn-delete"
                   onclick="return confirm('Supprimer cette publication ?')">
                    Supprimer
                </a>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

</body>
</html>
