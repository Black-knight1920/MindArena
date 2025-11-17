<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Forum</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Roboto", sans-serif;
            background: linear-gradient(45deg, #501755, #2d1854);
            color: white;
        }

        .section-title {
            text-align: center;
            padding: 60px 20px 20px;
        }

        .section-title h2 {
            font-size: 42px;
            text-transform: uppercase;
            font-weight: 700;
            color: #ff4df0;
        }

        .section-title p {
            font-size: 16px;
            opacity: 0.8;
        }

        .form-container {
            max-width: 650px;
            margin: 40px auto;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            padding: 35px;
            border-radius: 12px;
            backdrop-filter: blur(4px);
            box-shadow: 0 0 12px rgba(0,0,0,0.35);
        }

        label {
            display: block;
            margin-top: 18px;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
            color: #ffbaf9;
            text-transform: uppercase;
        }

        input[type="text"],
        textarea {
            width: 100%;
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-size: 16px;
            color: white;
            margin-bottom: 10px;
            transition: 0.25s;
        }

        input:focus, textarea:focus {
            background: rgba(255,255,255,0.25);
        }

        textarea {
            height: 150px;
            resize: none;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(90deg, #b01ba5, #ff2bdd);
            border: none;
            padding: 15px;
            font-size: 17px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            border-radius: 6px;
            margin-top: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            opacity: 0.85;
        }

        .btn-back {
            display: inline-block;
            margin-top: 25px;
            color: #ff95f5;
            text-transform: uppercase;
            font-weight: bold;
            text-decoration: none;
            transition: 0.25s;
        }

        .btn-back:hover {
            color: #ffdafc;
        }
    </style>

</head>

<body>

<section class="section-title">
    <h2>Créer un nouveau forum</h2>
    <p>Ajoutez une nouvelle catégorie de discussion.</p>
</section>

<div class="form-container">

    <form action="/mindarena_forum/front/add-forum" method="POST">

        <label>Titre du forum</label>
        <input type="text" name="title" placeholder="Ex: Programmation, Gaming..." required>

        <label>Description</label>
        <textarea name="description" placeholder="Décrivez le sujet du forum..." required></textarea>

        <button type="submit" class="btn-submit">Créer le forum</button>

    </form>

    <a href="/mindarena_forum/front/forums" class="btn-back">← Retour aux forums</a>

</div>

</body>
</html>
