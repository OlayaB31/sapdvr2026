<?php
require_once __DIR__ . '/includes/auth.php';
requerirLogin();

$pdo = conectarDB();
$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'asignar') {
    $tipo = $_POST['tipo'] ?? '';
    $oficialId = (int)($_POST['oficial_id'] ?? 0);
    $motivo = trim($_POST['motivo'] ?? '');
    $inicio = $_POST['periodo_inicio'] ?? date('Y-m-d');
    $fin = $_POST['periodo_fin'] ?? date('Y-m-d');

    if (!in_array($tipo, ['semana', 'mes'], true) || !$oficialId || $motivo === '') {
        $error = 'Completa el oficial y el motivo del reconocimiento.';
    } else {
        // Desactiva el destacado anterior de ese mismo tipo y crea el nuevo
        $pdo->prepare('UPDATE destacados SET activo = 0 WHERE tipo = :tipo AND activo = 1')->execute(['tipo' => $tipo]);

        $stmt = $pdo->prepare(
            'INSERT INTO destacados (tipo, oficial_id, motivo, periodo_inicio, periodo_fin, activo)
             VALUES (:tipo, :oficial_id, :motivo, :inicio, :fin, 1)'
        );
        $stmt->execute([
            'tipo' => $tipo, 'oficial_id' => $oficialId, 'motivo' => $motivo,
            'inicio' => $inicio, 'fin' => $fin,
        ]);
        $mensaje = 'Reconocimiento asignado correctamente.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'retirar') {
    $idDestacado = (int)$_POST['id'];
    $pdo->prepare('UPDATE destacados SET activo = 0 WHERE id = :id')->execute(['id' => $idDestacado]);
    $mensaje = 'Reconocimiento retirado.';
}

$actualSemana = obtenerDestacadoActivo('semana');
$actualMes = obtenerDestacadoActivo('mes');

$oficiales = $pdo->query("SELECT id, nombre, apellido, placa FROM oficiales WHERE estado = 'activo' ORDER BY apellido")->fetchAll();

$historial = $pdo->query(
    'SELECT d.*, o.nombre, o.apellido FROM destacados d JOIN oficiales o ON o.id = d.oficial_id ORDER BY d.creado_en DESC LIMIT 10'
)->fetchAll();

$titulo_pagina = 'Oficial de la semana / mes — Panel SAPD';
$seccion_activa = 'destacados';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-cab">
    <div>
        <span class="rotulo">Reconocimientos</span>
        <h1>Oficial de la semana y del mes</h1>
    </div>
</div>

<?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>
<?php if ($mensaje): ?><div class="alerta alerta-exito"><?= e($mensaje) ?></div><?php endif; ?>

<div class="form-grid">
    <div class="panel">
        <h3 style="margin-top:0;">Actualmente en cartelera</h3>
        <p><strong style="color: var(--dorado);">Semana:</strong>
            <?= $actualSemana ? e($actualSemana['nombre'] . ' ' . $actualSemana['apellido']) : 'Sin asignar' ?>
        </p>
        <?php if ($actualSemana): ?>
        <form method="post" style="display:inline;">
            <input type="hidden" name="accion" value="retirar">
            <input type="hidden" name="id" value="<?= $actualSemana['id'] ?>">
            <button type="submit" class="btn btn-linea">Retirar distinción de la semana</button>
        </form>
        <?php endif; ?>

        <hr style="border: none; border-top: 1px solid var(--azul-linea); margin: 20px 0;">

        <p><strong style="color: var(--dorado);">Mes:</strong>
            <?= $actualMes ? e($actualMes['nombre'] . ' ' . $actualMes['apellido']) : 'Sin asignar' ?>
        </p>
        <?php if ($actualMes): ?>
        <form method="post" style="display:inline;">
            <input type="hidden" name="accion" value="retirar">
            <input type="hidden" name="id" value="<?= $actualMes['id'] ?>">
            <button type="submit" class="btn btn-linea">Retirar distinción del mes</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h3 style="margin-top:0;">Asignar nuevo reconocimiento</h3>
        <form method="post">
            <input type="hidden" name="accion" value="asignar">
            <div class="campo">
                <label for="tipo">Tipo de reconocimiento</label>
                <select id="tipo" name="tipo" required>
                    <option value="semana">Oficial de la semana</option>
                    <option value="mes">Oficial del mes</option>
                </select>
            </div>
            <div class="campo">
                <label for="oficial_id">Oficial</label>
                <select id="oficial_id" name="oficial_id" required>
                    <option value="">Selecciona un oficial</option>
                    <?php foreach ($oficiales as $o): ?>
                        <option value="<?= $o['id'] ?>">#<?= e($o['placa']) ?> — <?= e($o['nombre'] . ' ' . $o['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="motivo">Motivo del reconocimiento</label>
                <textarea id="motivo" name="motivo" required placeholder="Describe la actuación que motiva esta distinción..."></textarea>
            </div>
            <div class="form-grid">
                <div class="campo">
                    <label for="periodo_inicio">Inicio del período</label>
                    <input type="date" id="periodo_inicio" name="periodo_inicio" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="campo">
                    <label for="periodo_fin">Fin del período</label>
                    <input type="date" id="periodo_fin" name="periodo_fin" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-dorado">Asignar reconocimiento</button>
        </form>
    </div>
</div>

<div class="panel">
    <h3 style="margin-top:0;">Historial reciente</h3>
    <?php if ($historial): ?>
    <table>
        <thead><tr><th>Tipo</th><th>Oficial</th><th>Período</th><th>Estado</th></tr></thead>
        <tbody>
        <?php foreach ($historial as $h): ?>
            <tr>
                <td><?= $h['tipo'] === 'semana' ? 'Semana' : 'Mes' ?></td>
                <td><?= e($h['nombre'] . ' ' . $h['apellido']) ?></td>
                <td><?= e(date('d/m/Y', strtotime($h['periodo_inicio']))) ?> – <?= e(date('d/m/Y', strtotime($h['periodo_fin']))) ?></td>
                <td><?= $h['activo'] ? '<span class="badge badge-activo">Vigente</span>' : '<span class="badge badge-baja">Finalizado</span>' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="vacio-tabla">Todavía no se han asignado reconocimientos.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
