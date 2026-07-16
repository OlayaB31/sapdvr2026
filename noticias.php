<?php
require_once __DIR__ . '/config/config.php';

$titulo_pagina = 'Noticias — ' . NOMBRE_SITIO;

$pdo = conectarDB();

$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina = NOTICIAS_POR_PAGINA;
$offset = ($pagina - 1) * $porPagina;

$total = (int)$pdo->query("SELECT COUNT(*) FROM noticias WHERE estado = 'publicado'")->fetchColumn();
$totalPaginas = max(1, (int)ceil($total / $porPagina));

$stmt = $pdo->prepare(
    "SELECT * FROM noticias WHERE estado = 'publicado' ORDER BY publicado_en DESC LIMIT :limite OFFSET :offset"
);
$stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$noticias = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="seccion">
    <div class="contenedor">
        <div class="seccion-cab">
            <div>
                <span class="rotulo">Sala de prensa</span>
                <h2>Noticias y comunicados</h2>
            </div>
        </div>

        <?php if ($noticias): ?>
        <div class="noticias-grid">
            <?php foreach ($noticias as $n): ?>
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

        <?php if ($totalPaginas > 1): ?>
        <div class="paginacion">
            <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                <?php if ($p === $pagina): ?>
                    <span class="actual"><?= $p ?></span>
                <?php else: ?>
                    <a href="noticias.php?pagina=<?= $p ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
            <div class="vacio">Todavía no hay comunicados publicados.</div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
