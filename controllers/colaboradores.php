<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/models/Cargo.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_colaboradores';

if ($page === 'ver_colaborador') {
    $colaboradorId = $_GET['id'] ?? null;
    $colaborador = $colaboradorId ? Colaborador::find($colaboradorId) : null;
    $cargoActual = $colaborador ? Cargo::findActivoByColaborador($colaboradorId) : null;
    $historialCargos = $colaborador ? Cargo::historialPorColaborador($colaboradorId) : [];

    render('gestionar_colaboradores/ver.php', [
        'colaborador' => $colaborador,
        'cargoActual' => $cargoActual,
        'historialCargos' => $historialCargos,
    ]);
    return;
}

if ($page === 'ver_historial_cargos') {
    $colaboradorId = $_GET['id'] ?? null;
    $colaborador = $colaboradorId ? Colaborador::find($colaboradorId) : null;
    $historialCargos = $colaborador ? Cargo::historialPorColaborador($colaboradorId) : [];

    render('gestionar_colaboradores/historial_cargos.php', [
        'colaborador' => $colaborador,
        'historialCargos' => $historialCargos,
    ]);
    return;
}

$colaboradores = Colaborador::all();
render('gestionar_colaboradores/index.php', [
    'colaboradores' => $colaboradores,
]);

