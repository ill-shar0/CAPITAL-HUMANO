<?php
require_once BASE_PATH . '/config/db.php';

class Vacaciones
{
    // Obtener colaboradores con días válidos de vacaciones
    public static function colaboradoresConVacaciones(): array
{
    $db = get_db();

    $sql = "
        SELECT
            c.colab_id,
            c.colab_primer_nombre AS primer_nombre,
            c.colab_apellido_paterno AS apellido_paterno,
            c.colab_car_cargo AS car_cargo,
            COUNT(a.asis_id) AS dias_trabajados,
            FLOOR(COUNT(a.asis_id) / 11) AS dias_vacaciones_validos,
            IF(FLOOR(COUNT(a.asis_id) / 11) >= 7, 'Válido', 'No válido') AS estado_vacaciones
        FROM colaboradores c
        LEFT JOIN asistencias a 
            ON a.asis_colab_id = c.colab_id
        WHERE c.colab_estado_colaborador = 'Activo'
        GROUP BY c.colab_id
        HAVING dias_vacaciones_validos >= 7
    ";

    return $db->query($sql)->fetchAll();
}


    // Obtener datos para el resuelto
    public static function datosColaborador(string $colabId): ?array
    {
        $stmt = get_db()->prepare("
            SELECT
                colab_id,
                colab_primer_nombre,
                colab_apellido_paterno,
                colab_cedula,
                colab_car_cargo
            FROM colaboradores
            WHERE colab_id = :id
        ");

        $stmt->execute(['id' => $colabId]);
        return $stmt->fetch() ?: null;
    }

    // Guardar resuelto y actualizar conteo
    public static function guardarResuelto(
        string $colabId,
        int $diasTomados,
        string $pdfPath
    ): bool {
        $db = get_db();

        $stmt = $db->prepare("
            INSERT INTO resueltos
            (resu_colab_id, resu_dias_vacaciones, resu_pdf_path, resu_fecha_creacion)
            VALUES (:colab, :dias, :pdf, NOW())
        ");

        return $stmt->execute([
            'colab' => $colabId,
            'dias' => $diasTomados,
            'pdf' => $pdfPath
        ]);
    }
}
