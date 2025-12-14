<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/services/PasswordService.php';
require_once BASE_PATH . '/services/AuditService.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_usuarios';
$messages = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $currentUser = current_user();
    $actorId = $currentUser['user_id'] ?? '';

    if ($action === 'create_user') {
        $username = trim($_POST['username'] ?? '');
        $rol = trim($_POST['rol'] ?? 'colaborador');
        $estado = '1';

        if ($username === '' || $rol === '') {
            $errors[] = 'Username y rol son obligatorios.';
        } else {
            $plain = PasswordService::generate();
            $hash = PasswordService::hash($plain);
            $newId = User::create($username, $hash, $rol, $estado, null);
            if ($newId) {
                $messages[] = "Usuario creado. Credenciales: usuario={$username}, password={$plain}";
            } else {
                $errors[] = 'No se pudo crear el usuario (posible username duplicado).';
            }
        }
    }

    if ($action === 'update_role_state') {
        $userId = $_POST['user_id'] ?? '';
        $rol = trim($_POST['rol'] ?? '');
        $estado = trim($_POST['estado'] ?? '1');
        if ($userId && $rol !== '') {
            if (User::updateRoleState($userId, $rol, $estado)) {
                $messages[] = 'Rol/estado actualizados.';
                AuditService::log($actorId, 'usuario', $userId, "Actualizó rol/estado a {$rol}/{$estado}");
            } else {
                $errors[] = 'No se pudo actualizar rol/estado.';
            }
        } else {
            $errors[] = 'Faltan datos para actualizar rol/estado.';
        }
    }

    if ($action === 'toggle_estado') {
        $userId = $_POST['user_id'] ?? '';
        $estadoActual = $_POST['estado_actual'] ?? '1';
        if ($userId) {
            $nuevo = ($estadoActual === '1') ? '0' : '1';
            if (User::toggleEstado($userId, $nuevo)) {
                $messages[] = 'Estado actualizado.';
                AuditService::log($actorId, 'usuario', $userId, "Cambio estado a {$nuevo}");
            } else {
                $errors[] = 'No se pudo cambiar el estado.';
            }
        } else {
            $errors[] = 'Falta user_id.';
        }
    }

    if ($action === 'change_pw') {
        $userId = $_POST['user_id'] ?? '';
        $newPw = trim($_POST['new_password'] ?? '');
        if ($userId && $newPw !== '') {
            $hash = PasswordService::hash($newPw);
            if (User::setPassword($userId, $hash)) {
                $messages[] = "Contraseña actualizada. Nueva password: {$newPw}";
                AuditService::log($actorId, 'password', $userId, 'Cambio de contraseña');
            } else {
                $errors[] = 'No se pudo actualizar la contraseña.';
            }
        } else {
            $errors[] = 'Debe seleccionar usuario y nueva contraseña.';
        }
    }
}

if ($page === 'cambiar_pw') {
    $users = User::all();
    render('gestionar_usuarios/cambiar_pw.php', [
        'users' => $users,
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

$users = User::all();
render('gestionar_usuarios/index.php', [
    'users' => $users,
    'messages' => $messages,
    'errors' => $errors,
]);

