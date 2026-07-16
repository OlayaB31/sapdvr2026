<?php
// $titulo_pagina y $descripcion_pagina deben definirse antes de incluir este archivo
$titulo_pagina = $titulo_pagina ?? NOMBRE_SITIO;
$descripcion_pagina = $descripcion_pagina ?? LEMA_SITIO;
$pagina_actual = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($titulo_pagina) ?></title>
<meta name="description" content="<?= e($descripcion_pagina) ?>">
<link rel="icon" href="data:,">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="encabezado">
    <nav class="nav">
        <a href="index.php" class="marca">
            <svg class="marca-escudo" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M24 2 L44 10 V22 C44 34 36 42 24 46 C12 42 4 34 4 22 V10 Z" fill="#16213a" stroke="#c9a227" stroke-width="2"/>
                <path d="M24 9 L24 27 M15 18 L33 18" stroke="#c9a227" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="24" cy="18" r="4.5" fill="none" stroke="#e0c15c" stroke-width="2"/>
            </svg>
            <span class="marca-texto">
                <span class="siglas">SAPD</span><br>
                <span class="sub">San Andreas PD</span>
            </span>
        </a>

        <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false">☰</button>

        <ul class="nav-links">
            <li><a href="index.php" class="<?= $pagina_actual === 'index.php' ? 'activo' : '' ?>">Inicio</a></li>
            <li><a href="noticias.php" class="<?= in_array($pagina_actual, ['noticias.php','noticia.php']) ? 'activo' : '' ?>">Noticias</a></li>
            <li><a href="oficiales.php" class="<?= in_array($pagina_actual, ['oficiales.php','oficial.php']) ? 'activo' : '' ?>">Oficiales</a></li>
            <li><a href="contacto.php" class="<?= $pagina_actual === 'contacto.php' ? 'activo' : '' ?>">Contacto</a></li>
            <li><a href="admin/login.php">Panel</a></li>
        </ul>
    </nav>
</header>
