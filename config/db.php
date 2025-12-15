<?php
function get_db(): PDO
{
    static $pdo = null; // cache conexión
    if ($pdo instanceof PDO) { // reutiliza si existe
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: 'localhost'; // host DB
    $name = getenv('DB_NAME') ?: 'capital_humano'; // nombre DB
    $user = getenv('DB_USER') ?: 'root'; // usuario DB
    $pass = getenv('DB_PASS') ?: ''; // password DB

    $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4"; // DSN MySQL

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // fetch asociativo
    ]);

    return $pdo; // retorna conexión lista
}

