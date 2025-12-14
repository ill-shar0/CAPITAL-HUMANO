<?php
/**
 * Este archivo contiene la clase Flash para manejar mensajes flash en la aplicación.
 * Los mensajes flash son mensajes temporales que se muestran al usuario después de ciertas acciones,
 * como éxito o error en operaciones.
 * Los mensajes se almacenan en la sesión y se eliminan después de ser mostrados.
 * Los implementé en controllers/asistencias.php
 * atte: Anie
 */
class Flash
{
    public static function success(string $message): void
    {
        $_SESSION['flash_success'] = $message;
    }

    public static function error(string $message): void
    {
        $_SESSION['flash_error'] = $message;
    }

    public static function get(): array
    {
        $messages = [
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null,
        ];

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return $messages;
    }
}
