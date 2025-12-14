<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Vacacion.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/models/Resuelto.php';
require_once BASE_PATH . '/services/AuditService.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_vacaciones';
$messages = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $currentUser = current_user();
    $actorId = $currentUser['user_id'] ?? '';

    if ($action === 'create_vacacion') {
        $colabId = $_POST['colab_id'] ?? '';
        $trab = $_POST['dias_trabajados'] ?? '0';
        $validos = $_POST['dias_validos'] ?? '0';
        $estado = $_POST['estado_vacaciones'] ?? 'No válido';
        if ($colabId) {
            $newId = Vacacion::create($colabId, $trab, $validos, $estado);
            if ($newId) {
                $messages[] = 'Vacación creada.';
                AuditService::log($actorId, 'vacacion', $newId, "Creó vacación colab {$colabId}");
            } else {
                $errors[] = 'No se pudo crear la vacación.';
            }
        } else {
            $errors[] = 'Falta colab_id.';
        }
    }

    if ($action === 'update_vacacion') {
        $vacId = $_POST['vac_id'] ?? '';
        $trab = $_POST['dias_trabajados'] ?? '0';
        $validos = $_POST['dias_validos'] ?? '0';
        $estado = $_POST['estado_vacaciones'] ?? 'No válido';
        $tomados = $_POST['dias_tomados'] ?? '0';
        if ($vacId) {
            if (Vacacion::updateVacacion($vacId, $trab, $validos, $estado, $tomados)) {
                $messages[] = 'Vacación actualizada.';
                AuditService::log($actorId, 'vacacion', $vacId, 'Actualizó vacación');
            } else {
                $errors[] = 'No se pudo actualizar la vacación.';
            }
        } else {
            $errors[] = 'Falta vac_id.';
        }
    }

    if ($action === 'delete_vacacion') {
        $vacId = $_POST['vac_id'] ?? '';
        if ($vacId) {
            if (Vacacion::deleteVacacion($vacId)) {
                $messages[] = 'Vacación eliminada.';
                AuditService::log($actorId, 'vacacion', $vacId, 'Eliminó vacación');
            } else {
                $errors[] = 'No se pudo eliminar la vacación.';
            }
        } else {
            $errors[] = 'Falta vac_id.';
        }
    }

    if ($action === 'create_resuelto') {
        $colabId = $_POST['colab_id'] ?? '';
        $dias = $_POST['dias_vacaciones'] ?? '';
        $inicio = $_POST['periodo_inicio'] ?? '';
        $fin = $_POST['periodo_fin'] ?? '';
        if ($colabId && $dias && $inicio && $fin) {
            $newId = Resuelto::create($colabId, $dias, $inicio, $fin, '');
            if ($newId) {
                $messages[] = 'Resuelto creado.';
                AuditService::log($actorId, 'resuelto', $newId, "Creó resuelto colab {$colabId}");
            } else {
                $errors[] = 'No se pudo crear el resuelto.';
            }
        } else {
            $errors[] = 'Faltan datos para el resuelto.';
        }
    }

    if ($action === 'delete_resuelto') {
        $resId = $_POST['resuelto_id'] ?? '';
        if ($resId) {
            if (Resuelto::deleteResuelto($resId)) {
                $messages[] = 'Resuelto eliminado.';
                AuditService::log($actorId, 'resuelto', $resId, 'Eliminó resuelto');
            } else {
                $errors[] = 'No se pudo eliminar el resuelto.';
            }
        } else {
            $errors[] = 'Falta resuelto_id.';
        }
    }
}

if ($page === 'generar_resuelto') {
    $colaboradorId = $_GET['id'] ?? null;
    $colaborador = $colaboradorId ? Colaborador::find($colaboradorId) : null;
    render('vacaciones/resuelto.php', [
        'colaborador' => $colaborador,
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

$vacaciones = Vacacion::resumen();
render('vacaciones/index.php', [
    'vacaciones' => $vacaciones,
    'messages' => $messages,
    'errors' => $errors,
]);

