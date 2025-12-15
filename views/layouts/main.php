<?php require_once BASE_PATH . '/views/partials/nav.php'; ?> <!-- layout principal con sidebar/nav -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Capital Humano</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
</head>
<body>
    <header class="topbar">
        <div class="brand">Capital Humano</div>
        <div class="user-info">
            <?php if (current_user()): ?>
                <span><?= htmlspecialchars(current_user()['username']) ?> (<?= htmlspecialchars(current_user()['rol']) ?>)</span>
                <a class="btn-link" href="<?= BASE_URL ?>/logout.php">Salir</a>
            <?php endif; ?>
        </div>
    </header>
    <div class="layout">
        <aside class="sidebar">
            <?= $nav ?? '' ?>
        </aside>
        <main class="content">
            <?= $content ?? '' ?>
        </main>
    </div>
</body>
</html>

