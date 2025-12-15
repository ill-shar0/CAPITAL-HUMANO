<?php
require_once BASE_PATH . '/middleware/auth.php'; // sesión/seguridad
require_once BASE_PATH . '/services/Authz.php'; // roles
require_once BASE_PATH . '/models/Colaborador.php'; // datos colaboradores

Authz::requireRoles(['administrador', 'recursos_humanos']); // solo admin/RRHH

// PAGINACIÓN
$porPagina = 10; // tamaño de página
$paginaActual = max(1, (int)($_GET['p'] ?? 1)); // página actual
$offset = ($paginaActual - 1) * $porPagina; // inicio

// FILTROS
$filtros = [
    'sexo' => $_GET['sexo'] ?? '', // filtro sexo
    'edad_min' => $_GET['edad_min'] ?? '', // edad min
    'edad_max' => $_GET['edad_max'] ?? '', // edad max
    'nombre' => $_GET['nombre'] ?? '', // nombre
    'apellido' => $_GET['apellido'] ?? '', // apellido
    'salario_min' => $_GET['salario_min'] ?? '', // salario min
];

// DATOS PARA TABLA CON PAGINACIÓN
$colaboradores = Colaborador::filtrarParaReporte(
    $filtros,
    $porPagina,
    $offset
);

// Total de registros para calcular páginas
$totalRegistros = Colaborador::contarParaReporte($filtros); // total filtrado
$totalPaginas = (int) ceil($totalRegistros / $porPagina); // páginas totales


// EXPORTAR CSV (EXCEL)
if (isset($_GET['export']) && $_GET['export'] === 'csv') {

    header('Content-Type: text/csv; charset=utf-8'); // tipo csv
    header('Content-Disposition: attachment; filename=reporte_colaborador_sueldo.csv'); // nombre archivo

    $output = fopen('php://output', 'w');

    // Encabezados
    fputcsv($output, ['Nombre', 'Sexo', 'Salario']); // headers

    foreach ($colaboradores as $col) {
        fputcsv($output, [
            $col['primer_nombre'] . ' ' . $col['apellido_paterno'],
            $col['sexo'],
            $col['car_sueldo']
        ]);
    }

    fclose($output); // cierra buffer
    exit; // termina respuesta
}

// RENDER
render('reportes/colaborador_sueldo.php', [
    'colaboradores' => $colaboradores,
    'filtros' => $filtros,
    'paginaActual' => $paginaActual,
    'totalPaginas' => $totalPaginas,
]);
