<?php
// Helpers de validación básica

/**
 * Valida campos requeridos. Devuelve arreglo de errores.
 */
function v_required(array $data, array $fields): array
{
    $errors = [];
    foreach ($fields as $field => $label) {
        $val = $data[$field] ?? '';
        if ($val === '' || $val === null) {
            $errors[] = "{$label} es obligatorio.";
        }
    }
    return $errors;
}

/**
 * Valida pertenencia a lista.
 */
function v_in(string $value, array $allowed, string $label): array
{
    return in_array($value, $allowed, true) ? [] : ["{$label} es inválido."];
}

/**
 * Valida longitud máxima.
 */
function v_maxlen(string $value, int $max, string $label): array
{
    return (mb_strlen($value) <= $max) ? [] : ["{$label} excede {$max} caracteres."];
}

/**
 * Valida solo letras (y espacios opcionales). Ignora si está vacío.
 */
function v_alpha(string $value, string $label, bool $allowSpaces = true): array
{
    if ($value === '') return [];
    $pattern = $allowSpaces ? '/^[\p{L}\s]+$/u' : '/^[\p{L}]+$/u';
    return preg_match($pattern, $value) ? [] : ["{$label} solo debe tener letras" . ($allowSpaces ? " y espacios." : ".")];
}

/**
 * Valida numérico entero (solo dígitos). Ignora si está vacío.
 */
function v_numeric(string $value, string $label): array
{
    if ($value === '') return [];
    return ctype_digit($value) ? [] : ["{$label} debe ser numérico."];
}

/**
 * Valida email. Ignora si está vacío.
 */
function v_email(string $value, string $label): array
{
    if ($value === '') return [];
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? [] : ["{$label} no es un correo válido."];
}

/**
 * Valida contra patrón personalizado. Ignora si está vacío.
 */
function v_pattern(string $value, string $label, string $pattern, string $hint): array
{
    if ($value === '') return [];
    return preg_match($pattern, $value) ? [] : ["{$label} debe cumplir: {$hint}"];
}

