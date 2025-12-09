<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$flashes = $_SESSION['_flash'] ?? [];
if ($flashes): ?>
    <div class="flash-stack">
        <?php foreach ($flashes as $flash): ?>
            <div class="flash <?= htmlspecialchars($flash['type'] ?? 'info') ?>">
                <?= htmlspecialchars($flash['message'] ?? '') ?>
            </div>
        <?php endforeach; ?>
    </div>
    <style>
        .flash-stack {
            position: fixed;
            top: 88px;
            right: 18px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .flash {
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(9,15,30,0.9);
            border: 1px solid rgba(255,255,255,0.12);
            color: #e5e7eb;
            min-width: 220px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            font-weight: 700;
        }
        .flash.success { border-color: rgba(52,211,153,0.5); color: #34d399; }
        .flash.error { border-color: rgba(248,113,113,0.6); color: #f87171; }
        .flash.info { border-color: rgba(96,165,250,0.5); color: #93c5fd; }
    </style>
<?php
endif;
unset($_SESSION['_flash']);
?>

