<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Cargo.php';
require_once BASE_PATH . '/models/Colaborador.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_cargos';

if ($page === 'ver_cargo') {
    $cargoId = $_GET['id'] ?? null;
    $cargo = $cargoId ? Cargo::find($cargoId) : null;
    $colaboradores = $cargoId ? Colaborador::porCargo($cargoId) : [];

    render('gestionar_cargos/ver.php', [
        'cargo' => $cargo,
        'colaboradores' => $colaboradores,
    ]);
    return;
}

if ($page === 'asignar_cargo') {
    $cargos = Cargo::all();
    $colaboradores = Colaborador::all();
    render('asignar_cargo/index.php', [
        'cargos' => $cargos,
        'colaboradores' => $colaboradores,
    ]);
    return;
}

$cargos = Cargo::all();
render('gestionar_cargos/index.php', [
    'cargos' => $cargos,
]);

