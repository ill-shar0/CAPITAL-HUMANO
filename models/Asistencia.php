<?php
require_once BASE_PATH . '/config/db.php';

class Asistencia
{
    public static function create(string $colabId, string $fecha, string $horaEntrada, ?string $horaSalida = null): ?string
    {
        try {
            $db = get_db();
            $sql = 'INSERT INTO asistencias (asis_colab_id, asis_fecha, asis_hora_entrada, asis_hora_salida, asis_fecha_creacion, asis_ultima_actualizacion)
                    VALUES (:colab, :fecha, :entrada, :salida, :fecha_now, :fecha_now)';
            $stmt = $db->prepare($sql);
            $now = date('Y-m-d H:i:s');
            $stmt->execute([
                'colab' => $colabId,
                'fecha' => $fecha,
                'entrada' => $horaEntrada,
                'salida' => $horaSalida,
                'fecha_now' => $now,
            ]);
            return $db->lastInsertId();
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function updateAsistencia(string $asisId, string $fecha, string $horaEntrada, ?string $horaSalida): bool
    {
        try {
            $db = get_db();
            $sql = 'UPDATE asistencias
                    SET asis_fecha = :fecha,
                        asis_hora_entrada = :entrada,
                        asis_hora_salida = :salida,
                        asis_ultima_actualizacion = :fecha_now
                    WHERE asis_id = :id';
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'fecha' => $fecha,
                'entrada' => $horaEntrada,
                'salida' => $horaSalida,
                'fecha_now' => date('Y-m-d H:i:s'),
                'id' => $asisId,
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function deleteAsistencia(string $asisId): bool
    {
        try {
            $db = get_db();
            $stmt = $db->prepare('DELETE FROM asistencias WHERE asis_id = :id');
            return $stmt->execute(['id' => $asisId]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function searchByColaborador(string $colabId): array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        asis_id,
                        asis_colab_id AS colab_id,
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

    public static function todas(): array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        a.asis_id,
                        a.asis_fecha,
                        a.asis_hora_entrada,
                        a.asis_hora_salida,
                        c.colab_primer_nombre,
                        c.colab_apellido_paterno
                    FROM asistencias a
                    INNER JOIN colaboradores c
                        ON a.asis_colab_id = c.colab_id
                    ORDER BY a.asis_fecha DESC, a.asis_hora_entrada DESC';

            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    // Buscar asistencias por nombre, apellido o cédula del colaborador
    public static function buscarPorColaborador(string $texto): array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        a.asis_id, 
                        a.asis_fecha, 
                        a.asis_hora_entrada, 
                        a.asis_hora_salida, 
                        c.colab_primer_nombre, 
                        c.colab_apellido_paterno
                    FROM asistencias a
                    INNER JOIN colaboradores c 
                        ON a.asis_colab_id = c.colab_id
                    WHERE 
                        c.colab_primer_nombre LIKE :texto
                        OR c.colab_apellido_paterno LIKE :texto
                        OR c.colab_cedula LIKE :texto
                    ORDER BY a.asis_fecha DESC';

            $stmt = $db->prepare($sql);
            $stmt->execute([
                'texto' => '%' . $texto . '%'
            ]);

            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    // Actualizar una asistencia existente
    public static function update(string $id, string $fecha, string $entrada, ? string $salida): bool {
        try {
            $stmt = get_db()->prepare(
                'UPDATE asistencias
                SET asis_fecha = :fecha,
                    asis_hora_entrada = :entrada,
                    asis_hora_salida = :salida,
                    asis_ultima_actualizacion = NOW()
                WHERE asis_id = :id'
            );

            return $stmt->execute([
                'id' => $id,
                'fecha' => $fecha,
                'entrada' => $entrada,
                'salida' => $salida
            ]);    
        } catch (Throwable $e) {
            return false;
        }    
    }

    // Eliminar una asistencia por su ID
    public static function delete(string $id): bool{
        try {
            $stmt = get_db()->prepare(
                'DELETE FROM asistencias WHERE asis_id = :id'
            );
            return $stmt->execute(['id' => $id]);
        } catch (Throwable $e) {
            return false;
        }
    }

    // Encontrar una asistencia por su ID
    public static function find(string $id): ?array{
        try {
            $stmt = get_db()->prepare(
                'SELECT asis_id, asis_fecha, asis_hora_entrada, asis_hora_salida
                FROM asistencias
                WHERE asis_id = :id'
            );

            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch();

            return $result ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    // Funciones de Colaborador

    // Obtener asistencias de un colaborador por el mismo colaborador
    public static function porColaborador(string $colabId): array
    {
        try {
            $db = get_db();
            $sql = 'SELECT 
                        asis_id,
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

    public static function registrarEntrada(string $colabId, string $fecha, string $horaEntrada): bool
    {
        try {
            $db = get_db();

            // Verificar si ya existe una entrada para el colaborador en la fecha dada
            $check = $db->prepare(
                'SELECT COUNT(*) FROM asistencias WHERE asis_colab_id = :colab AND asis_fecha = :fecha'
            );
            $check->execute(['colab' => $colabId, 'fecha' => $fecha]);

            if ($check->fetchColumn() > 0) {
                return false;
            }

            // Insertar nueva entrada de asistencia
            $stmt = $db->prepare(
                'INSERT INTO asistencias (asis_colab_id, asis_fecha, asis_hora_entrada, asis_fecha_creacion, asis_ultima_actualizacion)
                 VALUES (:colab, :fecha, :hora_entrada, NOW(), NOW())'
            );
            // Ejecutar la inserción
            return $stmt->execute([
                'colab' => $colabId,
                'fecha' => $fecha,
                'hora_entrada' => $horaEntrada
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function registrarSalida(string $colabId, string $fecha, string $horaSalida): bool
    {
        try {
            // Actualizar la hora de salida para la entrada existente
            $stmt = get_db()->prepare(
                'UPDATE asistencias
                 SET asis_hora_salida = :hora_salida, asis_ultima_actualizacion = NOW()
                 WHERE asis_colab_id = :colab AND asis_fecha = :fecha AND asis_hora_salida IS NULL'
            );
            // Ejecutar la actualización
            $stmt->execute([
                'hora_salida' => $horaSalida,
                'colab' => $colabId,
                'fecha' => $fecha
            ]);
            // Retornar verdadero si se actualizó alguna fila
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

}

