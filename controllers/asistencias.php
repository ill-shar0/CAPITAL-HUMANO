<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Asistencia.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/services/AuditService.php';

$page = $_GET['page'] ?? 'registrar_asistencia';
$messages = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $currentUser = current_user();
    $actorId = $currentUser['user_id'] ?? '';

    if ($action === 'create_asistencia') {
        $colabId = $_POST['colab_id'] ?? ($currentUser['colab_id'] ?? '');
        $fecha = $_POST['fecha'] ?? date('Y-m-d');
        $horaEntrada = $_POST['hora_entrada'] ?? date('H:i:s');
        $horaSalida = $_POST['hora_salida'] ?? null;
        if ($colabId) {
            $newId = Asistencia::create($colabId, $fecha, $horaEntrada, $horaSalida);
            if ($newId) {
                $messages[] = 'Asistencia registrada.';
                AuditService::log($actorId, 'asistencia', $newId, "Registro asistencia colab {$colabId}");
            } else {
                $errors[] = 'No se pudo registrar la asistencia.';
            }
        } else {
            $errors[] = 'Falta colaborador.';
        }
    }

    if ($action === 'update_asistencia') {
        $asisId = $_POST['asis_id'] ?? '';
        $fecha = $_POST['fecha'] ?? '';
        $horaEntrada = $_POST['hora_entrada'] ?? '';
        $horaSalida = $_POST['hora_salida'] ?? null;
        if ($asisId && $fecha && $horaEntrada) {
            if (Asistencia::updateAsistencia($asisId, $fecha, $horaEntrada, $horaSalida)) {
                $messages[] = 'Asistencia actualizada.';
                AuditService::log($actorId, 'asistencia', $asisId, 'Actualizó asistencia');
            } else {
                $errors[] = 'No se pudo actualizar la asistencia.';
            }
        } else {
            $errors[] = 'Faltan datos para actualizar.';
        }
    }

    if ($action === 'delete_asistencia') {
        $asisId = $_POST['asis_id'] ?? '';
        if ($asisId) {
            if (Asistencia::deleteAsistencia($asisId)) {
                $messages[] = 'Asistencia eliminada.';
                AuditService::log($actorId, 'asistencia', $asisId, 'Eliminó asistencia');
            } else {
                $errors[] = 'No se pudo eliminar.';
            }
        } else {
            $errors[] = 'Falta asis_id.';
        }
    }
}

if ($page === 'gestionar_asistencias') {
    Authz::requireRoles(['administrador', 'recursos_humanos']);
    $buscarColab = $_GET['colab_id'] ?? '';
    $historial = $buscarColab ? Asistencia::searchByColaborador($buscarColab) : Asistencia::todas();
    render('asistencias/gestionar.php', [
        'historial' => $historial,
        'messages' => $messages,
        'errors' => $errors,
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
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

Authz::requireRoles(['colaborador', 'administrador', 'recursos_humanos']);
render('asistencias/registrar.php', [
    'messages' => $messages,
    'errors' => $errors,
]);

