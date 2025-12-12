<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', __DIR__ . '/..');
define('BASE_URL', '/SEMESTRAL-CAPITAL-HUMANO/public');

date_default_timezone_set('UTC');

function render(string $view, array $data = []): void
{
    $viewPath = BASE_PATH . '/views/' . $view;
    if (!file_exists($viewPath)) {
        http_response_code(404);
        echo 'Vista no encontrada';
        return;
    }

    extract($data);

    ob_start();
    include $viewPath;
    $content = ob_get_clean();

    include BASE_PATH . '/views/layouts/main.php';
}

