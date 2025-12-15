<?php
require_once BASE_PATH . '/middleware/auth.php'; // sesión/seguridad
require_once BASE_PATH . '/services/Authz.php'; // roles
require_once BASE_PATH . '/models/Colaborador.php'; // modelo colaborador
require_once BASE_PATH . '/models/Cargo.php'; // modelo cargo
require_once BASE_PATH . '/models/User.php'; // modelo usuario
require_once BASE_PATH . '/services/PasswordService.php'; // passwords
require_once BASE_PATH . '/services/AuditService.php'; // auditoría
require_once BASE_PATH . '/helpers/redirect.php'; // helper redirect
require_once BASE_PATH . '/helpers/sanitize.php'; // sanitización
require_once BASE_PATH . '/helpers/validator.php'; // validación

Authz::requireRoles(['administrador', 'recursos_humanos']); // solo admin/RRHH

$page = $_GET['page'] ?? 'gestionar_colaboradores'; // página actual
$messages = []; // feedback éxito
$errors = [];   // feedback error
$allowedStates = ['Activo', 'Vacaciones', 'Licencia', 'Incapacitado']; // estados permitidos

function handle_photo_upload(): string // sube foto y retorna ruta relativa
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
    $action = $_POST['action'] ?? ''; // acción enviada
    $currentUser = current_user(); // usuario sesión
    $actorId = $currentUser['user_id'] ?? ''; // para auditoría

    // Limpia flash previo
    $_SESSION['flash']['messages'] = [];
    $_SESSION['flash']['errors'] = [];

    if ($action === 'update_estado_colaborador') { // cambiar solo estado
        $colabId = $_POST['colab_id'] ?? '';
        $estado = trim($_POST['estado_colaborador'] ?? 'Activo');
        if (!$colabId) {
            $errors[] = 'Falta colab_id.';
        } elseif (!in_array($estado, $allowedStates, true)) {
            $errors[] = 'Estado inválido.';
        } else {
            if (Colaborador::updateEstado($colabId, $estado)) {
                $messages[] = 'Estado actualizado.';
                AuditService::log($actorId, 'colaborador', $colabId, "Actualizó estado a {$estado}");
            } else {
                $errors[] = 'No se pudo actualizar el estado.';
            }
        }
    }

    if ($action === 'create_colaborador') { // crear colaborador
        $foto = handle_photo_upload(); // procesa foto
        $estado = s_str($_POST['estado_colaborador'] ?? 'Activo');
        if (!in_array($estado, $allowedStates, true)) {
            $errors[] = 'Estado inválido.';
            $estado = 'Activo';
        }
        $data = [
            'primer_nombre' => s_str($_POST['primer_nombre'] ?? '', 100),
            'segundo_nombre' => s_str($_POST['segundo_nombre'] ?? '', 100),
            'apellido_paterno' => s_str($_POST['apellido_paterno'] ?? '', 100),
            'apellido_materno' => s_str($_POST['apellido_materno'] ?? '', 100),
            'sexo' => s_str($_POST['sexo'] ?? '', 10),
            'cedula' => s_numtxt($_POST['cedula'] ?? '', 50),
            'fecha_nac' => s_date($_POST['fecha_nac'] ?? ''),
            'correo' => s_email($_POST['correo'] ?? ''),
            'telefono' => s_numtxt($_POST['telefono'] ?? ''),
            'celular' => s_numtxt($_POST['celular'] ?? ''),
            'direccion' => s_str($_POST['direccion'] ?? '', 200),
            'foto_perfil' => $foto,
            'car_sueldo' => s_str($_POST['car_sueldo'] ?? '', 50),
            'car_cargo' => s_str($_POST['car_cargo'] ?? '', 100),
            'estado_colaborador' => $estado,
        ];

        $errors = array_merge($errors, v_required($data, [
            'primer_nombre' => 'Primer nombre',
            'apellido_paterno' => 'Apellido paterno',
            'segundo_nombre' => 'Segundo nombre',
            'apellido_materno' => 'Apellido materno',
            'sexo' => 'Sexo',
            'cedula' => 'Cédula',
            'fecha_nac' => 'Fecha de nacimiento',
            'correo' => 'Correo',
            'telefono' => 'Teléfono',
            'celular' => 'Celular',
            'direccion' => 'Dirección',
            'car_sueldo' => 'Sueldo',
            'car_cargo' => 'Cargo',
            'estado_colaborador' => 'Estado',
        ]));
        $errors = array_merge($errors,
            v_alpha($data['primer_nombre'], 'Primer nombre'),
            v_alpha($data['segundo_nombre'], 'Segundo nombre'),
            v_alpha($data['apellido_paterno'], 'Apellido paterno'),
            v_alpha($data['apellido_materno'], 'Apellido materno'),
            v_email($data['correo'], 'Correo'),
            v_pattern($data['cedula'], 'Cédula', '/^[0-9-]+$/', 'solo dígitos y guiones'),
            v_pattern($data['telefono'], 'Teléfono', '/^[0-9-]+$/', 'solo dígitos y guiones'),
            v_pattern($data['celular'], 'Celular', '/^[0-9-]+$/', 'solo dígitos y guiones'),
            v_pattern($data['car_sueldo'], 'Sueldo', '/^[0-9]+(\.[0-9]{1,2})?$/', 'solo números y hasta 2 decimales'),
            v_alpha($data['car_cargo'], 'Cargo'),
            v_in($data['sexo'], ['M', 'F'], 'Sexo'),
            v_in($estado, $allowedStates, 'Estado')
        );

        if (empty($errors)) {
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
                    redirect('gestionar_usuarios'); // saltar a gestión usuarios
                }
            } else {
                $errors[] = 'No se pudo crear el colaborador.';
            }
        }
    }

    if ($action === 'update_colaborador') { // actualizar colaborador
        $colabId = $_POST['colab_id'] ?? '';
        if ($colabId) {
            $foto = handle_photo_upload();
            $estado = s_str($_POST['estado_colaborador'] ?? 'Activo');
            if (!in_array($estado, $allowedStates, true)) {
                $errors[] = 'Estado inválido.';
                $estado = 'Activo';
            }
            $data = [
                'primer_nombre' => s_str($_POST['primer_nombre'] ?? '', 100),
                'segundo_nombre' => s_str($_POST['segundo_nombre'] ?? '', 100),
                'apellido_paterno' => s_str($_POST['apellido_paterno'] ?? '', 100),
                'apellido_materno' => s_str($_POST['apellido_materno'] ?? '', 100),
                'sexo' => s_str($_POST['sexo'] ?? '', 10),
                'cedula' => s_numtxt($_POST['cedula'] ?? '', 50),
                'fecha_nac' => s_date($_POST['fecha_nac'] ?? ''),
                'correo' => s_email($_POST['correo'] ?? ''),
                'telefono' => s_numtxt($_POST['telefono'] ?? ''),
                'celular' => s_numtxt($_POST['celular'] ?? ''),
                'direccion' => s_str($_POST['direccion'] ?? '', 200),
                'foto_perfil' => $foto ?: (trim($_POST['foto_actual'] ?? '')),
                'car_sueldo' => s_str($_POST['car_sueldo'] ?? '', 50),
                'car_cargo' => s_str($_POST['car_cargo'] ?? '', 100),
                'estado_colaborador' => $estado,
            ];
            $errors = array_merge($errors, v_required($data, [
                'primer_nombre' => 'Primer nombre',
                'apellido_paterno' => 'Apellido paterno',
                'segundo_nombre' => 'Segundo nombre',
                'apellido_materno' => 'Apellido materno',
                'sexo' => 'Sexo',
                'cedula' => 'Cédula',
                'fecha_nac' => 'Fecha de nacimiento',
                'correo' => 'Correo',
                'telefono' => 'Teléfono',
                'celular' => 'Celular',
                'direccion' => 'Dirección',
                'car_sueldo' => 'Sueldo',
                'car_cargo' => 'Cargo',
                'estado_colaborador' => 'Estado',
            ]));
            $errors = array_merge($errors,
                v_alpha($data['primer_nombre'], 'Primer nombre'),
                v_alpha($data['segundo_nombre'], 'Segundo nombre'),
                v_alpha($data['apellido_paterno'], 'Apellido paterno'),
                v_alpha($data['apellido_materno'], 'Apellido materno'),
                v_email($data['correo'], 'Correo'),
                v_pattern($data['cedula'], 'Cédula', '/^[0-9-]+$/', 'solo dígitos y guiones'),
                v_pattern($data['telefono'], 'Teléfono', '/^[0-9-]+$/', 'solo dígitos y guiones'),
                v_pattern($data['celular'], 'Celular', '/^[0-9-]+$/', 'solo dígitos y guiones'),
                v_pattern($data['car_sueldo'], 'Sueldo', '/^[0-9]+(\.[0-9]{1,2})?$/', 'solo números y hasta 2 decimales'),
                v_alpha($data['car_cargo'], 'Cargo'),
                v_in($data['sexo'], ['M', 'F'], 'Sexo'),
                v_in($estado, $allowedStates, 'Estado')
            );
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

    if ($action === 'delete_colaborador') { // mover a historial
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

if ($page === 'ver_colaborador') { // detalle de un colaborador
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

if ($page === 'ver_historial_cargos') { // historial de cargos
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
$cargos = Cargo::all();
render('gestionar_colaboradores/index.php', [
    'colaboradores' => $colaboradores,
    'cargos' => $cargos,
    'messages' => $messages,
    'errors' => $errors,
]);

