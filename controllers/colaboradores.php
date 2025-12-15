<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/models/Cargo.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/services/PasswordService.php';
require_once BASE_PATH . '/services/AuditService.php';
require_once BASE_PATH . '/helpers/redirect.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$page = $_GET['page'] ?? 'gestionar_colaboradores';
$messages = [];
$errors = [];

function handle_photo_upload(): string
{
    if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
        return '';
    }
    $tmp = $_FILES['foto_perfil']['tmp_name'];
    $name = basename($_FILES['foto_perfil']['name']);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        return '';
    }
    $destName = uniqid('foto_', true) . '.' . $ext;
    $destPath = BASE_PATH . '/public/uploads/fotos/' . $destName;
    if (move_uploaded_file($tmp, $destPath)) {
        return 'uploads/fotos/' . $destName; // sin slash inicial para no duplicar en la vista
    }
    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $currentUser = current_user();
    $actorId = $currentUser['user_id'] ?? '';

    // Limpia flash previo
    $_SESSION['flash']['messages'] = [];
    $_SESSION['flash']['errors'] = [];

    if ($action === 'create_colaborador') {
        $foto = handle_photo_upload();
        $data = [
            'primer_nombre' => trim($_POST['primer_nombre'] ?? ''),
            'segundo_nombre' => trim($_POST['segundo_nombre'] ?? ''),
            'apellido_paterno' => trim($_POST['apellido_paterno'] ?? ''),
            'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
            'sexo' => trim($_POST['sexo'] ?? ''),
            'cedula' => trim($_POST['cedula'] ?? ''),
            'fecha_nac' => trim($_POST['fecha_nac'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'celular' => trim($_POST['celular'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'foto_perfil' => $foto,
            'car_sueldo' => trim($_POST['car_sueldo'] ?? ''),
            'car_cargo' => trim($_POST['car_cargo'] ?? ''),
            'estado_colaborador' => 'Activo',
        ];

        if ($data['primer_nombre'] === '' || $data['apellido_paterno'] === '') {
            $errors[] = 'Primer nombre y apellido paterno son obligatorios.';
        } else {
            $newId = Colaborador::create($data);
            if ($newId) {
                $messages[] = 'Colaborador creado.';
                AuditService::log($actorId, 'colaborador', $newId, 'Creó colaborador');

                // Crear usuario rol colaborador si se solicitó
                if (($_POST['crear_usuario'] ?? '') === 'si') {
                    // Redirigir a gestionar usuarios con datos precargados
                    $_SESSION['prefill_usuario'] = [
                        'cedula' => $data['cedula'],
                        'primer_nombre' => $data['primer_nombre'],
                        'apellido_paterno' => $data['apellido_paterno'],
                    ];
                    $messages[] = 'Colaborador creado. Continúa creando la cuenta de usuario en "Gestionar usuarios".';
                    $_SESSION['flash']['messages'] = $messages;
                    $_SESSION['flash']['errors'] = $errors;
                    redirect('gestionar_usuarios');
                }
            } else {
                $errors[] = 'No se pudo crear el colaborador.';
            }
        }
    }

    if ($action === 'update_colaborador') {
        $colabId = $_POST['colab_id'] ?? '';
        if ($colabId) {
            $foto = handle_photo_upload();
            $data = [
                'primer_nombre' => trim($_POST['primer_nombre'] ?? ''),
                'segundo_nombre' => trim($_POST['segundo_nombre'] ?? ''),
                'apellido_paterno' => trim($_POST['apellido_paterno'] ?? ''),
                'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
                'sexo' => trim($_POST['sexo'] ?? ''),
                'cedula' => trim($_POST['cedula'] ?? ''),
                'fecha_nac' => trim($_POST['fecha_nac'] ?? ''),
                'correo' => trim($_POST['correo'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'celular' => trim($_POST['celular'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'foto_perfil' => $foto ?: (trim($_POST['foto_actual'] ?? '')),
                'car_sueldo' => trim($_POST['car_sueldo'] ?? ''),
                'car_cargo' => trim($_POST['car_cargo'] ?? ''),
                'estado_colaborador' => trim($_POST['estado_colaborador'] ?? 'Activo'),
            ];
            if (Colaborador::updateColab($colabId, $data)) {
                $messages[] = 'Colaborador actualizado.';
                AuditService::log($actorId, 'colaborador', $colabId, 'Actualizó colaborador');
            } else {
                $errors[] = 'No se pudo actualizar el colaborador.';
            }
        } else {
            $errors[] = 'Falta colab_id.';
        }
    }

    if ($action === 'delete_colaborador') {
        $colabId = $_POST['colab_id'] ?? '';
        if ($colabId) {
            if (Colaborador::moveToHistorial($colabId)) {
                $messages[] = 'Colaborador movido a historial.';
                AuditService::log($actorId, 'colaborador', $colabId, 'Movió a historial');
            } else {
                $errors[] = 'No se pudo mover a historial.';
            }
        } else {
            $errors[] = 'Falta colab_id.';
        }
    }

    // PRG: redirigir siempre tras POST para no duplicar envíos
    $_SESSION['flash']['messages'] = array_merge($_SESSION['flash']['messages'] ?? [], $messages);
    $_SESSION['flash']['errors'] = array_merge($_SESSION['flash']['errors'] ?? [], $errors);
    redirect('gestionar_colaboradores');
}

if ($page === 'ver_colaborador') {
    $colaboradorId = $_GET['id'] ?? null;
    $colaborador = $colaboradorId ? Colaborador::find($colaboradorId) : null;
    $cargoActual = $colaborador ? Cargo::findActivoByColaborador($colaboradorId) : null;
    $historialCargos = $colaborador ? Cargo::historialPorColaborador($colaboradorId) : [];

    render('gestionar_colaboradores/ver.php', [
        'colaborador' => $colaborador,
        'cargoActual' => $cargoActual,
        'historialCargos' => $historialCargos,
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

if ($page === 'ver_historial_cargos') {
    $colaboradorId = $_GET['id'] ?? null;
    $colaborador = $colaboradorId ? Colaborador::find($colaboradorId) : null;
    $historialCargos = $colaborador ? Cargo::historialPorColaborador($colaboradorId) : [];

    render('gestionar_colaboradores/historial_cargos.php', [
        'colaborador' => $colaborador,
        'historialCargos' => $historialCargos,
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

$colaboradores = Colaborador::all();
render('gestionar_colaboradores/index.php', [
    'colaboradores' => $colaboradores,
    'messages' => $messages,
    'errors' => $errors,
]);

