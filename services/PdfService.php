<?php
require_once BASE_PATH . '/vendor/autoload.php'; // carga Dompdf

use Dompdf\Dompdf;

class PdfService
{
    // Genera PDF de resuelto usando Dompdf y devuelve ruta relativa
    public static function generateResuelto(array $data): string
    {
        $dompdf = new Dompdf();

        // Plantilla HTML simple (se puede reemplazar por una más formal)
        $html = '
        <h2 style="text-align:center;">RESUELTO DE VACACIONES</h2>

        <p>Por medio del presente se concede vacaciones al colaborador:</p>

        <p><strong>Nombre:</strong> ' . htmlspecialchars($data['nombre']) . '</p>
        <p><strong>Cédula:</strong> ' . htmlspecialchars($data['cedula']) . '</p>
        <p><strong>Cargo:</strong> ' . htmlspecialchars($data['cargo']) . '</p>

        <p><strong>Días otorgados:</strong> ' . htmlspecialchars($data['dias']) . '</p>
        <p><strong>Periodo:</strong> ' . htmlspecialchars($data['inicio']) .
        ' al ' . htmlspecialchars($data['fin']) . '</p>

        <p>Fecha de emisión: ' . date('d/m/Y') . '</p>

        <br><br>
        <p>__________________________</p>
        <p>Recursos Humanos</p>
        ';

        $dompdf->loadHtml($html); // carga HTML
        $dompdf->setPaper('A4'); // tamaño
        $dompdf->render(); // renderiza PDF

        $dir = BASE_PATH . '/public/uploads/resueltos';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true); // crea carpeta si no existe
        }

        $filename = 'resuelto_' . $data['colab_id'] . '_' . time() . '.pdf'; // nombre único
        $path = $dir . '/' . $filename;

        file_put_contents($path, $dompdf->output()); // guarda en disco

        return '/uploads/resueltos/' . $filename; // ruta pública relativa
    }
}
