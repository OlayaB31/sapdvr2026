<?php
require_once __DIR__ . '/includes/auth.php';
requerirLogin();

$pdo = conectarDB();

$id = (int)($_GET['id'] ?? 0);
$editando = $id > 0;
$error = '';

$oficial = [
    'nombre' => '', 'apellido' => '', 'placa' => '', 'rango_id' => '', 'division_id' => '',
    'foto' => '', 'biografia' => '', 'fecha_ingreso' => date('Y-m-d'), 'estado' => 'activo',
];

if ($editando) {
    $stmt = $pdo->prepare('SELECT * FROM oficiales WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $fila = $stmt->fetch();
    if (!$fila) {
        header('Location: oficiales.php');
        exit;
    }
    $oficial = $fila;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oficial['nombre'] = trim($_POST['nombre'] ?? '');
    $oficial['apellido'] = trim($_POST['apellido'] ?? '');
    $oficial['placa'] = trim($_POST['placa'] ?? '');
    $oficial['rango_id'] = $_POST['rango_id'] ?? '';
    $oficial['division_id'] = $_POST['division_id'] ?: null;
    $oficial['foto'] = trim($_POST['foto'] ?? '');
    $oficial['biografia'] = trim($_POST['biografia'] ?? '');
    $oficial['fecha_ingreso'] = $_POST['fecha_ingreso'] ?? date('Y-m-d');
    $oficial['estado'] = $_POST['estado'] ?? 'activo';

    if ($oficial['nombre'] === '' || $oficial['apellido'] === '' || $oficial['placa'] === '' || $oficial['rango_id'] === '') {
        $error = 'Completa nombre, apellido, placa y rango.';
    } else {
        try {
            if ($editando) {
                $stmt = $pdo->prepare(
                    'UPDATE oficiales SET nombre=:nombre, apellido=:apellido, placa=:placa, rango_id=:rango_id,
                     division_id=:division_id, foto=:foto, biografia=:biografia, fecha_ingreso=:fecha_ingreso, estado=:estado
                     WHERE id=:id'
                );
                $oficial['id'] = $id;
                $stmt->execute($oficial);
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO oficiales (nombre, apellido, placa, rango_id, division_id, foto, biografia, fecha_ingreso, estado)
                     VALUES (:nombre, :apellido, :placa, :rango_id, :division_id, :foto, :biografia, :fecha_ingreso, :estado)'
                );
                $stmt->execute($oficial);
            }
            header('Location: oficiales.php');
            exit;
        } catch (PDOException $e) {
            $error = str_contains($e->getMessage(), 'Duplicate')
                ? 'Ya existe un oficial con ese número de placa.'
                : 'Ocurrió un error al guardar. Inténtalo de nuevo.';
        }
    }
}

$rangos = obtenerRangos();
$divisiones = obtenerDivisiones();

$titulo_pagina = ($editando ? 'Editar' : 'Nuevo') . ' oficial — Panel SAPD';
$seccion_activa = 'oficiales';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-cab">
    <div>
        <span class="rotulo">Recursos humanos</span>
        <h1><?= $editando ? 'Editar oficial' : 'Nuevo oficial' ?></h1>
    </div>
    <a href="oficiales.php" class="btn btn-linea">← Volver al listado</a>
</div>

<?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

<div class="panel">
    <form method="post" action="oficial_form.php<?= $editando ? '?id=' . $id : '' ?>">
        <div class="form-grid">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required value="<?= e($oficial['nombre']) ?>">
            </div>
            <div class="campo">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" required value="<?= e($oficial['apellido']) ?>">
            </div>
            <div class="campo">
                <label for="placa">Número de placa</label>
                <input type="text" id="placa" name="placa" required value="<?= e($oficial['placa']) ?>">
            </div>
            <div class="campo">
                <label for="fecha_ingreso">Fecha de ingreso</label>
                <input type="date" id="fecha_ingreso" name="fecha_ingreso" required value="<?= e($oficial['fecha_ingreso']) ?>">
            </div>
            <div class="campo">
                <label for="rango_id">Rango</label>
                <select id="rango_id" name="rango_id" required>
                    <option value="">Selecciona un rango</option>
                    <?php foreach ($rangos as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= (string)$oficial['rango_id'] === (string)$r['id'] ? 'selected' : '' ?>><?= e($r['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="division_id">División</label>
                <select id="division_id" name="division_id">
                    <option value="">Sin asignar</option>
                    <?php foreach ($divisiones as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= (string)$oficial['division_id'] === (string)$d['id'] ? 'selected' : '' ?>><?= e($d['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="activo" <?= $oficial['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="baja" <?= $oficial['estado'] === 'baja' ? 'selected' : '' ?>>De baja</option>
                    <option value="suspendido" <?= $oficial['estado'] === 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                </select>
            </div>
            <div class="campo">
                <label for="foto">URL de la foto (opcional)</label>
                <input type="text" id="foto" name="foto" placeholder="https://..." value="<?= e($oficial['foto']) ?>">
                <span class="ayuda">Si se deja vacío se mostrará un ícono con la inicial del oficial.</span>
            </div>
        </div>

        <div class="campo">
            <label for="biografia">Biografía / reseña</label>
            <textarea id="biografia" name="biografia"><?= e($oficial['biografia']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-dorado"><?= $editando ? 'Guardar cambios' : 'Registrar oficial' ?></button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
