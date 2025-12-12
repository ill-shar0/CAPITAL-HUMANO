<?php
require_once BASE_PATH . '/config/db.php';

class Vacacion
{
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

