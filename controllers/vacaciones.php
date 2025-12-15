<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Vacacion.php';
require_once BASE_PATH . '/models/Colaborador.php';
require_once BASE_PATH . '/models/Resuelto.php';
require_once BASE_PATH . '/services/AuditService.php';
require_once BASE_PATH . '/services/PdfService.php';

// Gestionar vacaciones
if ($page === 'gestionar_vacaciones') {
    Authz::requireRoles(['recursos_humanos']);

    $colaboradores = Vacaciones::colaboradoresConVacaciones();

    render('vacaciones/gestionar.php', [
        'colaboradores' => $colaboradores
    ]);
    return;
}

// Generar resuelto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Authz::requireRoles(['recursos_humanos']);
    $data = [
        'colab_id' => $_POST['colab_id'],
        'nombre'   => $colaborador['colab_primer_nombre'] . ' ' . $colaborador['colab_apellido_paterno'],
        'cedula'   => $colaborador['colab_cedula'],
        'cargo'    => $colaborador['colab_car_cargo'],
        'dias'     => $_POST['dias_vacaciones'],
        'inicio'   => $_POST['periodo_inicio'],
        'fin'      => $_POST['periodo_fin'],
    ];

    $pdfPath = PdfService::generateResuelto($data);

    Vacaciones::guardarResuelto(
        $data['colab_id'],
        (int)$data['dias'],
        $pdfPath
    );

    Flash::success('Resuelto generado correctamente');
    redirect('gestionar_vacaciones');
}

