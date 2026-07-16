<?php
require_once __DIR__ . '/config/config.php';

$pdo = conectarDB();
$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare("SELECT n.*, u.nombre AS autor_nombre FROM noticias n LEFT JOIN usuarios u ON u.id = n.autor_id WHERE n.slug = :slug AND n.estado = 'publicado' LIMIT 1");
$stmt->execute(['slug' => $slug]);
$noticia = $stmt->fetch();

if (!$noticia) {
    http_response_code(404);
    $titulo_pagina = 'Noticia no encontrada — ' . NOMBRE_SITIO;
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="seccion"><div class="contenedor"><div class="vacio">Esta noticia no existe o fue retirada.<br><br><a href="noticias.php" class="btn btn-linea">Volver a noticias</a></div></div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Contador de vistas simple
$pdo->prepare('UPDATE noticias SET vistas = vistas + 1 WHERE id = :id')->execute(['id' => $noticia['id']]);

$titulo_pagina = $noticia['titulo'] . ' — ' . NOMBRE_SITIO;
$descripcion_pagina = $noticia['resumen'];

require_once __DIR__ . '/includes/header.php';
?>

<section class="seccion">
    <div class="contenedor">
        <div class="articulo-cab">
            <div class="meta"><?= e($noticia['categoria']) ?><span><?= fechaLarga($noticia['publicado_en']) ?></span><span><?= e($noticia['autor_nombre'] ?? 'Departamento de Prensa') ?></span></div>
            <h1><?= e($noticia['titulo']) ?></h1>
        </div>

        <?php if (!empty($noticia['imagen'])): ?>
        <div class="articulo-img">
            <img src="<?= e($noticia['imagen']) ?>" alt="">
        </div>
        <?php endif; ?>

        <div class="articulo-cuerpo">
            <?php foreach (explode("\n", trim($noticia['contenido'])) as $parrafo): ?>
                <?php if (trim($parrafo) !== ''): ?>
                    <p><?= e($parrafo) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="articulo-cuerpo" style="margin-top: 32px;">
            <a href="noticias.php" class="btn btn-linea">← Volver a noticias</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
