<?php
require_once __DIR__ . '/../config/app.php'; // iniciar sesión/contexto

// Limpia variables de sesión
$_SESSION = [];
if (ini_get('session.use_cookies')) { // invalida cookie si aplica
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000,
    $params['path'], $params['domain'], $params['secure'], $params['httponly']
  );
}
session_destroy(); // destruye sesión en servidor

header('Location: login.php'); // regreso a login
exit;
