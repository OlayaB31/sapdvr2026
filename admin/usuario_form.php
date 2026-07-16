<?php
require_once __DIR__ . '/includes/auth.php';
requerirRol(['admin']);

$pdo = conectarDB();

$id = (int)($_GET['id'] ?? 0);
$editando = $id > 0;
$error = '';

$usuario = ['nombre' => '', 'usuario' => '', 'rol' => 'editor', 'activo' => 1];

if ($editando) {
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $fila = $stmt->fetch();
    if (!$fila) {
        header('Location: usuarios.php');
        exit;
    }
    $usuario = $fila;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario['nombre'] = trim($_POST['nombre'] ?? '');
    $usuario['usuario'] = trim($_POST['usuario'] ?? '');
    $usuario['rol'] = $_POST['rol'] ?? 'editor';
    $password = $_POST['password'] ?? '';

    if ($usuario['nombre'] === '' || $usuario['usuario'] === '') {
        $error = 'Completa el nombre y el usuario de acceso.';
    } elseif (!$editando && $password === '') {
        $error = 'Define una contraseña para el nuevo usuario.';
    } else {
        try {
            if ($editando) {
                if ($password !== '') {
                    $stmt = $pdo->prepare('UPDATE usuarios SET nombre=:nombre, usuario=:usuario, rol=:rol, password=:password WHERE id=:id');
                    $stmt->execute([
                        'nombre' => $usuario['nombre'], 'usuario' => $usuario['usuario'], 'rol' => $usuario['rol'],
                        'password' => password_hash($password, PASSWORD_DEFAULT), 'id' => $id,
                    ]);
                } else {
                    $stmt = $pdo->prepare('UPDATE usuarios SET nombre=:nombre, usuario=:usuario, rol=:rol WHERE id=:id');
                    $stmt->execute(['nombre' => $usuario['nombre'], 'usuario' => $usuario['usuario'], 'rol' => $usuario['rol'], 'id' => $id]);
                }
            } else {
                $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, usuario, password, rol) VALUES (:nombre, :usuario, :password, :rol)');
                $stmt->execute([
                    'nombre' => $usuario['nombre'], 'usuario' => $usuario['usuario'],
                    'password' => password_hash($password, PASSWORD_DEFAULT), 'rol' => $usuario['rol'],
                ]);
            }
            header('Location: usuarios.php');
            exit;
        } catch (PDOException $e) {
            $error = str_contains($e->getMessage(), 'Duplicate')
                ? 'Ese nombre de usuario ya está en uso.'
                : 'Ocurrió un error al guardar. Inténtalo de nuevo.';
        }
    }
}

$titulo_pagina = ($editando ? 'Editar' : 'Nuevo') . ' usuario — Panel SAPD';
$seccion_activa = 'usuarios';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-cab">
    <div>
        <span class="rotulo">Administración</span>
        <h1><?= $editando ? 'Editar usuario' : 'Nuevo usuario' ?></h1>
    </div>
    <a href="usuarios.php" class="btn btn-linea">← Volver al listado</a>
</div>

<?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

<div class="panel">
    <form method="post" action="usuario_form.php<?= $editando ? '?id=' . $id : '' ?>">
        <div class="form-grid">
            <div class="campo">
                <label for="nombre">Nombre completo</label>
                <input type="text" id="nombre" name="nombre" required value="<?= e($usuario['nombre']) ?>">
            </div>
            <div class="campo">
                <label for="usuario">Usuario de acceso</label>
                <input type="text" id="usuario" name="usuario" required value="<?= e($usuario['usuario']) ?>">
            </div>
            <div class="campo">
                <label for="rol">Rol</label>
                <select id="rol" name="rol">
                    <option value="editor" <?= $usuario['rol'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>
            <div class="campo">
                <label for="password">Contraseña <?= $editando ? '(dejar vacío para no cambiarla)' : '' ?></label>
                <input type="password" id="password" name="password">
            </div>
        </div>

        <button type="submit" class="btn btn-dorado"><?= $editando ? 'Guardar cambios' : 'Crear usuario' ?></button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
