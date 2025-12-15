<?php
// Helpers de sanitización básica de entrada

/**
 * Sanitiza un string: trim, elimina tags y limita longitud.
 */
function s_str(?string $value, int $maxLen = 255): string
{
    $clean = trim((string) $value);
    $clean = strip_tags($clean);
    return mb_substr($clean, 0, $maxLen);
}

/**
 * Sanitiza un email.
 */
function s_email(?string $value, int $maxLen = 150): string
{
    $clean = filter_var((string) $value, FILTER_SANITIZE_EMAIL);
    return mb_substr(trim($clean), 0, $maxLen);
}

/**
 * Sanitiza números en texto (teléfono/cédula), manteniendo dígitos y guiones.
 */
function s_numtxt(?string $value, int $maxLen = 50): string
{
    $clean = preg_replace('/[^0-9-]/', '', (string) $value);
    return mb_substr($clean, 0, $maxLen);
}

/**
 * Sanitiza fechas (YYYY-MM-DD) manteniendo solo dígitos y guiones.
 */
function s_date(?string $value, int $maxLen = 30): string
{
    return s_numtxt($value, $maxLen);
}

