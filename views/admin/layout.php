<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?> - Admin Panel</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>

    /* GLOBAL --------------------------- */
    body {
        background: #0d0d1a;
        color: #ffffff;
        margin: 0;
        font-family: "Inter", sans-serif;
    }

    h1, h2, h3, h4, h5 {
        font-weight: 600;
        letter-spacing: .5px;
    }

    /* SIDEBAR -------------------------- */
    .sidebar {
        width: 260px;
        height: 100vh;
        background: #131327;
        position: fixed;
        left: 0;
        top: 0;
        padding: 32px 22px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        box-shadow: 4px 0 25px rgba(0,0,0,0.5);
    }

    .sidebar h2 {
        margin-bottom: 25px;
        color: #fff;
        font-size: 22px;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        font-size: 15px;
        color: #c7c7ff;
        background: #1b1b38;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        transition: .25s;
    }

    .sidebar a:hover {
        background: #5b22f0;
        color: #fff;
        transform: translateX(6px);
        box-shadow: 0 5px 12px rgba(90,40,255,.5);
    }

    .sidebar a.active {
        background: #693bff;
        box-shadow: 0 0 12px rgba(105,60,255,.6);
    }

    /* TOPBAR ---------------------------- */
    .topbar {
        margin-left: 260px;
        height: 70px;
        background: rgba(255,255,255,0.06);
        backdrop-filter: blur(12px);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 30px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .search-bar {
        width: 280px;
        padding: 9px 14px;
        border-radius: 12px;
        border: none;
        background: rgba(255,255,255,0.12);
        color: #fff;
    }

    .avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: url('https://i.pravatar.cc/300') center/cover;
        border: 2px solid #8358ff;
        cursor: pointer;
    }

    /* CONTENT ---------------------------- */
    .content {
        margin-left: 280px;
        margin-top: 30px;
        padding: 35px;
        max-width: 1200px;
        animation: fadeIn .4s ease;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* CARDS ------------------------------ */
    .card-dark {
        background: #1b1b30;
        padding: 25px;
        border-radius: 16px;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 0 18px rgba(0,0,0,0.35);
        transition: .25s;
    }

    .card-dark:hover {
        transform: translateY(-4px);
        box-shadow: 0 0 25px rgba(120,60,255,0.5);
    }

    /* TABLES ----------------------------- */
    .table-glass {
        width: 100%;
        background: rgba(255,255,255,0.04);
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.08);
        backdrop-filter: blur(8px);
    }

    .table-glass thead {
        background: rgba(255,255,255,0.08);
    }

    .table-glass th {
        font-size: 14px;
        padding: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #d4cfff;
    }

    .table-glass td {
        padding: 14px;
        color: #efefff;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .table-glass tr:hover {
        background: rgba(255,255,255,0.12);
        transform: scale(1.01);
        transition: .22s;
    }

    /* BUTTONS ----------------------------- */
    .btn-create {
        background: linear-gradient(135deg,#6e3bff,#a678ff);
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        transition: .25s;
    }

    .btn-create:hover {
        transform: translateY(-3px);
        background: linear-gradient(135deg,#8358ff,#c49aff);
        box-shadow: 0 0 12px rgba(150,120,255,.6);
    }

    .btn-edit {
        background: #305bff;
        padding: 6px 12px;
        border-radius: 8px;
        color: white;
    }

    .btn-delete {
        background: #d53043;
        padding: 6px 12px;
        border-radius: 8px;
        color: white;
    }

    .form-card {
        background: #1c1c38;
        padding: 25px;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.08);
        width: 600px;
    }

    .form-control {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        color: white;
        border-radius: 10px;
    }

    .form-control:focus {
        border-color: #8a63ff;
        box-shadow: 0 0 10px rgba(138,99,255,.4);
    }

    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2><i class="ri-shield-user-fill"></i> Admin Panel</h2>

        <a href="admin.php?action=dashboard" class="<?= ($active=='dashboard')?'active':'' ?>">
            <i class="ri-dashboard-line"></i> Dashboard
        </a>

        <a href="admin.php?action=forums" class="<?= ($active=='forums')?'active':'' ?>">
            <i class="ri-folder-3-line"></i> Forums
        </a>

        <a href="admin.php?action=publications" class="<?= ($active=='publications')?'active':'' ?>">
            <i class="ri-file-text-line"></i> Publications
        </a>
    </div>

    <!-- TOPBAR -->
    <div class="topbar">
        <input type="text" class="search-bar" placeholder="Recherche…">
        <div class="avatar"></div>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <?php include $viewFile; ?>
    </div>

</body>
</html>
