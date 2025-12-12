<?php
require_once __DIR__ . '/../middleware/auth.php';

class Authz
{
    public static function requireRoles(array $roles): void
    {
        require_roles($roles);
    }
}

