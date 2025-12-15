<?php
$defaultPage = 'gestionar_usuarios';
$user = current_user();
if ($user && $user['rol'] === 'colaborador') {
    $defaultPage = 'registrar_asistencia';
}

$page = $_GET['page'] ?? $defaultPage;

$routes = [
    'gestionar_usuarios' => 'usuarios.php',
    'cambiar_pw' => 'usuarios.php',

    'gestionar_colaboradores' => 'colaboradores.php',
    'ver_colaborador' => 'colaboradores.php',
    
    'ver_historial_cargos' => 'colaboradores.php',
    'gestionar_cargos' => 'cargos.php',
    'ver_cargo' => 'cargos.php',
    'asignar_cargo' => 'cargos.php',
    
    'registrar_asistencia' => 'asistencias.php',
    'ver_asistencias_personal' => 'asistencias.php',
    'gestionar_asistencias' => 'asistencias.php',
    'editar_asistencia' => 'asistencias.php',
    'eliminar_asistencia' => 'asistencias.php',

    'gestionar_vacaciones' => 'vacaciones.php',
    'generar_resuelto' => 'vacaciones.php',
    'reporte_colaborador_sueldo' => 'reportes.php',
    'estadisticas' => 'estadisticas.php',
];

if (array_key_exists($page, $routes)) {
    require BASE_PATH . '/controllers/' . $routes[$page];
} else {
    http_response_code(404);
    echo 'Página no encontrada';
}

