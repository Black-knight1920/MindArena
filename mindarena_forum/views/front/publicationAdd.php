<?php $forum_id = $_GET['forum_id'] ?? 0; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nouvelle Publication</title>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: "Roboto", sans-serif;
    background: linear-gradient(45deg, #501755, #2d1854);
    color: white;
}

h2 {
    font-size: 42px;
    text-align: center;
    margin-top: 40px;
    color: #ff4df0;
}

.form-card {
    max-width: 700px;
    margin: 40px auto;
    padding: 35px;
    background: rgba(255,255,255,0.08);
    border-radius: 12px;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255,255,255,0.08);
}

label {
    margin-top: 15px;
    font-weight: bold;
    text-transform: uppercase;
    color: #ffcdfc;
}

input[type="text"],
textarea {
    width: 100%;
    margin-top: 6px;
    padding: 12px;
    border-radius: 6px;
    border: none;
    background: rgba(255,255,255,0.2);
    color: white;
    font-size: 16px;
}

textarea {
    height: 150px;
    resize: none;
}

.btn-submit {
    width: 100%;
    margin-top: 20px;
    padding: 14px;
    background: #b01ba5;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    border-radius: 6px;
}
</style>

</head>
<body>

<h2>Nouvelle Publication</h2>

<div class="form-card">

    <form method="POST">

        <input type="hidden" name="forum_id" value="<?= $forum_id ?>">

        <label>Auteur</label>
        <input type="text" name="author" placeholder="Votre nom" required>

        <label>Contenu</label>
        <textarea name="content" placeholder="Votre message..." required></textarea>

        <button class="btn-submit">Publier</button>

    </form>

</div>

</body>
</html>
