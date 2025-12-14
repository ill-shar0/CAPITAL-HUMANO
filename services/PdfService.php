<?php
require_once BASE_PATH . '/vendor/autoload.php';

// Usar Dompdf
// DomPDF es una librería gratuita de PHP que funciona como un conversor de HTML a PDF,
// permitiendo a los desarrolladores generar documentos PDF a partir de código HTML y CSS,
// renderizando estilos y atributos de forma similar a un navegador web.

use Dompdf\Dompdf;

class PdfService
{
    public static function generateResuelto(array $data): string
    {
        $dompdf = new Dompdf();

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

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        $dir = BASE_PATH . '/public/uploads/resueltos';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'resuelto_' . $data['colab_id'] . '_' . time() . '.pdf';
        $path = $dir . '/' . $filename;

        file_put_contents($path, $dompdf->output());

        return '/uploads/resueltos/' . $filename;
    }
}
