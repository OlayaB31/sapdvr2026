<?php
require_once __DIR__ . '/config/config.php';

$titulo_pagina = 'Contacto — ' . NOMBRE_SITIO;

require_once __DIR__ . '/includes/header.php';
?>

<section class="seccion">
    <div class="contenedor">
        <div class="seccion-cab">
            <div>
                <span class="rotulo">Institucional</span>
                <h2>Nuestros valores</h2>
            </div>
        </div>

        <div class="valores-grid">
            <div class="valor-item">
                <h3>Integridad</h3>
                <p>Cada oficial responde por sus actos frente a la comunidad y frente a sus propios compañeros de cuerpo.</p>
            </div>
            <div class="valor-item">
                <h3>Cercanía</h3>
                <p>La seguridad se construye en el trato diario con los vecinos de cada distrito, no solo en la respuesta a incidentes.</p>
            </div>
            <div class="valor-item">
                <h3>Disciplina</h3>
                <p>La formación continua y el respeto por el procedimiento son la base de cada operación del departamento.</p>
            </div>
        </div>
    </div>
</section>

<section class="seccion seccion-alterna">
    <div class="contenedor">
        <div class="seccion-cab">
            <div>
                <span class="rotulo">Cómo encontrarnos</span>
                <h2>Información de contacto</h2>
            </div>
        </div>

        <div class="info-contacto">
            <div>
                <span class="lbl">Emergencias</span>
                <p style="margin: 0; color: var(--texto);">911</p>
            </div>
            <div>
                <span class="lbl">Línea administrativa</span>
                <p style="margin: 0; color: var(--texto);">(555) 019-4420</p>
            </div>
            <div>
                <span class="lbl">Cuartel general</span>
                <p style="margin: 0; color: var(--texto);">Downtown Los Santos, San Andreas</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
