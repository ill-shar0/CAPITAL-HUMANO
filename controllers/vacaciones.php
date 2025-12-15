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
    Authz::requireRoles(['recursos_humanos']);

    $vacaciones = Vacaciones::colaboradoresConVacaciones();

    render('vacaciones/index.php', [
        'vacaciones' => $vacaciones
    ]);
    return;
}

// Generar resuelto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Authz::requireRoles(['recursos_humanos']);
    $data = [
        'colab_id' => $colabId,
        'nombre'   => $colaborador['colab_primer_nombre'] . ' ' . $colaborador['colab_apellido_paterno'],
        'cedula'   => $colaborador['colab_cedula'],
        'cargo'    => $colaborador['colab_car_cargo'],
        'dias'     => (int)($_POST['dias_vacaciones'] ?? 0),
        'inicio'   => $_POST['periodo_inicio'] ?? '',
        'fin'      => $_POST['periodo_fin'] ?? '',
    ];

    if ($data['dias'] <= 0 || !$data['inicio'] || !$data['fin']) {
        $errors[] = 'Debe completar correctamente todos los campos.';
        render('vacaciones/generar_resuelto.php', [
            'colaborador' => $colaborador,
            'messages' => [],
            'errors' => $errors
        ]);
        return;
    }

    $pdfPath = PdfService::generateResuelto($data);

    Vacaciones::guardarResuelto(
        $data['colab_id'],
        $data['dias'],
        $pdfPath
    );

    Flash::success('Resuelto generado correctamente');
    redirect('gestionar_vacaciones');
}
