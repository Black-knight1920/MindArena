<?php $base = defined('BASE_URL') ? BASE_URL : ''; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MindArena</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/app.css">
</head>

<body>
<header class="ma-header">
    <div class="ma-header-inner">
        <div class="logo-container">
            <div class="logo-icon">🧠</div>
            <div class="logo-text">
                <div class="logo-main">MindArena</div>
                <div class="logo-sub">Community</div>
            </div>
        </div>

        <nav class="ma-nav">
            <a href="<?= $base ?>/front/forums">Forums</a>
            <a href="<?= $base ?>/front/add-forum">Créer un forum</a>
        </nav>
    </div>
</header>

<main class="ma-main">

