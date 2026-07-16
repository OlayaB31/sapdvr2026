<?php
/**
 * Configuración general del sitio.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Bogota');

define('NOMBRE_SITIO', 'San Andreas Police Department');
define('SIGLAS_SITIO', 'SAPD');
define('LEMA_SITIO', 'Protección, integridad y servicio para todo San Andreas');

// Número de noticias por página en el listado público
define('NOTICIAS_POR_PAGINA', 6);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';
