<?php
function current_user(): ?array
{
    return $_SESSION['user'] ?? null; // devuelve usuario en sesión o null
}

function require_login(): void
{
    if (!current_user()) { // si no hay sesión
        header('Location: login.php'); // redirige a login
        exit;
    }
}

function require_roles(array $roles): void
{
    require_login(); // exige sesión
    $user = current_user();
    if (!$user || !in_array($user['rol'], $roles, true)) { // valida rol
        http_response_code(403);
        echo 'Acceso denegado';
        exit;
    }
}

