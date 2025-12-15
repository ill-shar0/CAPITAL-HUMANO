<?php
/**
 * Helper Flash: guarda mensajes en sesión (éxito/error) y los limpia al leer.
 */
class Flash
{
    public static function success(string $message): void
    {
        $_SESSION['flash_success'] = $message; // set éxito
    }

    public static function error(string $message): void
    {
        $_SESSION['flash_error'] = $message; // set error
    }

    public static function get(): array
    {
        $messages = [
            'success' => $_SESSION['flash_success'] ?? null, // lee éxito
            'error' => $_SESSION['flash_error'] ?? null,     // lee error
        ];

        unset($_SESSION['flash_success'], $_SESSION['flash_error']); // limpia

        return $messages;
    }
}
