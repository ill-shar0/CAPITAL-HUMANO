<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/services/PasswordService.php';
require_once BASE_PATH . '/services/AuditService.php';
require_once BASE_PATH . '/helpers/sanitize.php';
require_once BASE_PATH . '/helpers/validator.php';

Authz::requireRoles(['administrador', 'recursos_humanos']); // solo admin/RRHH

$page = $_GET['page'] ?? 'gestionar_usuarios'; // página actual

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

function flash_success($msg) { // helper éxito
  $_SESSION['flash']['messages'][] = $msg;
}
function flash_error($msg) { // helper error
  $_SESSION['flash']['errors'][] = $msg;
}

$currentUser = current_user();           // usuario en sesión
$actorId = $currentUser['user_id'] ?? ''; // para auditoría

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? ''; // acción enviada

  if ($action === 'create_user') { // alta usuario
    $cedula = s_numtxt($_POST['cedula'] ?? '', 50);
    $primerNombre = s_str($_POST['primer_nombre'] ?? '', 100);
    $apellidoPaterno = s_str($_POST['apellido_paterno'] ?? '', 100);
    $rol = s_str($_POST['rol'] ?? 'colaborador', 30);
    $estado = '1';

    $allowedRoles = ['administrador', 'recursos_humanos', 'colaborador'];

    $localErrors = [];
    $localErrors = array_merge($localErrors, v_required([
      'cedula' => $cedula,
      'primerNombre' => $primerNombre,
      'apellidoPaterno' => $apellidoPaterno,
      'rol' => $rol,
    ], [
      'cedula' => 'Cédula',
      'primerNombre' => 'Nombre',
      'apellidoPaterno' => 'Apellido',
      'rol' => 'Rol',
    ]));
    $localErrors = array_merge($localErrors,
      v_alpha($primerNombre, 'Nombre'),
      v_alpha($apellidoPaterno, 'Apellido'),
      v_pattern($cedula, 'Cédula', '/^[0-9-]+$/', 'solo dígitos y guiones'),
      v_in($rol, $allowedRoles, 'Rol')
    );

    if (!empty($localErrors)) {
      foreach ($localErrors as $err) flash_error($err);
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

  if ($action === 'update_role_state') { // cambiar rol/estado
    $userId = $_POST['user_id'] ?? '';
    $rol = s_str($_POST['rol'] ?? '', 30);
    $estado = s_str($_POST['estado'] ?? '1', 1);
    $allowedRoles = ['administrador', 'recursos_humanos', 'colaborador'];

    $localErrors = [];
    if (!$userId) $localErrors[] = 'Falta user_id.';
    $localErrors = array_merge($localErrors, v_in($rol, $allowedRoles, 'Rol'));
    if (!in_array($estado, ['0', '1'], true)) {
      $localErrors[] = 'Estado inválido.';
    }

    if (empty($localErrors)) {
      if (User::updateRoleState($userId, $rol, $estado)) {
        flash_success('Rol/estado actualizados.');
        AuditService::log($actorId, 'usuario', $userId, "Actualizó rol/estado a {$rol}/{$estado}");
      } else {
        flash_error('No se pudo actualizar rol/estado.');
      }
    } else {
      foreach ($localErrors as $err) flash_error($err);
    }
  }

  if ($action === 'toggle_estado') { // activar/desactivar
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

  if ($action === 'change_pw') { // cambio de password
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

if ($page === 'cambiar_pw') { // vista de cambio de pw
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
