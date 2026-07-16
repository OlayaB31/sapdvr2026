<?php
require_once __DIR__ . '/config/config.php';

$titulo_pagina = NOMBRE_SITIO . ' — Inicio';

$pdo = conectarDB();

$oficialSemana = obtenerDestacadoActivo('semana');
$oficialMes = obtenerDestacadoActivo('mes');

$ultimasNoticias = $pdo->query(
    "SELECT * FROM noticias WHERE estado = 'publicado' ORDER BY publicado_en DESC LIMIT 3"
)->fetchAll();

$totalOficiales = contarOficialesActivos();
$totalNoticias = contarNoticiasPublicadas();
$totalDivisiones = count(obtenerDivisiones());

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="contenedor hero-grid">
        <div>
            <span class="hero-eyebrow">Departamento operativo · San Andreas</span>
            <h1>Servicio y ley para <span class="acento">todo San Andreas</span></h1>
            <p class="hero-lead"><?= e(LEMA_SITIO) ?>. Conoce a nuestros oficiales, sigue nuestros comunicados y descubre cómo trabajamos cada día por la seguridad del estado.</p>
            <div class="hero-cta">
                <a href="oficiales.php" class="btn btn-dorado">Ver cuadro de oficiales</a>
                <a href="noticias.php" class="btn btn-linea">Últimos comunicados</a>
            </div>

            <div class="hero-stats">
                <div>
                    <div class="num"><?= $totalOficiales ?></div>
                    <div class="lbl">Oficiales activos</div>
                </div>
                <div>
                    <div class="num"><?= $totalDivisiones ?></div>
                    <div class="lbl">Divisiones</div>
                </div>
                <div>
                    <div class="num"><?= $totalNoticias ?></div>
                    <div class="lbl">Comunicados</div>
                </div>
            </div>
        </div>

        <svg class="placa-visual" viewBox="0 0 240 280" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M120 8 L224 44 V128 C224 190 182 234 120 264 C58 234 16 190 16 128 V44 Z" fill="#10192b" stroke="#c9a227" stroke-width="2.5"/>
            <path d="M120 8 L224 44 V128 C224 190 182 234 120 264 C58 234 16 190 16 128 V44 Z" fill="none" stroke="#253552" stroke-width="1" transform="scale(0.9)" transform-origin="120 136"/>
            <circle cx="120" cy="118" r="46" fill="none" stroke="#e0c15c" stroke-width="2"/>
            <text x="120" y="130" text-anchor="middle" font-family="Oswald, sans-serif" font-size="30" fill="#c9a227" font-weight="600">SAPD</text>
            <text x="120" y="190" text-anchor="middle" font-family="Roboto Mono, monospace" font-size="11" fill="#93a0b8" letter-spacing="2">SAN ANDREAS</text>
            <text x="120" y="206" text-anchor="middle" font-family="Roboto Mono, monospace" font-size="11" fill="#93a0b8" letter-spacing="2">POLICE DEPT.</text>
        </svg>
    </div>
</section>

<?php if ($oficialSemana || $oficialMes): ?>
<section class="seccion">
    <div class="contenedor">
        <div class="seccion-cab">
            <div>
                <span class="rotulo">Reconocimientos</span>
                <h2>Oficiales destacados</h2>
            </div>
        </div>

        <div class="destacados-grid">
            <?php if ($oficialSemana): ?>
            <a href="oficial.php?id=<?= (int)$oficialSemana['oficial_id'] ?>" class="tarjeta-destacado">
                <span class="sello">Oficial de la semana</span>
                <?php if (!empty($oficialSemana['foto'])): ?>
                    <img class="foto" src="<?= e($oficialSemana['foto']) ?>" alt="">
                <?php else: ?>
                    <div class="foto-vacia"><?= e(mb_substr($oficialSemana['nombre'], 0, 1)) ?></div>
                <?php endif; ?>
                <div>
                    <h3><?= e($oficialSemana['nombre'] . ' ' . $oficialSemana['apellido']) ?></h3>
                    <div class="rango"><?= e($oficialSemana['rango_nombre']) ?> · Placa #<?= e($oficialSemana['placa']) ?></div>
                    <p class="motivo"><?= e($oficialSemana['motivo']) ?></p>
                </div>
            </a>
            <?php endif; ?>

            <?php if ($oficialMes): ?>
            <a href="oficial.php?id=<?= (int)$oficialMes['oficial_id'] ?>" class="tarjeta-destacado">
                <span class="sello">Oficial del mes</span>
                <?php if (!empty($oficialMes['foto'])): ?>
                    <img class="foto" src="<?= e($oficialMes['foto']) ?>" alt="">
                <?php else: ?>
                    <div class="foto-vacia"><?= e(mb_substr($oficialMes['nombre'], 0, 1)) ?></div>
                <?php endif; ?>
                <div>
                    <h3><?= e($oficialMes['nombre'] . ' ' . $oficialMes['apellido']) ?></h3>
                    <div class="rango"><?= e($oficialMes['rango_nombre']) ?> · Placa #<?= e($oficialMes['placa']) ?></div>
                    <p class="motivo"><?= e($oficialMes['motivo']) ?></p>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="seccion seccion-alterna">
    <div class="contenedor">
        <div class="seccion-cab">
            <div>
                <span class="rotulo">Sala de prensa</span>
                <h2>Últimos comunicados</h2>
            </div>
            <a href="noticias.php" class="ver-todo">Ver todas las noticias →</a>
        </div>

        <?php if ($ultimasNoticias): ?>
        <div class="noticias-grid">
            <?php foreach ($ultimasNoticias as $n): ?>
            <a href="noticia.php?slug=<?= e($n['slug']) ?>" class="tarjeta-noticia">
                <?php if (!empty($n['imagen'])): ?>
                    <img class="img" src="<?= e($n['imagen']) ?>" alt="">
                <?php endif; ?>
                <div class="cuerpo">
                    <div class="meta"><span><?= e($n['categoria']) ?></span><span class="fecha"><?= tiempoRelativo($n['publicado_en']) ?></span></div>
                    <h3><?= e($n['titulo']) ?></h3>
                    <p><?= e($n['resumen']) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="vacio">Todavía no hay comunicados publicados.</div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
