<?php
require_once BASE_PATH . '/config/db.php';

class Cargo
{
    // Crea un cargo con ID generado (tipo UUID_SHORT)
    public static function create(string $nombre, string $departamento, string $sueldo, string $ocupacion): ?string
    {
        // UUID_SHORT no es recuperable con lastInsertId() en PDO; generamos el ID antes.
        try {
            $db = get_db();
            $newId = (string)hexdec(uniqid()); // similar a UUID_SHORT()
            $sql = 'INSERT INTO cargos (
                        cargo_id,
                        carg_nombre_cargo,
                        carg_departamento_cargo,
                        carg_sueldo_cargo,
                        carg_ocupacion,
                        carg_fecha_creacion,
                        carg_ultima_actualizacion
                    ) VALUES (
                        :id, :nombre, :depto, :sueldo, :ocupacion, :fecha, :fecha
                    )';
            $stmt = $db->prepare($sql);
            $now = date('Y-m-d H:i:s');
            $stmt->execute([
                'id' => $newId,
                'nombre' => $nombre,
                'depto' => $departamento,
                'sueldo' => $sueldo,
                'ocupacion' => $ocupacion,
                'fecha' => $now,
            ]);
            return $newId;
        } catch (Throwable $e) {
            return null;
        }
    }

    // Actualiza datos básicos de un cargo
    public static function updateCargo(string $cargoId, string $nombre, string $departamento, string $sueldo, string $ocupacion): bool
    {
        try {
            $db = get_db();
            $sql = 'UPDATE cargos
                    SET carg_nombre_cargo = :nombre,
                        carg_departamento_cargo = :depto,
                        carg_sueldo_cargo = :sueldo,
                        carg_ocupacion = :ocupacion,
                        carg_ultima_actualizacion = :fecha
                    WHERE cargo_id = :id';
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'nombre' => $nombre,
                'depto' => $departamento,
                'sueldo' => $sueldo,
                'ocupacion' => $ocupacion,
                'fecha' => date('Y-m-d H:i:s'),
                'id' => $cargoId,
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }

    // Borra cargo y desactiva asignaciones relacionadas
    public static function deleteCargo(string $cargoId): bool
    {
        try {
            $db = get_db();
            $db->beginTransaction();

            // Desactivar asignaciones
            $sqlAssign = 'UPDATE colaborador_cargo
                          SET col_carg_activo = "0",
                              col_carg_ultima_actualizacion = :fecha
                          WHERE cal_carg_id = :id';
            $stmtAssign = $db->prepare($sqlAssign);
            $stmtAssign->execute([
                'fecha' => date('Y-m-d H:i:s'),
                'id' => $cargoId,
            ]);

            // Limpiar referencia en colaboradores
            $sqlColab = 'UPDATE colaboradores c
                         INNER JOIN colaborador_cargo cc ON cc.col_carg_id = c.colab_id AND cc.cal_carg_id = :id
                         SET c.colab_car_cargo = NULL,
                             c.colab_car_sueldo = NULL,
                             c.colab_ultima_actualizacion = :fecha';
            $stmtColab = $db->prepare($sqlColab);
            $stmtColab->execute([
                'id' => $cargoId,
                'fecha' => date('Y-m-d H:i:s'),
            ]);

            // Eliminar cargo
            $stmt = $db->prepare('DELETE FROM cargos WHERE cargo_id = :id');
            $stmt->execute(['id' => $cargoId]);

            $db->commit();
            return true;
        } catch (Throwable $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            return false;
        }
    }

    // Lista todos los cargos
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

    // Busca un cargo por ID
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

    // Cargo activo de un colaborador
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

    // Historial de cargos de un colaborador
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

    // Asigna cargo (desactiva previos y actualiza datos rápidos)
    public static function assignToColaborador(string $colabId, string $cargoId, string $periodo): bool
    {
        try {
            $db = get_db();
            $db->beginTransaction();

            // Desactivar asignaciones previas si las hay (solo uno activo)
            $sqlDeactivate = 'UPDATE colaborador_cargo
                              SET col_carg_activo = "0",
                                  col_carg_ultima_actualizacion = :fecha
                              WHERE col_carg_id = :colab';
            $stmtDeactivate = $db->prepare($sqlDeactivate);
            $stmtDeactivate->execute([
                'fecha' => date('Y-m-d H:i:s'),
                'colab' => $colabId,
            ]);

            $sql = 'INSERT INTO colaborador_cargo (col_carg_id, cal_carg_id, col_carg_periodo, col_carg_activo, col_carg_fecha_creacion, col_carg_ultima_actualizacion)
                    VALUES (:colab, :cargo, :periodo, "1", :fecha, :fecha)';
            $stmt = $db->prepare($sql);
            $now = date('Y-m-d H:i:s');
            $stmt->execute([
                'colab' => $colabId,
                'cargo' => $cargoId,
                'periodo' => $periodo,
                'fecha' => $now,
            ]);

            // Actualizar datos rápidos en colaboradores (cargo/sueldo)
            $cargo = self::find($cargoId);
            if ($cargo) {
                $sqlColab = 'UPDATE colaboradores
                             SET colab_car_cargo = :cargoNombre,
                                 colab_car_sueldo = :sueldo,
                                 colab_ultima_actualizacion = :fecha
                             WHERE colab_id = :colab';
                $stmtColab = $db->prepare($sqlColab);
                $stmtColab->execute([
                    'cargoNombre' => $cargo['nombre_cargo'],
                    'sueldo' => $cargo['sueldo_cargo'],
                    'fecha' => $now,
                    'colab' => $colabId,
                ]);
            }

            $db->commit();
            return true;
        } catch (Throwable $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            return false;
        }
    }

    // Desactiva asignación puntual
    public static function removeAssignment(string $colabId, string $cargoId): bool
    {
        try {
            $db = get_db();
            $sql = 'UPDATE colaborador_cargo
                    SET col_carg_activo = "0",
                        col_carg_ultima_actualizacion = :fecha
                    WHERE col_carg_id = :colab AND cal_carg_id = :cargo';
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'fecha' => date('Y-m-d H:i:s'),
                'colab' => $colabId,
                'cargo' => $cargoId,
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }
}

