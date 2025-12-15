<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Vacaciones.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/models/Resuelto.php';
require_once BASE_PATH . '/services/AuditService.php';
require_once BASE_PATH . '/services/PdfService.php';

// ============================
// GESTIONAR VACACIONES
// ============================
if ($page === 'gestionar_vacaciones') {
    Authz::requireRoles(['administrador', 'recursos_humanos']);

    $vacaciones = Vacaciones::colaboradoresConVacaciones();

    render('vacaciones/index.php', [
        'colaboradores' => $colaboradores
    ]);
    return;
}

// ============================
// GENERAR RESUELTO (FORMULARIO)
// ============================
if ($page === 'generar_resuelto' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    Authz::requireRoles(['administrador', 'recursos_humanos']);

    $colabId = $_GET['id'] ?? null;
    $colaborador = null;

    if ($colabId) {
        $colaborador = Vacaciones::datosColaborador($colabId);
    }

    render('vacaciones/generar_resuelto.php', [
        'colaborador' => $colaborador,
        'messages' => [],
        'errors' => []
    ]);
    return;
}

// ============================
// GENERAR RESUELTO (POST)
// ============================
if ($page === 'generar_resuelto' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    Authz::requireRoles(['administrador', 'recursos_humanos']);

    $errors = [];
    $messages = [];

    $colabId = $_POST['colab_id'] ?? null;

    // 🔑 OBTENER COLABORADOR (ESTO FALTABA)
    $colaborador = $colabId ? Vacaciones::datosColaborador($colabId) : null;

    if (!$colaborador) {
        $errors[] = 'No se encontró el colaborador para generar el resuelto.';
        render('vacaciones/generar_resuelto.php', [
            'colaborador' => null,
            'messages' => [],
            'errors' => $errors
        ]);
        return;
    }

    // Armar data del resuelto
    $data = [
        'colab_id' => $colabId,
        'nombre'   => $colaborador['colab_primer_nombre'] . ' ' . $colaborador['colab_apellido_paterno'],
        'cedula'   => $colaborador['colab_cedula'],
        'cargo'    => $colaborador['colab_car_cargo'],
        'dias'     => (int)($_POST['dias_vacaciones'] ?? 0),
        'inicio'   => $_POST['periodo_inicio'] ?? '',
        'fin'      => $_POST['periodo_fin'] ?? '',
    ];

    // Validaciones mínimas
    if ($data['dias'] <= 0 || !$data['inicio'] || !$data['fin']) {
        $errors[] = 'Debe completar correctamente todos los campos.';
        render('vacaciones/generar_resuelto.php', [
            'colaborador' => $colaborador,
            'messages' => [],
            'errors' => $errors
        ]);
        return;
    }

    // Generar PDF
    $pdfPath = PdfService::generateResuelto($data);

    // Guardar resuelto
    Vacaciones::guardarResuelto(
        $data['colab_id'],
        $data['dias'],
        $pdfPath
    );

    Flash::success('Resuelto generado correctamente');
    redirect('gestionar_vacaciones');
}
