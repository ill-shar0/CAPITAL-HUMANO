<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Colaborador.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

// PAGINACIÓN
$porPagina = 10;
$paginaActual = max(1, (int)($_GET['p'] ?? 1));
$offset = ($paginaActual - 1) * $porPagina;

// FILTROS
$filtros = [
    'sexo' => $_GET['sexo'] ?? '',
    'edad_min' => $_GET['edad_min'] ?? '',
    'edad_max' => $_GET['edad_max'] ?? '',
    'nombre' => $_GET['nombre'] ?? '',
    'apellido' => $_GET['apellido'] ?? '',
    'salario_min' => $_GET['salario_min'] ?? '',
];

// DATOS PARA TABLA CON PAGINACIÓN
$colaboradores = Colaborador::filtrarParaReporte(
    $filtros,
    $porPagina,
    $offset
);

// Total de registros para calcular páginas
$totalRegistros = Colaborador::contarParaReporte($filtros);
$totalPaginas = (int) ceil($totalRegistros / $porPagina);


// EXPORTAR CSV (EXCEL)
if (isset($_GET['export']) && $_GET['export'] === 'csv') {

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reporte_colaborador_sueldo.csv');

    $output = fopen('php://output', 'w');

    // Encabezados
    fputcsv($output, ['Nombre', 'Sexo', 'Salario']);

    foreach ($colaboradores as $col) {
        fputcsv($output, [
            $col['primer_nombre'] . ' ' . $col['apellido_paterno'],
            $col['sexo'],
            $col['car_sueldo']
        ]);
    }

    fclose($output);
    exit;
}

// RENDER
render('reportes/colaborador_sueldo.php', [
    'colaboradores' => $colaboradores,
    'filtros' => $filtros,
    'paginaActual' => $paginaActual,
    'totalPaginas' => $totalPaginas,
]);
