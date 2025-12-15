<?php
require_once __DIR__ . '/../middleware/auth.php'; // helpers de auth base

class Authz
{
    public static function requireRoles(array $roles): void
    {
        require_roles($roles); // delega en middleware/auth
    }
}

