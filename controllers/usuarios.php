<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/User.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_usuarios';

if ($page === 'cambiar_pw') {
    $users = User::all();
    render('gestionar_usuarios/cambiar_pw.php', [
        'users' => $users,
    ]);
    return;
}

$users = User::all();
render('gestionar_usuarios/index.php', [
    'users' => $users,
]);

