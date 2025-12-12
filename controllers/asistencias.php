<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Asistencia.php';
require_once BASE_PATH . '/models/Colaborador.php';

$page = $_GET['page'] ?? 'registrar_asistencia';

if ($page === 'gestionar_asistencias') {
    Authz::requireRoles(['administrador', 'recursos_humanos']);
    $historial = Asistencia::todas();
    render('asistencias/gestionar.php', [
        'historial' => $historial,
    ]);
    return;
}

if ($page === 'ver_asistencias_personal') {
    Authz::requireRoles(['colaborador', 'administrador', 'recursos_humanos']);
    $user = current_user();
    $colaboradorId = $user['colab_id'] ?? null;
    $historial = $colaboradorId ? Asistencia::porColaborador($colaboradorId) : [];
    render('asistencias/personal.php', [
        'historial' => $historial,
    ]);
    return;
}

Authz::requireRoles(['colaborador', 'administrador', 'recursos_humanos']);
render('asistencias/registrar.php', []);

