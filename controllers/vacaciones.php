<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Vacacion.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/models/Resuelto.php';
require_once BASE_PATH . '/services/AuditService.php';
require_once BASE_PATH . '/helpers/flash.php';
require_once BASE_PATH . '/helpers/redirect.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_vacaciones';

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
                Flash::success('Vacación creada.');
                AuditService::log($actorId, 'vacacion', $newId, "Creó vacación colab {$colabId}");
            } else {
                Flash::error('No se pudo crear la vacación.');
            }
        } else {
            Flash::error('Falta colab_id.');
        }
        redirect('gestionar_vacaciones');
    }

    if ($action === 'update_vacacion') {
        $vacId = $_POST['vac_id'] ?? '';
        $trab = $_POST['dias_trabajados'] ?? '0';
        $validos = $_POST['dias_validos'] ?? '0';
        $estado = $_POST['estado_vacaciones'] ?? 'No válido';
        $tomados = $_POST['dias_tomados'] ?? '0';
        if ($vacId) {
            if (Vacacion::updateVacacion($vacId, $trab, $validos, $estado, $tomados)) {
                Flash::success('Vacación actualizada.');
                AuditService::log($actorId, 'vacacion', $vacId, 'Actualizó vacación');
            } else {
                Flash::error('No se pudo actualizar la vacación.');
            }
        } else {
            Flash::error('Falta vac_id.');
        }
        redirect('gestionar_vacaciones');
    }

    if ($action === 'delete_vacacion') {
        $vacId = $_POST['vac_id'] ?? '';
        if ($vacId) {
            if (Vacacion::deleteVacacion($vacId)) {
                Flash::success('Vacación eliminada.');
                AuditService::log($actorId, 'vacacion', $vacId, 'Eliminó vacación');
            } else {
                Flash::error('No se pudo eliminar la vacación.');
            }
        } else {
            Flash::error('Falta vac_id.');
        }
        redirect('gestionar_vacaciones');
    }

    if ($action === 'create_resuelto') {
        $colabId = $_POST['colab_id'] ?? '';
        $dias = $_POST['dias_vacaciones'] ?? '';
        $inicio = $_POST['periodo_inicio'] ?? '';
        $fin = $_POST['periodo_fin'] ?? '';
        if ($colabId && $dias && $inicio && $fin) {
            $newId = Resuelto::create($colabId, $dias, $inicio, $fin, '');
            if ($newId) {
                Flash::success('Resuelto creado.');
                AuditService::log($actorId, 'resuelto', $newId, "Creó resuelto colab {$colabId}");
            } else {
                Flash::error('No se pudo crear el resuelto.');
            }
        } else {
            Flash::error('Faltan datos para el resuelto.');
        }
        redirect('gestionar_vacaciones');
    }

    if ($action === 'delete_resuelto') {
        $resId = $_POST['resuelto_id'] ?? '';
        if ($resId) {
            if (Resuelto::deleteResuelto($resId)) {
                Flash::success('Resuelto eliminado.');
                AuditService::log($actorId, 'resuelto', $resId, 'Eliminó resuelto');
            } else {
                Flash::error('No se pudo eliminar el resuelto.');
            }
        } else {
            Flash::error('Falta resuelto_id.');
        }
        redirect('gestionar_vacaciones');
    }
}

$flash = Flash::get();

if ($page === 'generar_resuelto') {
    $colaboradorId = $_GET['id'] ?? null;
    $colaborador = $colaboradorId ? Colaborador::find($colaboradorId) : null;
    render('vacaciones/resuelto.php', [
        'colaborador' => $colaborador,
        'flash' => $flash,
    ]);
    return;
}

$vacaciones = Vacacion::resumen();
render('vacaciones/index.php', [
    'vacaciones' => $vacaciones,
    'flash' => $flash,
]);

