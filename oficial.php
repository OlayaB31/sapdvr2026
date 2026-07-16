<?php
require_once __DIR__ . '/config/config.php';

$pdo = conectarDB();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    'SELECT o.*, r.nombre AS rango_nombre, d.nombre AS division_nombre
     FROM oficiales o
     LEFT JOIN rangos r ON r.id = o.rango_id
     LEFT JOIN divisiones d ON d.id = o.division_id
     WHERE o.id = :id LIMIT 1'
);
$stmt->execute(['id' => $id]);
$oficial = $stmt->fetch();

if (!$oficial) {
    http_response_code(404);
    $titulo_pagina = 'Oficial no encontrado — ' . NOMBRE_SITIO;
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="seccion"><div class="contenedor"><div class="vacio">No encontramos a este oficial en el registro.<br><br><a href="oficiales.php" class="btn btn-linea">Volver al cuadro de oficiales</a></div></div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// ¿Es oficial de la semana o del mes actualmente?
$stmtDest = $pdo->prepare("SELECT tipo FROM destacados WHERE oficial_id = :id AND activo = 1");
$stmtDest->execute(['id' => $id]);
$distinciones = $stmtDest->fetchAll(PDO::FETCH_COLUMN);

$titulo_pagina = $oficial['nombre'] . ' ' . $oficial['apellido'] . ' — ' . NOMBRE_SITIO;

require_once __DIR__ . '/includes/header.php';
?>

<section class="seccion">
    <div class="contenedor">
        <div class="seccion-cab">
            <div>
                <span class="rotulo">Ficha de oficial</span>
                <h2>Placa #<?= e($oficial['placa']) ?></h2>
            </div>
            <a href="oficiales.php" class="ver-todo">← Volver al cuadro</a>
        </div>

        <div class="perfil-oficial">
            <div>
                <?php if (!empty($oficial['foto'])): ?>
                    <img class="perfil-foto" src="<?= e($oficial['foto']) ?>" alt="">
                <?php else: ?>
                    <div class="perfil-foto-vacia"><?= e(mb_substr($oficial['nombre'], 0, 1)) ?></div>
                <?php endif; ?>

                <?php if ($distinciones): ?>
                <div style="margin-top: 16px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <?php if (in_array('semana', $distinciones)): ?><span class="etiqueta-estado activo">Oficial de la semana</span><?php endif; ?>
                    <?php if (in_array('mes', $distinciones)): ?><span class="etiqueta-estado activo">Oficial del mes</span><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <div>
                <h1><?= e($oficial['nombre'] . ' ' . $oficial['apellido']) ?></h1>
                <p style="font-family: var(--fuente-mono); color: var(--dorado); text-transform: uppercase; letter-spacing: 0.04em; font-size: 14px;">
                    <?= e($oficial['rango_nombre'] ?? 'Sin rango') ?><?= $oficial['division_nombre'] ? ' · ' . e($oficial['division_nombre']) : '' ?>
                </p>

                <?php if (!empty($oficial['biografia'])): ?>
                    <p><?= e($oficial['biografia']) ?></p>
                <?php endif; ?>

                <div class="ficha-datos">
                    <div><span>Placa</span><span>#<?= e($oficial['placa']) ?></span></div>
                    <div><span>Rango</span><span><?= e($oficial['rango_nombre'] ?? '—') ?></span></div>
                    <div><span>División</span><span><?= e($oficial['division_nombre'] ?? 'Sin asignar') ?></span></div>
                    <div><span>Ingreso al cuerpo</span><span><?= fechaLarga($oficial['fecha_ingreso']) ?></span></div>
                    <div><span>Estado</span><span><?= e(estadoOficialTexto($oficial['estado'])) ?></span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
