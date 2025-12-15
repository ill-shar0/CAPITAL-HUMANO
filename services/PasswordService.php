<?php
class PasswordService
{
    // Genera password aleatorio (sin caracteres confusos)
    public static function generate(int $length = 12): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        $max = strlen($alphabet) - 1;
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $alphabet[random_int(0, $max)];
        }
        return $result;
    }

    // Hash BCRYPT
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Verifica password vs hash
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}

