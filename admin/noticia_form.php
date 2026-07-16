<?php
require_once __DIR__ . '/includes/auth.php';
requerirLogin();

$pdo = conectarDB();

$id = (int)($_GET['id'] ?? 0);
$editando = $id > 0;
$error = '';

$noticia = [
    'titulo' => '', 'resumen' => '', 'contenido' => '', 'imagen' => '',
    'categoria' => 'General', 'estado' => 'borrador',
];

if ($editando) {
    $stmt = $pdo->prepare('SELECT * FROM noticias WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $fila = $stmt->fetch();
    if (!$fila) {
        header('Location: noticias.php');
        exit;
    }
    $noticia = $fila;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noticia['titulo'] = trim($_POST['titulo'] ?? '');
    $noticia['resumen'] = trim($_POST['resumen'] ?? '');
    $noticia['contenido'] = trim($_POST['contenido'] ?? '');
    $noticia['imagen'] = trim($_POST['imagen'] ?? '');
    $noticia['categoria'] = trim($_POST['categoria'] ?? 'General');
    $noticia['estado'] = $_POST['estado'] ?? 'borrador';

    if ($noticia['titulo'] === '' || $noticia['resumen'] === '' || $noticia['contenido'] === '') {
        $error = 'Completa título, resumen y contenido.';
    } else {
        $slugBase = generarSlug($noticia['titulo']);
        $slug = $slugBase;

        // Evitar slugs duplicados
        $sufijo = 2;
        while (true) {
            $stmt = $pdo->prepare('SELECT id FROM noticias WHERE slug = :slug AND id != :id');
            $stmt->execute(['slug' => $slug, 'id' => $editando ? $id : 0]);
            if (!$stmt->fetch()) break;
            $slug = $slugBase . '-' . $sufijo;
            $sufijo++;
        }

        $publicadoEn = $noticia['estado'] === 'publicado'
            ? (($editando && !empty($noticia['publicado_en'])) ? $noticia['publicado_en'] : date('Y-m-d H:i:s'))
            : null;

        try {
            if ($editando) {
                $stmt = $pdo->prepare(
                    'UPDATE noticias SET titulo=:titulo, slug=:slug, resumen=:resumen, contenido=:contenido,
                     imagen=:imagen, categoria=:categoria, estado=:estado, publicado_en=:publicado_en WHERE id=:id'
                );
                $stmt->execute([
                    'titulo' => $noticia['titulo'], 'slug' => $slug, 'resumen' => $noticia['resumen'],
                    'contenido' => $noticia['contenido'], 'imagen' => $noticia['imagen'], 'categoria' => $noticia['categoria'],
                    'estado' => $noticia['estado'], 'publicado_en' => $publicadoEn, 'id' => $id,
                ]);
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO noticias (titulo, slug, resumen, contenido, imagen, categoria, autor_id, estado, publicado_en)
                     VALUES (:titulo, :slug, :resumen, :contenido, :imagen, :categoria, :autor_id, :estado, :publicado_en)'
                );
                $stmt->execute([
                    'titulo' => $noticia['titulo'], 'slug' => $slug, 'resumen' => $noticia['resumen'],
                    'contenido' => $noticia['contenido'], 'imagen' => $noticia['imagen'], 'categoria' => $noticia['categoria'],
                    'autor_id' => $_SESSION['usuario_id'], 'estado' => $noticia['estado'], 'publicado_en' => $publicadoEn,
                ]);
            }
            header('Location: noticias.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Ocurrió un error al guardar la noticia. Inténtalo de nuevo.';
        }
    }
}

$titulo_pagina = ($editando ? 'Editar' : 'Nueva') . ' noticia — Panel SAPD';
$seccion_activa = 'noticias';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-cab">
    <div>
        <span class="rotulo">Sala de prensa</span>
        <h1><?= $editando ? 'Editar noticia' : 'Nueva noticia' ?></h1>
    </div>
    <a href="noticias.php" class="btn btn-linea">← Volver al listado</a>
</div>

<?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

<div class="panel">
    <form method="post" action="noticia_form.php<?= $editando ? '?id=' . $id : '' ?>">
        <div class="campo">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" required value="<?= e($noticia['titulo']) ?>">
        </div>

        <div class="form-grid">
            <div class="campo">
                <label for="categoria">Categoría</label>
                <input type="text" id="categoria" name="categoria" value="<?= e($noticia['categoria']) ?>">
            </div>
            <div class="campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="borrador" <?= $noticia['estado'] === 'borrador' ? 'selected' : '' ?>>Borrador</option>
                    <option value="publicado" <?= $noticia['estado'] === 'publicado' ? 'selected' : '' ?>>Publicado</option>
                </select>
            </div>
        </div>

        <div class="campo">
            <label for="resumen">Resumen (aparece en las tarjetas de la portada)</label>
            <textarea id="resumen" name="resumen" maxlength="300" required><?= e($noticia['resumen']) ?></textarea>
        </div>

        <div class="campo">
            <label for="contenido">Contenido</label>
            <textarea id="contenido" name="contenido" style="min-height: 220px;" required><?= e($noticia['contenido']) ?></textarea>
            <span class="ayuda">Separa los párrafos con una línea en blanco.</span>
        </div>

        <div class="campo">
            <label for="imagen">URL de imagen destacada (opcional)</label>
            <input type="text" id="imagen" name="imagen" placeholder="https://..." value="<?= e($noticia['imagen']) ?>">
        </div>

        <button type="submit" class="btn btn-dorado"><?= $editando ? 'Guardar cambios' : 'Crear noticia' ?></button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
