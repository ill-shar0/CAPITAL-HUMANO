<?php
require_once BASE_PATH . '/config/db.php';

class User
{
    public static function findByColabId(string $colabId): ?array
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
                WHERE usu_colab_id = :colab
                LIMIT 1');
            $stmt->execute(['colab' => $colabId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Genera username único: primera letra + "." + apellido (lowercase).
     * Si existe, agrega sufijo numérico incremental.
     */
    public static function generateUsername(string $primerNombre, string $apellidoPaterno): string
    {
        $base = strtolower(substr(trim($primerNombre), 0, 1)) . '.' . strtolower(str_replace(' ', '', trim($apellidoPaterno)));

        if ($base === '.') {
            $base = 'user';
        }

        try {
            $db = get_db();
            $stmt = $db->prepare('SELECT username FROM usuarios WHERE username LIKE :pattern');
            $stmt->execute(['pattern' => $base . '%']);
            $usernames = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!$usernames) {
                return $base;
            }

            $maxSuffix = 0;
            foreach ($usernames as $u) {
                if ($u === $base) {
                    $maxSuffix = max($maxSuffix, 0);
                    continue;
                }
                if (strpos($u, $base) === 0) {
                    $suffix = substr($u, strlen($base));
                    if ($suffix !== '' && ctype_digit($suffix)) {
                        $maxSuffix = max($maxSuffix, (int)$suffix);
                    }
                }
            }
            return $base . ($maxSuffix + 1);
        } catch (Throwable $e) {
            return $base;
        }
    }
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
}
