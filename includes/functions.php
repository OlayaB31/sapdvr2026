<?php
/**
 * Funciones auxiliares de uso general.
 */

function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function generarSlug(string $texto): string
{
    $texto = strtolower(trim($texto));
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    return trim($texto, '-');
}

function fechaLarga(string $fecha): string
{
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
    ];
    $ts = strtotime($fecha);
    return (int)date('j', $ts) . ' de ' . $meses[(int)date('n', $ts)] . ' de ' . date('Y', $ts);
}

function tiempoRelativo(string $fecha): string
{
    $ahora = new DateTime();
    $entonces = new DateTime($fecha);
    $diff = $ahora->diff($entonces);

    if ($diff->days === 0) {
        if ($diff->h === 0) {
            return $diff->i <= 1 ? 'hace un momento' : "hace {$diff->i} minutos";
        }
        return $diff->h === 1 ? 'hace 1 hora' : "hace {$diff->h} horas";
    }
    if ($diff->days === 1) {
        return 'ayer';
    }
    if ($diff->days < 7) {
        return "hace {$diff->days} días";
    }
    return fechaLarga($fecha);
}

function nombreRango(array $oficial): string
{
    return $oficial['rango_nombre'] ?? 'Sin asignar';
}

function estadoOficialTexto(string $estado): string
{
    return match ($estado) {
        'activo' => 'Activo',
        'baja' => 'De baja',
        'suspendido' => 'Suspendido',
        default => ucfirst($estado),
    };
}

// ---------------------------------------------------------
// Consultas reutilizables
// ---------------------------------------------------------

function obtenerRangos(): array
{
    $pdo = conectarDB();
    return $pdo->query('SELECT * FROM rangos ORDER BY nivel ASC')->fetchAll();
}

function obtenerDivisiones(): array
{
    $pdo = conectarDB();
    return $pdo->query('SELECT * FROM divisiones ORDER BY nombre ASC')->fetchAll();
}

function obtenerDestacadoActivo(string $tipo): ?array
{
    $pdo = conectarDB();
    $stmt = $pdo->prepare(
        'SELECT d.*, o.nombre, o.apellido, o.placa, o.foto, r.nombre AS rango_nombre
         FROM destacados d
         JOIN oficiales o ON o.id = d.oficial_id
         LEFT JOIN rangos r ON r.id = o.rango_id
         WHERE d.tipo = :tipo AND d.activo = 1
         ORDER BY d.id DESC
         LIMIT 1'
    );
    $stmt->execute(['tipo' => $tipo]);
    $resultado = $stmt->fetch();
    return $resultado ?: null;
}

function contarOficialesActivos(): int
{
    $pdo = conectarDB();
    return (int)$pdo->query("SELECT COUNT(*) FROM oficiales WHERE estado = 'activo'")->fetchColumn();
}

function contarNoticiasPublicadas(): int
{
    $pdo = conectarDB();
    return (int)$pdo->query("SELECT COUNT(*) FROM noticias WHERE estado = 'publicado'")->fetchColumn();
}
