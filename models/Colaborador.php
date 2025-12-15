<?php
require_once BASE_PATH . '/config/db.php';

class Colaborador
{
    public static function create(array $data): ?string
    {
        try {
            $db = get_db();
            $sql = 'INSERT INTO colaboradores (
                        `colab_primer_nombre`, segundo_nombre, colab_apellido_paterno, colab_apellido_materno,
                        colab_sexo, colab_cedula, colab_fecha_nac, colab_correo, colab_telefono, colab_celular,
                        colab_direccion, colab_foto_perfil, colab_car_sueldo, colab_car_cargo,
                        colab_estado_colaborador, colab_fecha_creacion, colab_ultima_actualizacion
                    ) VALUES (
                        :primer_nombre, :segundo_nombre, :apellido_paterno, :apellido_materno,
                        :sexo, :cedula, :fecha_nac, :correo, :telefono, :celular,
                        :direccion, :foto, :sueldo, :cargo,
                        :estado, :fecha, :fecha
                    )';
            $stmt = $db->prepare($sql);
            $now = date('Y-m-d H:i:s');
            $stmt->execute([
                'primer_nombre' => $data['primer_nombre'] ?? '',
                'segundo_nombre' => $data['segundo_nombre'] ?? '',
                'apellido_paterno' => $data['apellido_paterno'] ?? '',
                'apellido_materno' => $data['apellido_materno'] ?? '',
                'sexo' => $data['sexo'] ?? '',
                'cedula' => $data['cedula'] ?? '',
                'fecha_nac' => $data['fecha_nac'] ?? '',
                'correo' => $data['correo'] ?? '',
                'telefono' => $data['telefono'] ?? '',
                'celular' => $data['celular'] ?? '',
                'direccion' => $data['direccion'] ?? '',
                'foto' => $data['foto_perfil'] ?? '',
                'sueldo' => $data['car_sueldo'] ?? '',
                'cargo' => $data['car_cargo'] ?? '',
                'estado' => $data['estado_colaborador'] ?? 'Activo',
                'fecha' => $now,
            ]);
            return $db->lastInsertId();
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function updateColab(string $id, array $data): bool
    {
        try {
            $db = get_db();
            $sql = 'UPDATE colaboradores SET
                        `colab_primer_nombre` = :primer_nombre,
                        segundo_nombre = :segundo_nombre,
                        colab_apellido_paterno = :apellido_paterno,
                        colab_apellido_materno = :apellido_materno,
                        colab_sexo = :sexo,
                        colab_cedula = :cedula,
                        colab_fecha_nac = :fecha_nac,
                        colab_correo = :correo,
                        colab_telefono = :telefono,
                        colab_celular = :celular,
                        colab_direccion = :direccion,
                        colab_foto_perfil = :foto,
                        colab_car_sueldo = :sueldo,
                        colab_car_cargo = :cargo,
                        colab_estado_colaborador = :estado,
                        colab_ultima_actualizacion = :fecha
                    WHERE colab_id = :id';
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'primer_nombre' => $data['primer_nombre'] ?? '',
                'segundo_nombre' => $data['segundo_nombre'] ?? '',
                'apellido_paterno' => $data['apellido_paterno'] ?? '',
                'apellido_materno' => $data['apellido_materno'] ?? '',
                'sexo' => $data['sexo'] ?? '',
                'cedula' => $data['cedula'] ?? '',
                'fecha_nac' => $data['fecha_nac'] ?? '',
                'correo' => $data['correo'] ?? '',
                'telefono' => $data['telefono'] ?? '',
                'celular' => $data['celular'] ?? '',
                'direccion' => $data['direccion'] ?? '',
                'foto' => $data['foto_perfil'] ?? '',
                'sueldo' => $data['car_sueldo'] ?? '',
                'cargo' => $data['car_cargo'] ?? '',
                'estado' => $data['estado_colaborador'] ?? 'Activo',
                'fecha' => date('Y-m-d H:i:s'),
                'id' => $id,
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function moveToHistorial(string $colabId): bool
    {
        try {
            $db = get_db();
            $db->beginTransaction();

            $colab = self::find($colabId);
            if (!$colab) {
                $db->rollBack();
                return false;
            }

            $sqlHist = 'INSERT INTO historial_colaboradores (
                his_col_id, his_col_primer_nombre, his_col_segundo_nombre, his_col_apellido_paterno,
                his_col_apellido_materno, his_col_sexo, his_col_cedula, his_col_fecha_nac,
                his_col_correo, his_col_telefono, his_col_celular, his_col_direccion,
                his_col_foto_perfil, his_col_car_sueldo, his_col_car_cargo, his_col_estado_colaborador,
                his_col_fecha_creacion, his_col_ultima_actualizacion, his_col_fecha_salida
            ) VALUES (
                :id, :primer_nombre, :segundo_nombre, :apellido_paterno,
                :apellido_materno, :sexo, :cedula, :fecha_nac,
                :correo, :telefono, :celular, :direccion,
                :foto, :sueldo, :cargo, :estado,
                :creacion, :actualizacion, :salida
            )';
            $stmtHist = $db->prepare($sqlHist);
            $stmtHist->execute([
                'id' => $colab['colab_id'],
                'primer_nombre' => $colab['primer_nombre'],
                'segundo_nombre' => $colab['segundo_nombre'],
                'apellido_paterno' => $colab['apellido_paterno'],
                'apellido_materno' => $colab['apellido_materno'],
                'sexo' => $colab['sexo'],
                'cedula' => $colab['cedula'],
                'fecha_nac' => $colab['fecha_nac'],
                'correo' => $colab['correo'],
                'telefono' => $colab['telefono'] ?? '',
                'celular' => $colab['celular'] ?? '',
                'direccion' => $colab['direccion'] ?? '',
                'foto' => $colab['foto_perfil'] ?? '',
                'sueldo' => $colab['car_sueldo'] ?? '',
                'cargo' => $colab['car_cargo'] ?? '',
                'estado' => $colab['estado_colaborador'] ?? '',
                'creacion' => $colab['fecha_creacion'] ?? '',
                'actualizacion' => $colab['ultima_actualizacion'] ?? '',
                'salida' => date('Y-m-d H:i:s'),
            ]);

            // Desactivar asignaciones
            $sqlAssign = 'UPDATE colaborador_cargo
                          SET col_carg_activo = "0",
                              col_carg_ultima_actualizacion = :fecha
                          WHERE col_carg_id = :colab';
            $stmtAssign = $db->prepare($sqlAssign);
            $stmtAssign->execute([
                'fecha' => date('Y-m-d H:i:s'),
                'colab' => $colabId,
            ]);

            // Eliminar colaborador
            $stmtDel = $db->prepare('DELETE FROM colaboradores WHERE colab_id = :id');
            $stmtDel->execute(['id' => $colabId]);

            $db->commit();
            return true;
        } catch (Throwable $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            return false;
        }
    }
    public static function all(): array
    {
        try {
            $db = get_db();
            $stmt = $db->query('SELECT 
                    colab_id,
                    `colab_primer_nombre` AS primer_nombre,
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
                    `colab_primer_nombre` AS primer_nombre,
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
                        c.`colab_primer_nombre` AS primer_nombre,
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

    public static function filtrarParaReporte(array $filtros, int $limit, int $offset): array
{
    try {
        $db = get_db();
        $condiciones[] = "colab_estado_colaborador = 'Activo'";
        $params = [];

        if (!empty($filtros['sexo'])) {
            $condiciones[] = 'colab_sexo = :sexo';
            $params['sexo'] = $filtros['sexo'];
        }

        if (!empty($filtros['nombre'])) {
            $condiciones[] = '(`colab_primer_nombre` LIKE :nombre OR segundo_nombre LIKE :nombre)';
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
            $params['edad_min'] = (int)$filtros['edad_min'];
        }

        if (!empty($filtros['edad_max'])) {
            $condiciones[] = 'TIMESTAMPDIFF(YEAR, STR_TO_DATE(colab_fecha_nac, "%Y-%m-%d"), CURDATE()) <= :edad_max';
            $params['edad_max'] = (int)$filtros['edad_max'];
        }

        $where = $condiciones ? ('WHERE ' . implode(' AND ', $condiciones)) : '';

        $sql = "SELECT 
                    colab_id,
                    `colab_primer_nombre` AS primer_nombre,
                    colab_apellido_paterno AS apellido_paterno,
                    colab_sexo AS sexo,
                    colab_car_sueldo AS car_sueldo
                FROM colaboradores
                {$where}
                ORDER BY primer_nombre, apellido_paterno
                LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

public static function contarParaReporte(array $filtros): int
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
            $condiciones[] = '(`colab_primer_nombre` LIKE :nombre OR segundo_nombre LIKE :nombre)';
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
            $params['edad_min'] = (int)$filtros['edad_min'];
        }

        if (!empty($filtros['edad_max'])) {
            $condiciones[] = 'TIMESTAMPDIFF(YEAR, STR_TO_DATE(colab_fecha_nac, "%Y-%m-%d"), CURDATE()) <= :edad_max';
            $params['edad_max'] = (int)$filtros['edad_max'];
        }

        $where = $condiciones ? ('WHERE ' . implode(' AND ', $condiciones)) : '';

        $sql = "SELECT COUNT(*) FROM colaboradores {$where}";
        $stmt = $db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
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

