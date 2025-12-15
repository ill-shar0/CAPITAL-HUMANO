<?php
$user = current_user(); // usuario en sesión
$links = []; // links permitidos según rol

if ($user && $user['rol'] === 'administrador') {
    $links[] = ['Gestión de usuarios', BASE_URL . '/index.php?page=gestionar_usuarios'];
    $links[] = ['Cambiar contraseña', BASE_URL . '/index.php?page=cambiar_pw'];
    $links[] = ['Gestión de colaboradores', BASE_URL . '/index.php?page=gestionar_colaboradores'];
    $links[] = ['Gestión de cargos', BASE_URL . '/index.php?page=gestionar_cargos'];
    $links[] = ['Asignar cargo', BASE_URL . '/index.php?page=asignar_cargo'];
    $links[] = ['Gestionar asistencias', BASE_URL . '/index.php?page=gestionar_asistencias'];
    $links[] = ['Gestionar vacaciones', BASE_URL . '/index.php?page=gestionar_vacaciones'];
    $links[] = ['Reporte colaborador/sueldo', BASE_URL . '/index.php?page=reporte_colaborador_sueldo'];
    $links[] = ['Estadísticas', BASE_URL . '/index.php?page=estadisticas'];
} elseif ($user && $user['rol'] === 'recursos_humanos') {
    $links[] = ['Gestión de colaboradores', BASE_URL . '/index.php?page=gestionar_colaboradores'];
    $links[] = ['Gestión de cargos', BASE_URL . '/index.php?page=gestionar_cargos'];
    $links[] = ['Asignar cargo', BASE_URL . '/index.php?page=asignar_cargo'];
    $links[] = ['Gestionar asistencias', BASE_URL . '/index.php?page=gestionar_asistencias'];
    $links[] = ['Gestionar vacaciones', BASE_URL . '/index.php?page=gestionar_vacaciones'];
    $links[] = ['Reporte colaborador/sueldo', BASE_URL . '/index.php?page=reporte_colaborador_sueldo'];
    $links[] = ['Estadísticas', BASE_URL . '/index.php?page=estadisticas'];
}

if ($user && in_array($user['rol'], ['colaborador', 'administrador', 'recursos_humanos'], true)) {
    $links[] = ['Registrar asistencia', BASE_URL . '/index.php?page=registrar_asistencia'];
    $links[] = ['Ver asistencias personales', BASE_URL . '/index.php?page=ver_asistencias_personal'];
}

ob_start();
?>
<nav class="sidebar-nav">
    <?php foreach ($links as [$label, $href]): ?>
        <a class="nav-link" href="<?= $href ?>"><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
</nav>
<?php
$nav = ob_get_clean();

