<?php
require_once __DIR__ . '/includes/auth.php';
requerirRol(['admin']);

$pdo = conectarDB();
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id = (int)$_POST['id'];
    if ($id === (int)$_SESSION['usuario_id']) {
        $error = 'No puedes eliminar tu propio usuario mientras tienes la sesión abierta.';
    } else {
        $pdo->prepare('DELETE FROM usuarios WHERE id = :id')->execute(['id' => $id]);
        $mensaje = 'Usuario eliminado.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'alternar_estado') {
    $id = (int)$_POST['id'];
    if ($id !== (int)$_SESSION['usuario_id']) {
        $pdo->prepare('UPDATE usuarios SET activo = NOT activo WHERE id = :id')->execute(['id' => $id]);
        $mensaje = 'Estado del usuario actualizado.';
    }
}

$usuarios = $pdo->query('SELECT * FROM usuarios ORDER BY creado_en ASC')->fetchAll();

$titulo_pagina = 'Usuarios del panel — SAPD';
$seccion_activa = 'usuarios';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-cab">
    <div>
        <span class="rotulo">Administración</span>
        <h1>Usuarios del panel</h1>
    </div>
    <a href="usuario_form.php" class="btn btn-dorado">+ Nuevo usuario</a>
</div>

<?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>
<?php if ($mensaje): ?><div class="alerta alerta-exito"><?= e($mensaje) ?></div><?php endif; ?>

<div class="panel">
    <table>
        <thead><tr><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= e($u['nombre']) ?></td>
                <td><?= e($u['usuario']) ?></td>
                <td><span class="badge badge-<?= $u['rol'] ?>"><?= ucfirst($u['rol']) ?></span></td>
                <td><span class="badge badge-<?= $u['activo'] ? 'activo' : 'baja' ?>"><?= $u['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
                <td>
                    <div class="acciones-tabla">
                        <a class="editar" href="usuario_form.php?id=<?= $u['id'] ?>">Editar</a>
                        <?php if ((int)$u['id'] !== (int)$_SESSION['usuario_id']): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="accion" value="alternar_estado">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button type="submit" class="editar"><?= $u['activo'] ? 'Desactivar' : 'Activar' ?></button>
                        </form>
                        <form method="post" onsubmit="return confirm('¿Eliminar este usuario del panel?');" style="display:inline;">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button type="submit" class="borrar">Eliminar</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
