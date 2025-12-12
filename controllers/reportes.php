<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Colaborador.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$filtros = [
    'sexo' => $_GET['sexo'] ?? '',
    'edad_min' => $_GET['edad_min'] ?? '',
    'edad_max' => $_GET['edad_max'] ?? '',
    'nombre' => $_GET['nombre'] ?? '',
    'apellido' => $_GET['apellido'] ?? '',
    'salario_min' => $_GET['salario_min'] ?? '',
];

$colaboradores = Colaborador::filtrarParaReporte($filtros);

render('reportes/colaborador_sueldo.php', [
    'colaboradores' => $colaboradores,
    'filtros' => $filtros,
]);

