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
}

