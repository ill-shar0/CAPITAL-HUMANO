<?php
require_once BASE_PATH . '/middleware/auth.php';
require_once BASE_PATH . '/services/Authz.php';
require_once BASE_PATH . '/models/Colaborador.php';

Authz::requireRoles(['administrador', 'recursos_humanos']);

$estadisticas = Colaborador::estadisticas();

render('estadisticas/index.php', [
    'estadisticas' => $estadisticas,
]);

