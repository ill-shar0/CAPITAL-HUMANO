<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/services/PasswordService.php';
require_once BASE_PATH . '/services/AuditService.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_usuarios';

/**
 * Flash (para no duplicar POST al refrescar)
 */
if (!isset($_SESSION['flash'])) $_SESSION['flash'] = ['messages' => [], 'errors' => []];

$messages = $_SESSION['flash']['messages'] ?? [];
$errors   = $_SESSION['flash']['errors'] ?? [];
$_SESSION['flash'] = ['messages' => [], 'errors' => []]; // limpiar

// Prefill (por redirección desde colaboradores)
$prefill = $_SESSION['prefill_usuario'] ?? null;
unset($_SESSION['prefill_usuario']);

function flash_success($msg) {
  $_SESSION['flash']['messages'][] = $msg;
}
function flash_error($msg) {
  $_SESSION['flash']['errors'][] = $msg;
}

$currentUser = current_user();
$actorId = $currentUser['user_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'create_user') {
    $cedula = trim($_POST['cedula'] ?? '');
    $primerNombre = trim($_POST['primer_nombre'] ?? '');
    $apellidoPaterno = trim($_POST['apellido_paterno'] ?? '');
    $rol = trim($_POST['rol'] ?? 'colaborador');
    $estado = '1';

    if ($cedula === '' || $primerNombre === '' || $apellidoPaterno === '' || $rol === '') {
      flash_error('Cédula, nombre, apellido y rol son obligatorios.');
    } else {
      // Buscar colaborador por cédula
      $colab = Colaborador::findByCedula($cedula);
      if (!$colab) {
        flash_error('El colaborador no existe. Primero registre al colaborador.');
      } else {
        // Si ya tiene usuario
        $userByColab = User::findByColabId($colab['colab_id']);
        if ($userByColab) {
          if (($userByColab['estado_usuario'] ?? '1') === '0') {
            // Reactivar y regenerar password
            $plain = PasswordService::generate();
            $hash  = PasswordService::hash($plain);
            $reactivated = User::setPassword($userByColab['user_id'], $hash)
              && User::toggleEstado($userByColab['user_id'], '1');
            if ($reactivated) {
              flash_success("Cuenta reactivada. Credenciales: usuario={$userByColab['username']}, password={$plain}");
              AuditService::log($actorId, 'usuario', $userByColab['user_id'], 'Reactivó cuenta por cédula');
            } else {
              flash_error('No se pudo reactivar la cuenta.');
            }
          } else {
            flash_error('El colaborador ya tiene una cuenta activa.');
          }
        } else {
          // Generar username único
          $username = User::generateUsername(
            $colab['primer_nombre'] ?? $primerNombre,
            $colab['apellido_paterno'] ?? $apellidoPaterno
          );

          $plain = PasswordService::generate();
          $hash  = PasswordService::hash($plain);

          $ok = User::create($username, $hash, $rol, $estado, $colab['colab_id']);

          if ($ok) {
            flash_success("Usuario creado. Credenciales: usuario={$username}, password={$plain}");
            AuditService::log($actorId, 'usuario', $username, "Creó usuario con rol {$rol} vinculado a colaborador");
          } else {
            flash_error('No se pudo crear el usuario (posible duplicado).');
          }
        }
      }
    }
  }

  if ($action === 'update_role_state') {
    $userId = $_POST['user_id'] ?? '';
    $rol = trim($_POST['rol'] ?? '');
    $estado = trim($_POST['estado'] ?? '1');

    if ($userId && $rol !== '') {
      if (User::updateRoleState($userId, $rol, $estado)) {
        flash_success('Rol/estado actualizados.');
        AuditService::log($actorId, 'usuario', $userId, "Actualizó rol/estado a {$rol}/{$estado}");
      } else {
        flash_error('No se pudo actualizar rol/estado.');
      }
    } else {
      flash_error('Faltan datos para actualizar rol/estado.');
    }
  }

  if ($action === 'toggle_estado') {
    $userId = $_POST['user_id'] ?? '';
    $estadoActual = $_POST['estado_actual'] ?? '1';

    if ($userId) {
      $nuevo = ($estadoActual === '1') ? '0' : '1';
      if (User::toggleEstado($userId, $nuevo)) {
        flash_success('Estado actualizado.');
        AuditService::log($actorId, 'usuario', $userId, "Cambio estado a {$nuevo}");
      } else {
        flash_error('No se pudo cambiar el estado.');
      }
    } else {
      flash_error('Falta user_id.');
    }
  }

  if ($action === 'change_pw') {
    $userId = $_POST['user_id'] ?? '';
    $newPw = trim($_POST['new_password'] ?? '');

    if ($userId && $newPw !== '') {
      $hash = PasswordService::hash($newPw);
      if (User::setPassword($userId, $hash)) {
        flash_success("Contraseña actualizada. Nueva password: {$newPw}");
        AuditService::log($actorId, 'password', $userId, 'Cambio de contraseña');
      } else {
        flash_error('No se pudo actualizar la contraseña.');
      }
    } else {
      flash_error('Debe seleccionar usuario y nueva contraseña.');
    }
  }

  // ✅ PRG: evita reenvío del formulario al refrescar
  header('Location: ' . BASE_URL . '/index.php?page=' . urlencode($page));
  exit;
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
  'prefill' => $prefill,
]);
