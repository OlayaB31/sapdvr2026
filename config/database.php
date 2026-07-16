<?php
/**
 * Conexión a la base de datos.
 * Ajusta estos datos según tu entorno (XAMPP, hosting compartido, VPS...).
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'sapd');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function conectarDB(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        return $pdo;
    } catch (PDOException $e) {
        // En producción esto debería registrarse en un log, no mostrarse tal cual.
        die('No se pudo conectar a la base de datos: ' . $e->getMessage());
    }
}
