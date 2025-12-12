<?php
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!current_user()) {
        header('Location: login.php');
        exit;
    }
}

function require_roles(array $roles): void
{
    require_login();
    $user = current_user();
    if (!$user || !in_array($user['rol'], $roles, true)) {
        http_response_code(403);
        echo 'Acceso denegado';
        exit;
    }
}

