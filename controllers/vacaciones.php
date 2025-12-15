<?php
require_once BASE_PATH . '/middleware/auth.php'; // sesión/seguridad
require_once BASE_PATH . '/services/Authz.php'; // roles
require_once BASE_PATH . '/models/Vacaciones.php'; // modelo vacaciones
require_once BASE_PATH . '/models/Colaborador.php'; // modelo colaborador
require_once BASE_PATH . '/models/Resuelto.php'; // modelo resuelto
require_once BASE_PATH . '/services/AuditService.php'; // auditoría
require_once BASE_PATH . '/services/PdfService.php'; // PDF resueltos
require_once BASE_PATH . '/helpers/flash.php'; // mensajes flash
require_once BASE_PATH . '/helpers/redirect.php'; // helper redirect

$page = $_GET['page'] ?? 'gestionar_vacaciones'; // página solicitada
$currentUser = current_user(); // usuario en sesión
$actorId = $currentUser['user_id'] ?? ''; // para auditoría

// ============================
// GESTIONAR VACACIONES
// ============================
if ($page === 'gestionar_vacaciones') {
    Authz::requireRoles(['recursos_humanos', 'administrador']); // RRHH y Admin

    $flash = Flash::get(); // recoger flash PRG
    $messages = [];
    $errors = [];
    if (!empty($flash['success'])) $messages[] = $flash['success'];
    if (!empty($flash['error'])) $errors[] = $flash['error'];

    $vacaciones = Vacaciones::colaboradoresConVacaciones(); // lista con disponibles y último PDF

    render('vacaciones/index.php', [
        'vacaciones' => $vacaciones,
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

// ============================
// GENERAR RESUELTO (GET)
// ============================
if ($page === 'generar_resuelto' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    Authz::requireRoles(['recursos_humanos', 'administrador']); // RRHH/Admin

    $colabId = $_GET['id'] ?? ''; // id de colaborador
    if ($colabId === '') {
        Flash::error('Falta el colaborador para generar el resuelto.');
        redirect('gestionar_vacaciones');
    }

    $colaborador = Vacaciones::findByColabId($colabId); // datos + días disponibles
    $messages = [];
    $errors = [];
    $flash = Flash::get();
    if (!empty($flash['success'])) $messages[] = $flash['success'];
    if (!empty($flash['error'])) $errors[] = $flash['error'];

    render('vacaciones/generar_resuelto.php', [
        'colaborador' => $colaborador,
        'messages' => $messages,
        'errors' => $errors,
    ]);
    return;
}

// ============================
// GENERAR RESUELTO (POST)
// ============================
if ($page === 'generar_resuelto' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    Authz::requireRoles(['recursos_humanos', 'administrador']); // RRHH/Admin

    $action = $_POST['action'] ?? ''; // acción enviada
    if ($action !== 'create_resuelto') {
        redirect('gestionar_vacaciones');
    }

    $colabId = $_POST['colab_id'] ?? ''; // colaborador destino
    if ($colabId === '') {
        Flash::error('Falta el colaborador para generar el resuelto.');
        redirect('gestionar_vacaciones');
    }

    $colaborador = Vacaciones::findByColabId($colabId); // datos + disponibles
    if (!$colaborador) {
        Flash::error('No se encontró el colaborador.');
        redirect('gestionar_vacaciones');
    }

    $diasDisponibles = (int)($colaborador['dias_disponibles'] ?? 0); // cupo actual
    $dias = (int)($_POST['dias_vacaciones'] ?? 0); // solicitados
    $inicio = trim($_POST['periodo_inicio'] ?? ''); // fecha inicio
    $fin = trim($_POST['periodo_fin'] ?? ''); // fecha fin

    if ($dias < 7) {
        Flash::error('Debe solicitar mínimo 7 días.');
        redirect(BASE_URL . '/index.php?page=generar_resuelto&id=' . urlencode($colabId));
    }
    if ($dias > 30) {
        Flash::error('Máximo 30 días por resuelto.');
        redirect(BASE_URL . '/index.php?page=generar_resuelto&id=' . urlencode($colabId));
    }
    if ($diasDisponibles > 0 && $dias > $diasDisponibles) {
        Flash::error('No puede pedir más días de los disponibles.');
        redirect(BASE_URL . '/index.php?page=generar_resuelto&id=' . urlencode($colabId));
    }
    if ($inicio === '' || $fin === '') {
        Flash::error('Debe completar el periodo de inicio y fin.');
        redirect(BASE_URL . '/index.php?page=generar_resuelto&id=' . urlencode($colabId));
    }

    $data = [
        'colab_id' => $colabId,
        'nombre'   => ($colaborador['colab_primer_nombre'] ?? '') . ' ' . ($colaborador['colab_apellido_paterno'] ?? ''),
        'cedula'   => $colaborador['colab_cedula'] ?? '',
        'cargo'    => $colaborador['colab_car_cargo'] ?? '',
        'dias'     => $dias,
        'inicio'   => $inicio,
        'fin'      => $fin,
    ];

    $pdfPath = PdfService::generateResuelto($data);

    $ok = Vacaciones::guardarResuelto(
        $data['colab_id'],
        $data['dias'],
        $data['inicio'],
        $data['fin'],
        $pdfPath
    );

    if ($ok) {
        AuditService::log($actorId, 'resuelto', $colabId, "Generó resuelto de vacaciones ({$dias} días)");
        Flash::success('Resuelto generado correctamente.');
    } else {
        Flash::error('No se pudo generar el resuelto.');
    }

    redirect('gestionar_vacaciones');
}
