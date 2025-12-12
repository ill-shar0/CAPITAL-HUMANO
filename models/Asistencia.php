<?php
require_once BASE_PATH . '/config/db.php';

class Asistencia
{
    public static function todas(): array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        asis_id,
                        asis_colab_id AS colab_id,
                        asis_fecha AS fecha,
                        asis_hora_entrada AS hora_entrada,
                        asis_hora_salida AS hora_salida,
                        asis_fecha_creacion AS fecha_creacion,
                        asis_ultima_actualizacion AS ultima_actualizacion
                    FROM asistencias
                    ORDER BY asis_fecha DESC, asis_hora_entrada DESC';
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function porColaborador(string $colabId): array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        asis_fecha AS fecha,
                        asis_hora_entrada AS hora_entrada,
                        asis_hora_salida AS hora_salida
                    FROM asistencias
                    WHERE asis_colab_id = :colab
                    ORDER BY asis_fecha DESC';
            $stmt = $db->prepare($sql);
            $stmt->execute(['colab' => $colabId]);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }
}

