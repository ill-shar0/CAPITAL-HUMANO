<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', __DIR__ . '/..');
<<<<<<< HEAD
$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $baseUrl);

=======

$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $baseUrl);
>>>>>>> 1f92c73fb97e286b08790676ba8622dca2dc6543

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

