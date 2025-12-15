<?php

/**
 * Redirige usando BASE_URL. Si se pasa un valor sin slash ni http(s),
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