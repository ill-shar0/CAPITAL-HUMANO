<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Asistencia.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/helpers/flash.php';
require_once BASE_PATH . '/helpers/redirect.php';
require_once BASE_PATH . '/services/AuditService.php';
require_once BASE_PATH . '/services/AuditService.php';

$page = $_GET['page'] ?? 'registrar_asistencia';

// Gestionar asistencias (vista para RRHH y administradores)
if ($page === 'gestionar_asistencias') {
    Authz::requireRoles(['administrador', 'recursos_humanos']);
    // Procesar búsqueda si se proporciona un término
    $busqueda = $_GET['q'] ?? '';
    // Obtener historial de asistencias según la búsqueda
    if ($busqueda !== '') {
        // Buscar por colaborador
        $historial = Asistencia::buscarPorColaborador($busqueda);
    } else {
        // Obtener todas las asistencias
        $historial = Asistencia::todas();
    }
    render('asistencias/gestionar.php', [
        'historial' => $historial,
        'busqueda' => $busqueda,
    ]);
    return;
}

// Editar asistencia existente
if ($page === 'editar_asistencia') {
    Authz::requireRoles(['administrador', 'recursos_humanos']);
    // Obtener ID de la asistencia a editar
    $id = $_GET['id'] ?? null;
    if (!$id) {
        Flash::error('Asistencia no encontrada');
        redirect('gestionar_asistencias');
    }
    // Procesar formulario de edición
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ok = Asistencia::update(
            $id,
            $_POST['fecha'],
            $_POST['hora_entrada'],
            $_POST['hora_salida'] ?: null
        );
        // Mostrar mensaje según el resultado
        if ($ok) {
            Flash::success('Asistencia actualizada');
            $actorId = (current_user()['user_id'] ?? '');
            AuditService::log($actorId, 'asistencia', $id, 'Actualizó asistencia');
        } else {
            Flash::error('Error al actualizar asistencia');
        }
        // Redirigir de vuelta a la gestión de asistencias
        redirect('gestionar_asistencias');
    }
    // Obtener datos de la asistencia para mostrar en el formulario
    $asistencia = Asistencia::find($id);
    // Renderizar vista de edición
    render('asistencias/editar.php', [
        'asistencia' => $asistencia
    ]);
    return;
}

// Eliminar asistencia existente
if ($page === 'eliminar_asistencia') {
    Authz::requireRoles(['administrador', 'recursos_humanos']);
    // Obtener ID de la asistencia a eliminar
    $id = $_GET['id'] ?? null;
    // Intentar eliminar la asistencia y mostrar mensaje según el resultado
    if ($id && Asistencia::delete($id)) {
        Flash::success('Asistencia eliminada');
        $actorId = (current_user()['user_id'] ?? '');
        AuditService::log($actorId, 'asistencia', $id, 'Eliminó asistencia');
    } else {
        Flash::error('No se pudo eliminar la asistencia');
    }
    // Redirigir de vuelta a la gestión de asistencias
    redirect('gestionar_asistencias');
    return;
}

// Ver asistencias del colaborador actual
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

// Procesar formulario de registro de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Authz::requireRoles(['colaborador', 'administrador', 'recursos_humanos']);
    $user = current_user();
    $colaboradorId = $user['colab_id'] ?? null;

    if ($colaboradorId) {
        $accion = $_POST['accion'] ?? '';
        $fechaActual = date('Y-m-d');
        $horaActual = date('H:i:s');
        // Procesar la acción de entrada
        if ($accion === 'entrada') {
            // Registrar la entrada
            $ok = Asistencia::registrarEntrada($colaboradorId, $fechaActual, $horaActual);
            // Mostrar mensaje según el resultado
            if (!$ok) {
                Flash::error('No se pudo registrar la entrada.');
            } else {
                Flash::success('Entrada registrada con correctamente.');
                $actorId = $user['user_id'] ?? '';
                AuditService::log($actorId, 'asistencia', 'entrada', "Entrada colab {$colaboradorId} {$fechaActual}");
            }
        }
        // Procesar la acción de salida
        if ($accion === 'salida') {
            // Registrar la salida
            $ok = Asistencia::registrarSalida($colaboradorId, $fechaActual, $horaActual);
            // Mostrar mensaje según el resultado
            if (!$ok) {
                Flash::error('No se pudo registrar la salida.');
            } else {
                Flash::success('Salida registrada con correctamente.');
                $actorId = $user['user_id'] ?? '';
                AuditService::log($actorId, 'asistencia', 'salida', "Salida colab {$colaboradorId} {$fechaActual}");
            }
        }
    }
}

Authz::requireRoles(['colaborador', 'administrador', 'recursos_humanos']);
render('asistencias/registrar.php', []);

