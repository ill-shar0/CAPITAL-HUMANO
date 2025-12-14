<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Cargo.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/services/AuditService.php';
require_once BASE_PATH . '/helpers/flash.php';
require_once BASE_PATH . '/helpers/redirect.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_cargos';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $currentUser = current_user();
    $actorId = $currentUser['user_id'] ?? '';

    if ($action === 'create_cargo') {
        $nombre = trim($_POST['nombre_cargo'] ?? '');
        $departamento = trim($_POST['departamento_cargo'] ?? '');
        $sueldo = trim($_POST['sueldo_cargo'] ?? '');
        $ocupacion = trim($_POST['ocupacion'] ?? '');
        if ($nombre === '') {
            Flash::error('El nombre del cargo es obligatorio.');
        } else {
            $newId = Cargo::create($nombre, $departamento, $sueldo, $ocupacion);
            if ($newId) {
                Flash::success('Cargo creado correctamente.');
                AuditService::log($actorId, 'cargo', $newId, "Creó cargo {$nombre}");
            } else {
                Flash::error('No se pudo crear el cargo.');
            }
        }
        redirect('gestionar_cargos');
    }

    if ($action === 'update_cargo') {
        $id = $_POST['cargo_id'] ?? '';
        $nombre = trim($_POST['nombre_cargo'] ?? '');
        $departamento = trim($_POST['departamento_cargo'] ?? '');
        $sueldo = trim($_POST['sueldo_cargo'] ?? '');
        $ocupacion = trim($_POST['ocupacion'] ?? '');
        if ($id && $nombre !== '') {
            if (Cargo::updateCargo($id, $nombre, $departamento, $sueldo, $ocupacion)) {
                Flash::success('Cargo actualizado.');
                AuditService::log($actorId, 'cargo', $id, "Actualizó cargo {$nombre}");
            } else {
                Flash::error('No se pudo actualizar el cargo.');
            }
        } else {
            Flash::error('Faltan datos para actualizar el cargo.');
        }
        redirect('gestionar_cargos');
    }

    if ($action === 'delete_cargo') {
        $id = $_POST['cargo_id'] ?? '';
        if ($id) {
            if (Cargo::deleteCargo($id)) {
                Flash::success('Cargo eliminado y asignaciones desactivadas.');
                AuditService::log($actorId, 'cargo', $id, 'Eliminó cargo');
            } else {
                Flash::error('No se pudo eliminar el cargo.');
            }
        } else {
            Flash::error('Falta cargo_id.');
        }
        redirect('gestionar_cargos');
    }

    if ($action === 'remove_assignment') {
        $cargoId = $_POST['cargo_id'] ?? '';
        $colabId = $_POST['colab_id'] ?? '';
        if ($cargoId && $colabId) {
            if (Cargo::removeAssignment($colabId, $cargoId)) {
                Flash::success('Asignación desactivada.');
                AuditService::log($actorId, 'cargo', $cargoId, "Quitó cargo a colab {$colabId}");
            } else {
                Flash::error('No se pudo quitar el cargo del colaborador.');
            }
        } else {
            Flash::error('Faltan datos de cargo o colaborador.');
        }
        redirect('gestionar_cargos');
    }

    if ($action === 'assign_cargo') {
        $cargoId = $_POST['cargo_id'] ?? '';
        $colabId = $_POST['colab_id'] ?? '';
        $periodo = $_POST['periodo'] ?? 'Permanente';
        if ($cargoId && $colabId) {
            if (Cargo::assignToColaborador($colabId, $cargoId, $periodo)) {
                Flash::success('Cargo asignado.');
                AuditService::log($actorId, 'cargo', $cargoId, "Asignó cargo a colab {$colabId}");
            } else {
                Flash::error('No se pudo asignar el cargo.');
            }
        } else {
            Flash::error('Seleccione cargo y colaborador.');
        }
        redirect('gestionar_cargos');
    }
}

$flash = Flash::get();

if ($page === 'ver_cargo') {
    $cargoId = $_GET['id'] ?? null;
    $cargo = $cargoId ? Cargo::find($cargoId) : null;
    $colaboradores = $cargoId ? Colaborador::porCargo($cargoId) : [];

    render('gestionar_cargos/ver.php', [
        'cargo' => $cargo,
        'colaboradores' => $colaboradores,
        'flash' => $flash,
    ]);
    return;
}

if ($page === 'asignar_cargo') {
    $cargos = Cargo::all();
    $colaboradores = Colaborador::all();
    render('asignar_cargo/index.php', [
        'cargos' => $cargos,
        'colaboradores' => $colaboradores,
        'flash' => $flash,
    ]);
    return;
}

$cargos = Cargo::all();
render('gestionar_cargos/index.php', [
    'cargos' => $cargos,
    'flash' => $flash,
]);

