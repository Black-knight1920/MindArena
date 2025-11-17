<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/mindarena_forum/assets/dashboard.css">
</head>
<body>

<div class="dashboard">

  <aside class="sidebar">
    <h2>⚙️ Admin</h2>
    <ul>
      <li><a href="/mindarena_forum/admin/dashboard" class="active">🏠 Dashboard</a></li>
      <li><a href="/mindarena_forum/admin/forums">📁 Forums</a></li>
      <li><a href="/mindarena_forum/admin/publications">💬 Publications</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <header class="header"><h1>Dashboard</h1></header>

    <section class="cards">
      <div class="card">
        <h3>Total forums</h3>
        <p><?= $totalForums ?></p>
      </div>

      <div class="card">
        <h3>Total publications</h3>
        <p><?= $totalPublications ?></p>
      </div>
    </section>
  </main>

</div>

</body>
</html>
