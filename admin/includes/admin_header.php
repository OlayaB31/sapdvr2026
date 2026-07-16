<?php
// $titulo_pagina y $seccion_activa deben definirse antes de incluir este archivo
$titulo_pagina = $titulo_pagina ?? 'Panel — SAPD';
$seccion_activa = $seccion_activa ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($titulo_pagina) ?></title>
<link rel="icon" href="data:,">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="marca">
            <svg width="30" height="30" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M24 2 L44 10 V22 C44 34 36 42 24 46 C12 42 4 34 4 22 V10 Z" fill="#16213a" stroke="#c9a227" stroke-width="2"/>
                <path d="M24 9 L24 27 M15 18 L33 18" stroke="#c9a227" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            <span>
                <span class="siglas">SAPD</span>
                <span class="sub">Panel interno</span>
            </span>
        </div>

        <ul class="admin-nav">
            <li><a href="dashboard.php" class="<?= $seccion_activa === 'dashboard' ? 'activo' : '' ?>">Resumen</a></li>
            <li><a href="noticias.php" class="<?= $seccion_activa === 'noticias' ? 'activo' : '' ?>">Noticias</a></li>
            <li><a href="oficiales.php" class="<?= $seccion_activa === 'oficiales' ? 'activo' : '' ?>">Oficiales</a></li>
            <li><a href="destacados.php" class="<?= $seccion_activa === 'destacados' ? 'activo' : '' ?>">Oficial de la semana / mes</a></li>
            <?php if (esAdmin()): ?>
            <li><a href="usuarios.php" class="<?= $seccion_activa === 'usuarios' ? 'activo' : '' ?>">Usuarios del panel</a></li>
            <?php endif; ?>
            <li><a href="../index.php" target="_blank">Ver sitio público ↗</a></li>
        </ul>

        <div class="usuario-caja">
            <div class="nombre"><?= e($_SESSION['usuario_nombre'] ?? '') ?></div>
            <div class="rol"><?= e($_SESSION['usuario_rol'] ?? '') ?></div>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </aside>

    <main class="admin-main">
