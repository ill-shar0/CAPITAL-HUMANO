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
}

