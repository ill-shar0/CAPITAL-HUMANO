<?php

/**
 * Redirige usando BASE_URL. Si no es URL absoluta ni empieza con slash,
 * se asume como page de index.php?page=...
 */
function redirect(string $url): void
{
    // Absoluto o ya con slash: respeta tal cual
    if (preg_match('#^https?://#i', $url) || str_starts_with($url, '/')) {
        header('Location: ' . $url);
        exit;
    }

    // Caso de page
    $dest = BASE_URL . '/index.php?page=' . urlencode($url);
    header('Location: ' . $dest);
    exit;
}