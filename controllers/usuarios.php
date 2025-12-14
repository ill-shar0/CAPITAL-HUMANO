<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/services/PasswordService.php';
require_once BASE_PATH . '/services/AuditService.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/helpers/flash.php';
require_once BASE_PATH . '/helpers/redirect.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_usuarios';
$flash = Flash::get();

function flash_redirect(string $msg, string $type = 'error', string $target = 'gestionar_usuarios'): void
{
    $type === 'success' ? Flash::success($msg) : Flash::error($msg);
    redirect($target);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $currentUser = current_user();
    $actorId = $currentUser['user_id'] ?? '';

    if ($action === 'create_user') {
        $username = trim($_POST['username'] ?? '');
        $rol = trim($_POST['rol'] ?? 'colaborador');
        $estado = '1';
        $cedula = trim($_POST['cedula'] ?? '');

        if ($username === '' || $rol === '' || $cedula === '') {
            flash_redirect('Username, rol y cédula son obligatorios.');
        }

        $colab = Colaborador::findByCedula($cedula);
        if (!$colab) {
            flash_redirect('No existe un colaborador con esa cédula; primero registre el colaborador.');
        }

        $existing = User::findByColabId($colab['colab_id']);
        if ($existing && $existing['estado_usuario'] === '1') {
            flash_redirect('Este colaborador ya tiene una cuenta activa.');
        }

        $plain = PasswordService::generate();
        $hash = PasswordService::hash($plain);

        if ($existing && $existing['estado_usuario'] !== '1') {
            $okPw = User::setPassword($existing['user_id'], $hash);
            $okEstado = User::toggleEstado($existing['user_id'], '1');
            $okPw && $okEstado
                ? Flash::success("Cuenta reactivada. usuario={$existing['username']}, password={$plain}")
                : Flash::error('No se pudo reactivar la cuenta existente.');
            AuditService::log($actorId, 'usuario', $existing['user_id'], 'Reactivó usuario');
        } else {
            $newId = User::create($username, $hash, $rol, $estado, $colab['colab_id']);
            if ($newId) {
                Flash::success("Usuario creado. Credenciales: usuario={$username}, password={$plain}");
                AuditService::log($actorId, 'usuario', $newId, "Creó usuario {$username}");
            } else {
                Flash::error('No se pudo crear el usuario (posible username duplicado).');
            }
        }
        redirect('gestionar_usuarios');
    }

    if ($action === 'update_role_state') {
        $userId = $_POST['user_id'] ?? '';
        $rol = trim($_POST['rol'] ?? '');
        $estado = trim($_POST['estado'] ?? '1');
        if ($userId && $rol !== '') {
            if (User::updateRoleState($userId, $rol, $estado)) {
                Flash::success('Rol/estado actualizados.');
                AuditService::log($actorId, 'usuario', $userId, "Actualizó rol/estado a {$rol}/{$estado}");
            } else {
                Flash::error('No se pudo actualizar rol/estado.');
            }
        } else {
            Flash::error('Faltan datos para actualizar rol/estado.');
        }
        redirect('gestionar_usuarios');
    }

    if ($action === 'toggle_estado') {
        $userId = $_POST['user_id'] ?? '';
        $estadoActual = $_POST['estado_actual'] ?? '1';
        if ($userId) {
            $nuevo = ($estadoActual === '1') ? '0' : '1';
            if (User::toggleEstado($userId, $nuevo)) {
                Flash::success('Estado actualizado.');
                AuditService::log($actorId, 'usuario', $userId, "Cambio estado a {$nuevo}");
            } else {
                Flash::error('No se pudo cambiar el estado.');
            }
        } else {
            Flash::error('Falta user_id.');
        }
        redirect('gestionar_usuarios');
    }

    if ($action === 'change_pw') {
        $userId = $_POST['user_id'] ?? '';
        $newPw = trim($_POST['new_password'] ?? '');
        if ($userId && $newPw !== '') {
            $hash = PasswordService::hash($newPw);
            if (User::setPassword($userId, $hash)) {
                Flash::success("Contraseña actualizada. Nueva password: {$newPw}");
                AuditService::log($actorId, 'password', $userId, 'Cambio de contraseña');
            } else {
                Flash::error('No se pudo actualizar la contraseña.');
            }
        } else {
            Flash::error('Debe seleccionar usuario y nueva contraseña.');
        }
        redirect('cambiar_pw');
    }
}

if ($page === 'cambiar_pw') {
    $users = User::all();
    render('gestionar_usuarios/cambiar_pw.php', [
        'users' => $users,
        'flash' => $flash,
    ]);
    return;
}

$users = User::all();
render('gestionar_usuarios/index.php', [
    'users' => $users,
    'flash' => $flash,
]);

