<?php
require_once BASE_PATH . '/middleware/auth.php'; // sesión/seguridad
require_once BASE_PATH . '/services/Authz.php'; // roles
require_once BASE_PATH . '/models/Colaborador.php'; // datos colaboradores

Authz::requireRoles(['administrador', 'recursos_humanos']); // solo admin/RRHH

$estadisticas = Colaborador::estadisticas(); // datos para charts

render('estadisticas/index.php', [
    'estadisticas' => $estadisticas, // pasa al view
]);

