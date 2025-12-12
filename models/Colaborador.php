<?php
require_once BASE_PATH . '/config/db.php';

class Colaborador
{
    public static function all(): array
    {
        try {
            $db = get_db();
            $stmt = $db->query('SELECT 
                    colab_id,
                    `colab_ primer_nombre` AS primer_nombre,
                    segundo_nombre,
                    colab_apellido_paterno AS apellido_paterno,
                    colab_apellido_materno AS apellido_materno,
                    colab_sexo AS sexo,
                    colab_cedula AS cedula,
                    colab_correo AS correo,
                    colab_estado_colaborador AS estado_colaborador,
                    colab_fecha_creacion AS fecha_creacion,
                    colab_ultima_actualizacion AS ultima_actualizacion,
                    colab_car_sueldo AS car_sueldo,
                    colab_car_cargo AS car_cargo
                FROM colaboradores
                ORDER BY primer_nombre, apellido_paterno');
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
                    colab_id,
                    `colab_ primer_nombre` AS primer_nombre,
                    segundo_nombre,
                    colab_apellido_paterno AS apellido_paterno,
                    colab_apellido_materno AS apellido_materno,
                    colab_sexo AS sexo,
                    colab_cedula AS cedula,
                    colab_fecha_nac AS fecha_nac,
                    colab_correo AS correo,
                    colab_telefono AS telefono,
                    colab_celular AS celular,
                    colab_direccion AS direccion,
                    colab_foto_perfil AS foto_perfil,
                    colab_car_sueldo AS car_sueldo,
                    colab_car_cargo AS car_cargo,
                    colab_estado_colaborador AS estado_colaborador,
                    colab_fecha_creacion AS fecha_creacion,
                    colab_ultima_actualizacion AS ultima_actualizacion
                FROM colaboradores
                WHERE colab_id = :id
                LIMIT 1');
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function porCargo(string $cargoId): array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        c.colab_id,
                        c.`colab_ primer_nombre` AS primer_nombre,
                        c.colab_apellido_paterno AS apellido_paterno,
                        cc.col_carg_periodo AS periodo
                    FROM colaboradores c
                    INNER JOIN colaborador_cargo cc ON cc.col_carg_id = c.colab_id
                    WHERE cc.cal_carg_id = :cargo AND cc.col_carg_activo = "1"';
            $stmt = $db->prepare($sql);
            $stmt->execute(['cargo' => $cargoId]);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function filtrarParaReporte(array $filtros): array
    {
        try {
            $db = get_db();
            $condiciones = [];
            $params = [];

            if (!empty($filtros['sexo'])) {
                $condiciones[] = 'colab_sexo = :sexo';
                $params['sexo'] = $filtros['sexo'];
            }
            if (!empty($filtros['nombre'])) {
                $condiciones[] = '(`colab_ primer_nombre` LIKE :nombre OR segundo_nombre LIKE :nombre)';
                $params['nombre'] = '%' . $filtros['nombre'] . '%';
            }
            if (!empty($filtros['apellido'])) {
                $condiciones[] = '(colab_apellido_paterno LIKE :apellido OR colab_apellido_materno LIKE :apellido)';
                $params['apellido'] = '%' . $filtros['apellido'] . '%';
            }
            if (!empty($filtros['salario_min'])) {
                $condiciones[] = 'colab_car_sueldo >= :salario_min';
                $params['salario_min'] = $filtros['salario_min'];
            }
            if (!empty($filtros['edad_min'])) {
                $condiciones[] = 'TIMESTAMPDIFF(YEAR, STR_TO_DATE(colab_fecha_nac, "%Y-%m-%d"), CURDATE()) >= :edad_min';
                $params['edad_min'] = $filtros['edad_min'];
            }
            if (!empty($filtros['edad_max'])) {
                $condiciones[] = 'TIMESTAMPDIFF(YEAR, STR_TO_DATE(colab_fecha_nac, "%Y-%m-%d"), CURDATE()) <= :edad_max';
                $params['edad_max'] = $filtros['edad_max'];
            }

            $where = $condiciones ? ('WHERE ' . implode(' AND ', $condiciones)) : '';

            $sql = "SELECT 
                        colab_id,
                        `colab_ primer_nombre` AS primer_nombre,
                        segundo_nombre,
                        colab_apellido_paterno AS apellido_paterno,
                        colab_apellido_materno AS apellido_materno,
                        colab_sexo AS sexo,
                        colab_car_sueldo AS car_sueldo 
                    FROM colaboradores {$where}
                    ORDER BY primer_nombre, apellido_paterno";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function estadisticas(): array
    {
        try {
            $db = get_db();
            $porSexo = $db->query('SELECT colab_sexo AS sexo, COUNT(*) as total FROM colaboradores GROUP BY colab_sexo')->fetchAll();
            $porDireccion = $db->query('SELECT colab_direccion AS direccion, COUNT(*) as total FROM colaboradores GROUP BY colab_direccion')->fetchAll();
            $porRangoEdad = $db->query('
                SELECT
                    CASE
                        WHEN edad BETWEEN 18 AND 24 THEN "18-24"
                        WHEN edad BETWEEN 25 AND 30 THEN "25-30"
                        WHEN edad BETWEEN 31 AND 40 THEN "31-40"
                        WHEN edad BETWEEN 41 AND 50 THEN "41-50"
                        ELSE "51+"
                    END as rango,
                    COUNT(*) as total
                FROM (
                    SELECT TIMESTAMPDIFF(YEAR, STR_TO_DATE(colab_fecha_nac, "%Y-%m-%d"), CURDATE()) as edad
                    FROM colaboradores
                ) t
                GROUP BY rango
            ')->fetchAll();

            return [
                'por_sexo' => $porSexo,
                'por_direccion' => $porDireccion,
                'por_rango_edad' => $porRangoEdad,
            ];
        } catch (Throwable $e) {
            return [
                'por_sexo' => [],
                'por_direccion' => [],
                'por_rango_edad' => [],
            ];
        }
    }
}

