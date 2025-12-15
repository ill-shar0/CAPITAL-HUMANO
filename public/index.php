<?php
ini_set('display_errors', 1);            // mostrar errores en dev
ini_set('display_startup_errors', 1);    // errores de arranque
error_reporting(E_ALL);                  // nivel máximo

require_once __DIR__ . '/../config/app.php'; // bootstrap app (paths/layout)
require_once __DIR__ . '/../config/db.php';  // conexión PDO
require_once __DIR__ . '/../middleware/auth.php'; // helpers de auth

require_login(); // bloquea acceso si no hay sesión
require __DIR__ . '/../routes.php'; // despacha a la ruta solicitada

