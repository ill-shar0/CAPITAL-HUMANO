<?php
require_once BASE_PATH . '/config/db.php';

class Cargo
{
    public static function all(): array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        cargo_id,
                        carg_nombre_cargo AS nombre_cargo,
                        carg_departamento_cargo AS departamento_cargo,
                        carg_sueldo_cargo AS sueldo_cargo,
                        carg_ocupacion AS ocupacion,
                        carg_fecha_creacion AS fecha_creacion,
                        carg_ultima_actualizacion AS ultima_actualizacion
                    FROM cargos
                    ORDER BY nombre_cargo';
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function find(string $id): ?array
    {
        try {
            $db = get_db();
            $stmt = $db->prepare('SELECT 
                    cargo_id,
                    carg_nombre_cargo AS nombre_cargo,
                    carg_departamento_cargo AS departamento_cargo,
                    carg_sueldo_cargo AS sueldo_cargo,
                    carg_ocupacion AS ocupacion,
                    carg_fecha_creacion AS fecha_creacion,
                    carg_ultima_actualizacion AS ultima_actualizacion
                FROM cargos
                WHERE cargo_id = :id
                LIMIT 1');
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function findActivoByColaborador(string $colabId): ?array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        c.cargo_id,
                        c.carg_nombre_cargo AS nombre_cargo,
                        c.carg_departamento_cargo AS departamento_cargo,
                        c.carg_sueldo_cargo AS sueldo_cargo,
                        c.carg_ocupacion AS ocupacion,
                        cc.col_carg_periodo AS periodo
                    FROM cargos c
                    INNER JOIN colaborador_cargo cc ON cc.cal_carg_id = c.cargo_id
                    WHERE cc.col_carg_id = :colab AND cc.col_carg_activo = "1"
                    LIMIT 1';
            $stmt = $db->prepare($sql);
            $stmt->execute(['colab' => $colabId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function historialPorColaborador(string $colabId): array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        c.carg_nombre_cargo AS nombre_cargo,
                        c.carg_departamento_cargo AS departamento_cargo,
                        c.carg_sueldo_cargo AS sueldo_cargo,
                        cc.col_carg_periodo AS periodo,
                        cc.col_carg_activo AS activo,
                        cc.col_carg_fecha_creacion AS fecha_creacion
                    FROM cargos c
                    INNER JOIN colaborador_cargo cc ON cc.cal_carg_id = c.cargo_id
                    WHERE cc.col_carg_id = :colab
                    ORDER BY cc.col_carg_fecha_creacion DESC';
            $stmt = $db->prepare($sql);
            $stmt->execute(['colab' => $colabId]);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }
}

