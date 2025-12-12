<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Vacacion.php';
require_once BASE_PATH . '/models/Colaborador.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_vacaciones';

if ($page === 'generar_resuelto') {
    $colaboradorId = $_GET['id'] ?? null;
    $colaborador = $colaboradorId ? Colaborador::find($colaboradorId) : null;
    render('vacaciones/resuelto.php', [
        'colaborador' => $colaborador,
    ]);
    return;
}

$vacaciones = Vacacion::resumen();
render('vacaciones/index.php', [
    'vacaciones' => $vacaciones,
]);

