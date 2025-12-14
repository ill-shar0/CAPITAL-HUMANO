<?php
require_once __DIR__ . '/../config/app.php';

class PdfService
{
    public static function generateResuelto(array $data): string
    {
        $dir = BASE_PATH . '/public/uploads/resueltos';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $filename = 'resuelto_' . ($data['colab_id'] ?? uniqid()) . '_' . date('YmdHis') . '.pdf';
        $path = $dir . '/' . $filename;

        $html = '<h1>Resuelto de Vacaciones</h1>'
            . '<p>Colaborador: ' . htmlspecialchars($data['nombre'] ?? '') . '</p>'
            . '<p>Cédula: ' . htmlspecialchars($data['cedula'] ?? '') . '</p>'
            . '<p>Cargo: ' . htmlspecialchars($data['cargo'] ?? '') . '</p>'
            . '<p>Días solicitados: ' . htmlspecialchars($data['dias'] ?? '') . '</p>'
            . '<p>Periodo: ' . htmlspecialchars($data['inicio'] ?? '') . ' al ' . htmlspecialchars($data['fin'] ?? '') . '</p>'
            . '<p>Fecha de generación: ' . date('Y-m-d H:i:s') . '</p>';

        file_put_contents($path, $html);
        return '/uploads/resueltos/' . $filename;
    }
}

