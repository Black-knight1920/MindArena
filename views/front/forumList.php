<?php
// $forums is already provided by your controller
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Forums - MindArena</title>

    <style>
        /* GLOBAL PAGE STYLE */
        body {
            margin: 0;
            padding: 0;
            font-family: "Roboto", sans-serif;
            color: white;
            background: linear-gradient(135deg, #2d1854, #501755);
        }

        h1, h2, h3, p {
            margin: 0;
        }

        /* TOP TITLE SECTION */
        .page-title {
            text-align: center;
            padding: 60px 20px;
        }

        .page-title h2 {
            font-size: 42px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #e647ff;
            margin-bottom: 10px;
        }

        .page-title p {
            font-size: 17px;
            opacity: .85;
        }

        /* GRID */
        .forum-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 20px 60px 100px;
            max-width: 1400px;
            margin: auto;
        }

        /* FORUM CARD */
        .forum-card {
            background: rgba(255,255,255,0.08);
            padding: 25px;
            border-radius: 15px;
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,0.15);
            transition: .25s;
            box-shadow: 0 0 18px rgba(0,0,0,0.45);
        }

        .forum-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,0.12);
            box-shadow: 0 0 25px rgba(0,0,0,0.55);
        }

        .forum-card h3 {
            font-size: 24px;
            color: #ff44ff;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .forum-card p {
            color: #ddd;
            font-size: 15px;
            margin-bottom: 18px;
        }

        /* BUTTONS */
        .btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            text-align: center;
            transition: .25s;
        }

        .btn-view {
            background: linear-gradient(90deg, #a01bd1, #ff00d4);
            color: white;
        }

        .btn-view:hover {
            background: linear-gradient(90deg, #d926d0, #ff2be6);
        }

        .btn-delete {
            margin-top: 8px;
            background: #d22;
            color: white;
        }

        .btn-delete:hover {
            background: #ff4444;
        }

        /* FLOATING ADD BUTTON */
        .add-forum-btn {
            position: fixed;
            right: 30px;
            bottom: 30px;
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: linear-gradient(90deg, #a01bd1, #ff00d4);
            box-shadow: 0 0 18px #e647ff;
            color: white;
            font-size: 38px;
            text-align: center;
            line-height: 65px;
            font-weight: bold;
            cursor: pointer;
            transition: .25s;
            text-decoration: none;
        }

        .add-forum-btn:hover {
            transform: scale(1.12);
            box-shadow: 0 0 25px #ff2be6;
        }
    </style>
</head>

<body>

    <!-- PAGE TITLE -->
    <section class="page-title">
        <h2>Forums</h2>
        <p>Explorez les catégories créées par la communauté.</p>
    </section>

    <!-- FORUM GRID -->
    <div class="forum-grid">
        <?php foreach ($forums as $f): ?>
            <div class="forum-card">

                <h3><?= htmlspecialchars($f['title']) ?></h3>

                <p><?= nl2br(htmlspecialchars($f['description'])) ?></p>

                <a class="btn btn-view"
                   href="/mindarena_forum/front/publications?forum_id=<?= $f['id'] ?>">
                    Voir les publications
                </a>

                <br>

                <a class="btn btn-delete"
                   href="/mindarena_forum/front/delete-forum?id=<?= $f['id'] ?>"
                   onclick="return confirm('Supprimer ce forum ?');">
                    Supprimer
                </a>

            </div>
        <?php endforeach; ?>
    </div>

    <!-- ADD FORUM FLOATING BUTTON -->
    <a href="/mindarena_forum/front/add-forum" class="add-forum-btn">+</a>

</body>
</html>
