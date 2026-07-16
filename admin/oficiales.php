<?php
require_once __DIR__ . '/includes/auth.php';
requerirLogin();

$pdo = conectarDB();
$mensaje = '';

// Eliminar oficial
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id = (int)$_POST['id'];
    $pdo->prepare('DELETE FROM oficiales WHERE id = :id')->execute(['id' => $id]);
    $mensaje = 'Oficial eliminado del registro.';
}

$oficiales = $pdo->query(
    'SELECT o.*, r.nombre AS rango_nombre, d.nombre AS division_nombre
     FROM oficiales o
     LEFT JOIN rangos r ON r.id = o.rango_id
     LEFT JOIN divisiones d ON d.id = o.division_id
     ORDER BY r.nivel ASC, o.apellido ASC'
)->fetchAll();

$titulo_pagina = 'Oficiales — Panel SAPD';
$seccion_activa = 'oficiales';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-cab">
    <div>
        <span class="rotulo">Recursos humanos</span>
        <h1>Oficiales</h1>
    </div>
    <a href="oficial_form.php" class="btn btn-dorado">+ Nuevo oficial</a>
</div>

<?php if ($mensaje): ?><div class="alerta alerta-exito"><?= e($mensaje) ?></div><?php endif; ?>

<div class="panel">
    <?php if ($oficiales): ?>
    <table>
        <thead>
            <tr><th>Placa</th><th>Nombre</th><th>Rango</th><th>División</th><th>Estado</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($oficiales as $o): ?>
            <tr>
                <td>#<?= e($o['placa']) ?></td>
                <td><?= e($o['nombre'] . ' ' . $o['apellido']) ?></td>
                <td><?= e($o['rango_nombre'] ?? '—') ?></td>
                <td><?= e($o['division_nombre'] ?? '—') ?></td>
                <td><span class="badge badge-<?= $o['estado'] ?>"><?= e(estadoOficialTexto($o['estado'])) ?></span></td>
                <td>
                    <div class="acciones-tabla">
                        <a class="editar" href="oficial_form.php?id=<?= $o['id'] ?>">Editar</a>
                        <form method="post" onsubmit="return confirm('¿Eliminar a este oficial del registro? Esta acción no se puede deshacer.');" style="display:inline;">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= $o['id'] ?>">
                            <button type="submit" class="borrar">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="vacio-tabla">No hay oficiales registrados todavía.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
