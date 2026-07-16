<?php
require_once __DIR__ . '/includes/auth.php';
requerirLogin();

$pdo = conectarDB();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id = (int)$_POST['id'];
    $pdo->prepare('DELETE FROM noticias WHERE id = :id')->execute(['id' => $id]);
    $mensaje = 'Noticia eliminada.';
}

$noticias = $pdo->query(
    'SELECT n.*, u.nombre AS autor_nombre FROM noticias n LEFT JOIN usuarios u ON u.id = n.autor_id ORDER BY n.creado_en DESC'
)->fetchAll();

$titulo_pagina = 'Noticias — Panel SAPD';
$seccion_activa = 'noticias';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-cab">
    <div>
        <span class="rotulo">Sala de prensa</span>
        <h1>Noticias</h1>
    </div>
    <a href="noticia_form.php" class="btn btn-dorado">+ Nueva noticia</a>
</div>

<?php if ($mensaje): ?><div class="alerta alerta-exito"><?= e($mensaje) ?></div><?php endif; ?>

<div class="panel">
    <?php if ($noticias): ?>
    <table>
        <thead>
            <tr><th>Título</th><th>Categoría</th><th>Autor</th><th>Estado</th><th>Vistas</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($noticias as $n): ?>
            <tr>
                <td><?= e($n['titulo']) ?></td>
                <td><?= e($n['categoria']) ?></td>
                <td><?= e($n['autor_nombre'] ?? '—') ?></td>
                <td><span class="badge badge-<?= $n['estado'] ?>"><?= ucfirst($n['estado']) ?></span></td>
                <td><?= (int)$n['vistas'] ?></td>
                <td>
                    <div class="acciones-tabla">
                        <a class="editar" href="noticia_form.php?id=<?= $n['id'] ?>">Editar</a>
                        <form method="post" onsubmit="return confirm('¿Eliminar esta noticia? Esta acción no se puede deshacer.');" style="display:inline;">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= $n['id'] ?>">
                            <button type="submit" class="borrar">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="vacio-tabla">Todavía no se ha creado ninguna noticia.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
