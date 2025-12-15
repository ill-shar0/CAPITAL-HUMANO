<?php
require_once BASE_PATH . '/config/db.php';

class User
{
    public static function all(): array
    {
        try {
            $db = get_db();
            $stmt = $db->query('SELECT 
                    user_id,
                    username,
                    usu_rol AS rol,
                    usu_estado_usuario AS estado_usuario,
                    usu_fecha_creacion AS fecha_creacion,
                    usu_ultima_actualizacion AS ultima_actualizacion
                FROM usuarios
                ORDER BY username');
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function findById(string $userId): ?array
    {
        try {
            $db = get_db();
            $stmt = $db->prepare('SELECT 
                    user_id,
                    username,
                    usu_password_hash AS password_hash,
                    usu_rol AS rol,
                    usu_estado_usuario AS estado_usuario,
                    usu_colab_id AS colab_id,
                    usu_fecha_creacion AS fecha_creacion,
                    usu_ultima_actualizacion AS ultima_actualizacion
                FROM usuarios
                WHERE user_id = :id
                LIMIT 1');
            $stmt->execute(['id' => $userId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function findByUsername(string $username): ?array
    {
        try {
            $db = get_db();
            $stmt = $db->prepare('SELECT 
                    user_id,
                    username,
                    usu_password_hash AS password_hash,
                    usu_rol AS rol,
                    usu_estado_usuario AS estado_usuario,
                    usu_colab_id AS colab_id,
                    usu_fecha_creacion AS fecha_creacion,
                    usu_ultima_actualizacion AS ultima_actualizacion
                FROM usuarios
                WHERE username = :username
                LIMIT 1');
            $stmt->execute(['username' => $username]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function create($username, $passwordHash, $rol, $estado, $colabId = null): bool
{
    try {
        $db = get_db();

        $sql = 'INSERT INTO usuarios (username, usu_password_hash, usu_rol, usu_estado_usuario, usu_colab_id, usu_fecha_creacion, usu_ultima_actualizacion)
                VALUES (:username, :hash, :rol, :estado, :colab, :fecha, :fecha)';

        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([
            'username' => $username,
            'hash'     => $passwordHash,
            'rol'      => $rol,
            'estado'   => $estado,
            'colab'    => $colabId,
            'fecha'    => date('Y-m-d H:i:s'),
        ]);

        return $ok && $stmt->rowCount() > 0;

    } catch (PDOException $e) {
        // ✅ Duplicado (MySQL): 1062
        if ((int)($e->errorInfo[1] ?? 0) === 1062) return false;
        return false;
    }
}

    public static function updateRoleState(string $userId, string $rol, string $estado): bool
    {
        try {
            $db = get_db();
            $sql = 'UPDATE usuarios
                    SET usu_rol = :rol,
                        usu_estado_usuario = :estado,
                        usu_ultima_actualizacion = :fecha
                    WHERE user_id = :id';
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'rol' => $rol,
                'estado' => $estado,
                'fecha' => date('Y-m-d H:i:s'),
                'id' => $userId,
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function toggleEstado(string $userId, string $nuevoEstado): bool
    {
        try {
            $db = get_db();
            $sql = 'UPDATE usuarios
                    SET usu_estado_usuario = :estado,
                        usu_ultima_actualizacion = :fecha
                    WHERE user_id = :id';
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'estado' => $nuevoEstado,
                'fecha' => date('Y-m-d H:i:s'),
                'id' => $userId,
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function setPassword(string $userId, string $hash): bool
    {
        try {
            $db = get_db();
            $sql = 'UPDATE usuarios
                    SET usu_password_hash = :hash,
                        usu_ultima_actualizacion = :fecha
                    WHERE user_id = :id';
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'hash' => $hash,
                'fecha' => date('Y-m-d H:i:s'),
                'id' => $userId,
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Devuelve el colab_id asociado a un user_id, o null si no existe.
     */
    public static function colabIdByUserId(string $userId): ?string
    {
        try {
            $db = get_db();
            $stmt = $db->prepare('SELECT usu_colab_id FROM usuarios WHERE user_id = :id LIMIT 1');
            $stmt->execute(['id' => $userId]);
            $row = $stmt->fetch();
            if (!$row) {
                return null;
            }
            return $row['usu_colab_id'] ?? null;
        } catch (Throwable $e) {
            return null;
        }
    }
}
