<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Cargo.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/services/AuditService.php';
require_once BASE_PATH . '/helpers/sanitize.php';
require_once BASE_PATH . '/helpers/validator.php';

Authz::requireRoles(['administrador', 'recursos_humanos']); // acceso restringido

$page = $_GET['page'] ?? 'gestionar_cargos'; // página solicitada
$messages = []; // feedback éxito
$errors = [];   // feedback error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';       // acción enviada
    $currentUser = current_user();          // usuario en sesión
    $actorId = $currentUser['user_id'] ?? ''; // para auditoría

    if ($action === 'create_cargo') { // crear nuevo cargo
        $nombre = s_str($_POST['nombre_cargo'] ?? '', 150);
        $departamento = s_str($_POST['departamento_cargo'] ?? '', 150);
        $sueldo = s_str($_POST['sueldo_cargo'] ?? '', 50);
        $ocupacion = s_str($_POST['ocupacion'] ?? '', 255);

        $errors = array_merge($errors,
            v_required(['nombre' => $nombre], ['nombre' => 'Nombre del cargo']),
            v_alpha($nombre, 'Nombre del cargo'),
            v_alpha($departamento, 'Departamento', true),
            v_pattern($sueldo, 'Sueldo', '/^[0-9]+(\\.[0-9]{1,2})?$/', 'solo números y hasta 2 decimales')
        );

        if (empty($errors)) {
            $newId = Cargo::create($nombre, $departamento, $sueldo, $ocupacion);
            if ($newId !== null) {
                $messages[] = 'Cargo creado correctamente.';
                AuditService::log($actorId, 'cargo', $newId, "Creó cargo {$nombre}");
            } else {
                $errors[] = 'No se pudo crear el cargo.';
            }
        }
    }

    if ($action === 'update_cargo') { // actualizar cargo
        $id = $_POST['cargo_id'] ?? '';
        $nombre = s_str($_POST['nombre_cargo'] ?? '', 150);
        $departamento = s_str($_POST['departamento_cargo'] ?? '', 150);
        $sueldo = s_str($_POST['sueldo_cargo'] ?? '', 50);
        $ocupacion = s_str($_POST['ocupacion'] ?? '', 255);

        $errors = array_merge($errors,
            v_required(['id' => $id, 'nombre' => $nombre], [
                'id' => 'ID de cargo',
                'nombre' => 'Nombre del cargo'
            ]),
            v_alpha($nombre, 'Nombre del cargo'),
            v_alpha($departamento, 'Departamento', true),
            v_pattern($sueldo, 'Sueldo', '/^[0-9]+(\\.[0-9]{1,2})?$/', 'solo números y hasta 2 decimales')
        );

        if (empty($errors)) {
            if (Cargo::updateCargo($id, $nombre, $departamento, $sueldo, $ocupacion)) {
                $messages[] = 'Cargo actualizado.';
                AuditService::log($actorId, 'cargo', $id, "Actualizó cargo {$nombre}");
            } else {
                $errors[] = 'No se pudo actualizar el cargo.';
            }
        }
    }

    if ($action === 'delete_cargo') { // eliminar cargo
        $id = $_POST['cargo_id'] ?? '';
        if ($id) {
            if (Cargo::deleteCargo($id)) {
                $messages[] = 'Cargo eliminado y asignaciones desactivadas.';
                AuditService::log($actorId, 'cargo', $id, 'Eliminó cargo');
            } else {
                $errors[] = 'No se pudo eliminar el cargo.';
            }
        } else {
            $errors[] = 'Falta cargo_id.';
        }
    }

    if ($action === 'remove_assignment') { // desasignar cargo a colaborador
        $cargoId = $_POST['cargo_id'] ?? '';
        $colabId = $_POST['colab_id'] ?? '';
        if ($cargoId && $colabId) {
            if (Cargo::removeAssignment($colabId, $cargoId)) {
                $messages[] = 'Asignación desactivada.';
                AuditService::log($actorId, 'cargo', $cargoId, "Quitó cargo a colab {$colabId}");
            } else {
                $errors[] = 'No se pudo quitar el cargo del colaborador.';
            }
        } else {
            $errors[] = 'Faltan datos de cargo o colaborador.';
        }
    }

    if ($action === 'assign_cargo') { // asignar cargo a colaborador
        $cargoId = $_POST['cargo_id'] ?? '';
        $colabId = $_POST['colab_id'] ?? '';
        $periodo = $_POST['periodo'] ?? 'Permanente';
        if ($cargoId && $colabId) {
            if (Cargo::assignToColaborador($colabId, $cargoId, $periodo)) {
                $messages[] = 'Cargo asignado.';
                AuditService::log($actorId, 'cargo', $cargoId, "Asignó cargo a colab {$colabId}");
            } else {
                $errors[] = 'No se pudo asignar el cargo.';
            }
        } else {
            $errors[] = 'Seleccione cargo y colaborador.';
        }
    }
}

if ($page === 'ver_cargo') { // detalle cargo
    $cargoId = $_GET['id'] ?? null;
    $cargo = $cargoId ? Cargo::find($cargoId) : null;
    $colaboradores = $cargoId ? Colaborador::porCargo($cargoId) : [];

    render('gestionar_cargos/ver.php', [
        'cargo' => $cargo,
        'colaboradores' => $colaboradores,
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

if ($page === 'asignar_cargo') { // pantalla de asignación
    $cargos = Cargo::all();
    $colaboradores = Colaborador::all();
    render('asignar_cargo/index.php', [
        'cargos' => $cargos,
        'colaboradores' => $colaboradores,
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

$cargos = Cargo::all();
render('gestionar_cargos/index.php', [
    'cargos' => $cargos,
    'messages' => $messages,
    'errors' => $errors,
]);

