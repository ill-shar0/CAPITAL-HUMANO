<?php
if (session_status() === PHP_SESSION_NONE) { // iniciar sesión si no existe
    session_start();
}

define('BASE_PATH', __DIR__ . '/..'); // raíz del proyecto
$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'); // base url calculada
define('BASE_URL', $baseUrl); // URL base pública

date_default_timezone_set('UTC'); // zona horaria por defecto

function render(string $view, array $data = []): void
{
    $viewPath = BASE_PATH . '/views/' . $view; // ruta a la vista
    if (!file_exists($viewPath)) { // 404 si falta
        http_response_code(404);
        echo 'Vista no encontrada';
        return;
    }

    extract($data); // extrae variables para la vista

    ob_start(); // inicia buffer
    include $viewPath; // carga vista
    $content = ob_get_contents(); // captura html
    ob_end_clean(); // limpia buffer

    include BASE_PATH . '/views/layouts/main.php'; // aplica layout principal
}

