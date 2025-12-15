<?php
require_once BASE_PATH . '/config/db.php';

class Vacaciones
{
    /**
     * Lista colaboradores con cálculo de días válidos y disponibles.
     * Usa asistencia como base (1 día válido cada 11 asistencias) y resta días tomados en vacaciones.
     */
    public static function colaboradoresConVacaciones(): array
    {
        try {
            $db = get_db();
            $sql = "
                SELECT
                    c.colab_id,
                    c.colab_primer_nombre AS primer_nombre,
                    c.colab_apellido_paterno AS apellido_paterno,
                    c.colab_car_cargo AS car_cargo,
                    COUNT(a.asis_id) AS dias_trabajados,
                    COALESCE(v.vac_dias_vacaciones_validos, FLOOR(COUNT(a.asis_id) / 11)) AS dias_vacaciones_validos,
                    COALESCE(v.vac_dias_vacaciones_tomados, 0) AS dias_tomados,
                    COALESCE(v.vac_dias_vacaciones_validos, FLOOR(COUNT(a.asis_id) / 11)) - COALESCE(v.vac_dias_vacaciones_tomados, 0) AS dias_disponibles,
                    CASE
                        WHEN COALESCE(v.vac_dias_vacaciones_validos, FLOOR(COUNT(a.asis_id) / 11)) - COALESCE(v.vac_dias_vacaciones_tomados, 0) >= 7
                            THEN 'Válido'
                        ELSE 'No válido'
                    END AS estado_vacaciones,
                    (
                        SELECT r2.resu_pdf_path
                        FROM resueltos r2
                        WHERE r2.resu_colab_id = c.colab_id
                        ORDER BY r2.resu_fecha_creacion DESC
                        LIMIT 1
                    ) AS resuelto_pdf_path
                FROM colaboradores c
                LEFT JOIN vacaciones v ON v.vac_colab_id = c.colab_id
                LEFT JOIN asistencias a ON a.asis_colab_id = c.colab_id
                WHERE c.colab_estado_colaborador = 'Activo'
                GROUP BY c.colab_id
                ORDER BY c.colab_primer_nombre, c.colab_apellido_paterno
            ";
            return $db->query($sql)->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Datos de colaborador + días disponibles para el resuelto.
     */
    public static function findByColabId(string $colabId): ?array
    {
        try {
            $db = get_db();
            $sql = "
                SELECT
                    c.colab_id,
                    c.colab_primer_nombre,
                    c.colab_apellido_paterno,
                    c.colab_cedula,
                    c.colab_car_cargo,
                    COUNT(a.asis_id) AS dias_trabajados,
                    COALESCE(v.vac_dias_vacaciones_validos, FLOOR(COUNT(a.asis_id) / 11)) AS vac_dias_vacaciones_validos,
                    COALESCE(v.vac_dias_vacaciones_tomados, 0) AS vac_dias_vacaciones_tomados,
                    COALESCE(v.vac_dias_vacaciones_validos, FLOOR(COUNT(a.asis_id) / 11)) - COALESCE(v.vac_dias_vacaciones_tomados, 0) AS dias_disponibles
                FROM colaboradores c
                LEFT JOIN vacaciones v ON v.vac_colab_id = c.colab_id
                LEFT JOIN asistencias a ON a.asis_colab_id = c.colab_id
                WHERE c.colab_id = :id
                GROUP BY c.colab_id
                LIMIT 1
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $colabId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Guardar resuelto y actualizar días tomados en vacaciones.
     */
    public static function guardarResuelto(
        string $colabId,
        int $diasTomados,
        string $inicio = '',
        string $fin = '',
        string $pdfPath = ''
    ): bool {
        try {
            $db = get_db();
            $db->beginTransaction(); // resuelto + actualización en vacaciones

            // Insertar resuelto
            $newId = (string)hexdec(uniqid());
            $stmtRes = $db->prepare("
                INSERT INTO resueltos (
                    resuelto_id,
                    resu_colab_id,
                    resu_dias_vacaciones,
                    resu_periodo_inicio,
                    resu_periodo_fin,
                    resu_pdf_path,
                    resu_fecha_creacion,
                    resu_ultima_actualizacion
                ) VALUES (
                    :id, :colab, :dias, :inicio, :fin, :pdf, :fecha, :fecha
                )
            ");
            $now = date('Y-m-d H:i:s');
            $stmtRes->execute([
                'id' => $newId,
                'colab' => $colabId,
                'dias' => $diasTomados,
                'inicio' => $inicio,
                'fin' => $fin,
                'pdf' => $pdfPath,
                'fecha' => $now,
            ]);

            // Leer o crear registro de vacaciones
            $stmtVac = $db->prepare("SELECT vac_id, vac_dias_vacaciones_validos, vac_dias_vacaciones_tomados FROM vacaciones WHERE vac_colab_id = :id LIMIT 1");
            $stmtVac->execute(['id' => $colabId]);
            $vac = $stmtVac->fetch();

            if (!$vac) {
                // Calcular válidos desde asistencias
                $diasValidos = self::calcularDiasValidos($colabId);
                $stmtInsertVac = $db->prepare("
                    INSERT INTO vacaciones (
                        vac_id,
                        vac_colab_id,
                        vac_dias_trabajados,
                        vac_dias_vacaciones_validos,
                        vac_dias_vacaciones_tomados,
                        vac_estado_vacaciones,
                        vac_fecha_creacion,
                        vac_ultima_actualizacion
                    ) VALUES (
                        :id, :colab, :trab, :validos, :tomados, :estado, :fecha, :fecha
                    )
                ");
                $stmtInsertVac->execute([
                    'id' => (string)hexdec(uniqid()),
                    'colab' => $colabId,
                    'trab' => 0,
                    'validos' => $diasValidos,
                    'tomados' => $diasTomados,
                    'estado' => $diasValidos >= 7 ? 'Válido' : 'No válido',
                    'fecha' => $now,
                ]);
            } else {
                $validos = (int)$vac['vac_dias_vacaciones_validos']; // saldo
                $tomados = (int)$vac['vac_dias_vacaciones_tomados']; // usados
                $nuevoTomado = $tomados + $diasTomados; // nuevo total
                $estado = ($validos - $nuevoTomado) >= 7 ? 'Válido' : 'No válido'; // recalcular estado

                $stmtUpdateVac = $db->prepare("
                    UPDATE vacaciones
                    SET vac_dias_vacaciones_tomados = :tomados,
                        vac_estado_vacaciones = :estado,
                        vac_ultima_actualizacion = :fecha
                    WHERE vac_id = :id
                ");
                $stmtUpdateVac->execute([
                    'tomados' => $nuevoTomado,
                    'estado' => $estado,
                    'fecha' => $now,
                    'id' => $vac['vac_id'],
                ]);
            }

            $db->commit();
            return true;
        } catch (Throwable $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack(); // revertir si falla
            }
            return false;
        }
    }

    /**
     * Calcula días válidos desde asistencias (1 por cada 11).
     */
    private static function calcularDiasValidos(string $colabId): int
    {
        try {
            $db = get_db();
            $stmt = $db->prepare("SELECT COUNT(*) AS total FROM asistencias WHERE asis_colab_id = :id");
            $stmt->execute(['id' => $colabId]);
            $row = $stmt->fetch();
            $total = (int)($row['total'] ?? 0);
            return (int)floor($total / 11);
        } catch (Throwable $e) {
            return 0;
        }
    }
}
