<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Créer un forum</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:"Roboto",sans-serif;}
body{background:#130020;color:white;padding:40px;}

h1{color:#d43cff;font-size:40px;margin-bottom:15px;}

.form-box{
    background:#1d0b33;padding:30px;border-radius:8px;
    border:1px solid rgba(255,255,255,.15);
    max-width:600px;margin:auto;
}
label{display:block;margin-bottom:8px;font-weight:bold;}
input,textarea{
    width:100%;padding:12px;background:#2b1547;color:white;
    border:none;border-radius:6px;margin-bottom:20px;
}
textarea{height:160px;}

.btn-submit{
    width:100%;background:#d43cff;color:white;
    padding:12px;border:none;border-radius:6px;
    font-size:15px;font-weight:bold;cursor:pointer;
}
.btn-submit:hover{background:#ff55ff;}
</style>

</head>
<body>

<h1>Créer un forum</h1>

<div class="form-box">
<form action="/mindarena_forum/front/add-forum" method="POST">

    <label>Titre</label>
    <input type="text" name="title" required>

    <label>Description</label>
    <textarea name="description" required></textarea>

    <button class="btn-submit">Créer</button>
</form>
</div>

</body>
</html>
