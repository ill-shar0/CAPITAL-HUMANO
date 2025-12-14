<?php
require_once BASE_PATH . '/config/db.php';

class Vacacion
{
    public static function create(string $colabId, string $diasTrab, string $diasValidos, string $estado, string $diasTomados = '0'): ?string
    {
        try {
            $db = get_db();
            $sql = 'INSERT INTO vacaciones (vac_colab_id, vac_dias_trabajados, vac_dias_vacaciones_validos, vac_estado_vacaciones, vac_dias_vacaciones_tomados, vac_fecha_creacion, vac_ultima_actualizacion)
                    VALUES (:colab, :trab, :validos, :estado, :tomados, :fecha, :fecha)';
            $stmt = $db->prepare($sql);
            $now = date('Y-m-d H:i:s');
            $stmt->execute([
                'colab' => $colabId,
                'trab' => $diasTrab,
                'validos' => $diasValidos,
                'estado' => $estado,
                'tomados' => $diasTomados,
                'fecha' => $now,
            ]);
            return $db->lastInsertId();
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function updateVacacion(string $vacId, string $diasTrab, string $diasValidos, string $estado, string $diasTomados = '0'): bool
    {
        try {
            $db = get_db();
            $sql = 'UPDATE vacaciones
                    SET vac_dias_trabajados = :trab,
                        vac_dias_vacaciones_validos = :validos,
                        vac_estado_vacaciones = :estado,
                        vac_dias_vacaciones_tomados = :tomados,
                        vac_ultima_actualizacion = :fecha
                    WHERE vac_id = :id';
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'trab' => $diasTrab,
                'validos' => $diasValidos,
                'estado' => $estado,
                'tomados' => $diasTomados,
                'fecha' => date('Y-m-d H:i:s'),
                'id' => $vacId,
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function deleteVacacion(string $vacId): bool
    {
        try {
            $db = get_db();
            $stmt = $db->prepare('DELETE FROM vacaciones WHERE vac_id = :id');
            return $stmt->execute(['id' => $vacId]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function resumen(): array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        v.vac_id,
                        v.vac_colab_id AS colab_id,
                        v.vac_dias_trabajados AS dias_trabajados,
                        v.vac_dias_vacaciones_validos AS dias_vacaciones_validos,
                        v.vac_estado_vacaciones AS estado_vacaciones,
                        c.`colab_ primer_nombre` AS primer_nombre,
                        c.colab_apellido_paterno AS apellido_paterno,
                        c.colab_car_cargo AS car_cargo
                    FROM vacaciones v
                    INNER JOIN colaboradores c ON c.colab_id = v.vac_colab_id
                    ORDER BY v.vac_dias_vacaciones_validos DESC';
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }
}

