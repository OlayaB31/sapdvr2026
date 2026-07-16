<?php
require_once __DIR__ . '/config/config.php';

$titulo_pagina = 'Oficiales — ' . NOMBRE_SITIO;

$pdo = conectarDB();

$rangoFiltro = $_GET['rango'] ?? '';
$divisionFiltro = $_GET['division'] ?? '';
$busqueda = trim($_GET['q'] ?? '');

$condiciones = ["o.estado = 'activo'"];
$parametros = [];

if ($rangoFiltro !== '') {
    $condiciones[] = 'o.rango_id = :rango';
    $parametros['rango'] = $rangoFiltro;
}
if ($divisionFiltro !== '') {
    $condiciones[] = 'o.division_id = :division';
    $parametros['division'] = $divisionFiltro;
}
if ($busqueda !== '') {
    $condiciones[] = '(o.nombre LIKE :q OR o.apellido LIKE :q OR o.placa LIKE :q)';
    $parametros['q'] = '%' . $busqueda . '%';
}

$sql = 'SELECT o.*, r.nombre AS rango_nombre, r.nivel AS rango_nivel, d.nombre AS division_nombre
        FROM oficiales o
        LEFT JOIN rangos r ON r.id = o.rango_id
        LEFT JOIN divisiones d ON d.id = o.division_id
        WHERE ' . implode(' AND ', $condiciones) . '
        ORDER BY r.nivel ASC, o.apellido ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$oficiales = $stmt->fetchAll();

$rangos = obtenerRangos();
$divisiones = obtenerDivisiones();

require_once __DIR__ . '/includes/header.php';
?>

<section class="seccion">
    <div class="contenedor">
        <div class="seccion-cab">
            <div>
                <span class="rotulo">Cadena de mando</span>
                <h2>Cuadro de oficiales</h2>
            </div>
        </div>

        <form class="filtros" method="get" action="oficiales.php">
            <input type="text" name="q" placeholder="Buscar por nombre o placa" value="<?= e($busqueda) ?>">
            <select name="rango" onchange="this.form.submit()">
                <option value="">Todos los rangos</option>
                <?php foreach ($rangos as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= (string)$rangoFiltro === (string)$r['id'] ? 'selected' : '' ?>><?= e($r['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="division" onchange="this.form.submit()">
                <option value="">Todas las divisiones</option>
                <?php foreach ($divisiones as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= (string)$divisionFiltro === (string)$d['id'] ? 'selected' : '' ?>><?= e($d['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-linea">Filtrar</button>
        </form>

        <?php if ($oficiales): ?>
        <div class="oficiales-grid">
            <?php foreach ($oficiales as $o): ?>
            <a href="oficial.php?id=<?= (int)$o['id'] ?>" class="tarjeta-id">
                <?php if (!empty($o['foto'])): ?>
                    <img class="foto" src="<?= e($o['foto']) ?>" alt="">
                <?php else: ?>
                    <div class="foto-vacia"><?= e(mb_substr($o['nombre'], 0, 1)) ?></div>
                <?php endif; ?>
                <div>
                    <div class="placa">Placa #<?= e($o['placa']) ?></div>
                    <h3><?= e($o['nombre'] . ' ' . $o['apellido']) ?></h3>
                    <div class="rango"><?= e($o['rango_nombre'] ?? 'Sin rango') ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="vacio">No se encontraron oficiales con esos filtros.</div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
