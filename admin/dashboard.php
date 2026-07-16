<?php
require_once __DIR__ . '/includes/auth.php';
requerirLogin();

$pdo = conectarDB();

$totalOficiales = (int)$pdo->query("SELECT COUNT(*) FROM oficiales WHERE estado = 'activo'")->fetchColumn();
$totalNoticiasPublicadas = (int)$pdo->query("SELECT COUNT(*) FROM noticias WHERE estado = 'publicado'")->fetchColumn();
$totalBorradores = (int)$pdo->query("SELECT COUNT(*) FROM noticias WHERE estado = 'borrador'")->fetchColumn();
$totalUsuarios = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE activo = 1")->fetchColumn();

$ultimasNoticias = $pdo->query('SELECT titulo, estado, creado_en FROM noticias ORDER BY creado_en DESC LIMIT 5')->fetchAll();

$titulo_pagina = 'Resumen — Panel SAPD';
$seccion_activa = 'dashboard';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-cab">
    <div>
        <span class="rotulo">Panel interno</span>
        <h1>Hola, <?= e(explode(' ', $_SESSION['usuario_nombre'])[0]) ?></h1>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-caja">
        <div class="num"><?= $totalOficiales ?></div>
        <div class="lbl">Oficiales activos</div>
    </div>
    <div class="stat-caja">
        <div class="num"><?= $totalNoticiasPublicadas ?></div>
        <div class="lbl">Noticias publicadas</div>
    </div>
    <div class="stat-caja">
        <div class="num"><?= $totalBorradores ?></div>
        <div class="lbl">Borradores pendientes</div>
    </div>
    <div class="stat-caja">
        <div class="num"><?= $totalUsuarios ?></div>
        <div class="lbl">Usuarios del panel</div>
    </div>
</div>

<div class="panel">
    <h3 style="margin-top:0;">Actividad reciente</h3>
    <?php if ($ultimasNoticias): ?>
    <table>
        <thead>
            <tr><th>Título</th><th>Estado</th><th>Creado</th></tr>
        </thead>
        <tbody>
            <?php foreach ($ultimasNoticias as $n): ?>
            <tr>
                <td><?= e($n['titulo']) ?></td>
                <td><span class="badge badge-<?= $n['estado'] ?>"><?= ucfirst($n['estado']) ?></span></td>
                <td><?= e(date('d/m/Y H:i', strtotime($n['creado_en']))) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="vacio-tabla">Aún no se ha creado ninguna noticia.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
