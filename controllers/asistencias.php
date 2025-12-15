<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Asistencia.php';

$page = $_GET['page'] ?? 'registrar_asistencia';
$messages = [];
$errors = [];

Authz::requireRoles(['colaborador', 'administrador', 'recursos_humanos']);

// ============================
// VER ASISTENCIAS PERSONALES
// ============================
if ($page === 'ver_asistencias_personal') {

   require_once BASE_PATH . '/models/User.php';

$user = current_user();
$colabId = $user['usu_colab_id'] ?? ($user['colab_id'] ?? null);

// ✅ Si la sesión no trae el colab_id, lo consultamos en BD
if (!$colabId) {
    $userId = $user['user_id'] ?? null;
    if ($userId) {
        $colabId = User::colabIdByUserId((string)$userId);
    }
}

    $historial = $colabId ? Asistencia::porColaborador($colabId) : [];
    if (!$colabId) {
        $errors[] = 'Tu usuario no tiene colaborador asociado (usu_colab_id / colab_id).';
    }

    render('asistencias/personal.php', [
        'historial' => $historial,
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

// ============================
// GESTIONAR ASISTENCIAS (RRHH/ADMIN)
// ============================
if ($page === 'gestionar_asistencias') {
    Authz::requireRoles(['administrador', 'recursos_humanos']);
    $historial = Asistencia::todas();

    render('asistencias/gestionar.php', [
        'historial' => $historial,
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

// ============================
// REGISTRAR ASISTENCIA
// ============================
if ($page === 'registrar_asistencia') {

    $user = current_user();
    $colabId = $user['usu_colab_id'] ?? $user['colab_id'] ?? null;

    $messages = [];
    $errors = [];
    $confirmAction = null;

    $tz = new DateTimeZone('America/Panama');
    $now = new DateTime('now', $tz);

    $fechaActual = $now->format('Y-m-d');
    $horaActual  = $now->format('H:i:s');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion  = $_POST['accion'] ?? '';
        $confirm = $_POST['confirm'] ?? null;

        if (!$colabId) {
            $errors[] = 'Tu usuario no tiene colaborador asociado.';
        } else {

            // Si aún NO confirmó, mostramos modal
            if (!$confirm && in_array($accion, ['entrada', 'salida'], true)) {
                $confirmAction = $accion;
            }

            // Confirmado → ejecutar
            if ($confirm === 'yes' && $accion === 'entrada') {
                $ok = Asistencia::registrarEntrada($colabId, $fechaActual, $horaActual);
                if ($ok) {
                    $messages[] = "✅ Entrada registrada a las {$horaActual}";
                } else {
                    $errors[] = "⚠ Ya existe una entrada registrada hoy.";
                }
            }

            if ($confirm === 'yes' && $accion === 'salida') {
                $ok = Asistencia::registrarSalida($colabId, $fechaActual, $horaActual);
                if ($ok) {
                    $messages[] = "✅ Salida registrada a las {$horaActual}";
                } else {
                    $errors[] = "⚠ No hay entrada sin salida para hoy.";
                }
            }
        }
    }

    // Historial
    $historial = $colabId ? Asistencia::porColaborador($colabId) : [];

    render('asistencias/registrar.php', [
        'messages' => $messages,
        'errors' => $errors,
        'confirmAction' => $confirmAction,
        'historial' => $historial,
        'fechaActual' => $fechaActual,
        'horaActual' => $horaActual,
    ]);
    return;
}
