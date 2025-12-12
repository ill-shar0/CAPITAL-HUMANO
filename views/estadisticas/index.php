<?php ob_start(); ?>
<div class="page-header">
    <h1>Estadísticas</h1>
    <p class="help-text">Colaboradores por sexo, dirección, rango de edad.</p>
</div>

<div class="grid two-cols">
    <div class="card">
        <h3>Por sexo</h3>
        <ul>
            <?php foreach ($estadisticas['por_sexo'] as $item): ?>
                <li><?= htmlspecialchars($item['sexo']) ?>: <?= htmlspecialchars($item['total']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="card">
        <h3>Por dirección</h3>
        <ul>
            <?php foreach ($estadisticas['por_direccion'] as $item): ?>
                <li><?= htmlspecialchars($item['direccion']) ?>: <?= htmlspecialchars($item['total']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="card">
    <h3>Por rango de edad</h3>
    <ul>
        <?php foreach ($estadisticas['por_rango_edad'] as $item): ?>
            <li><?= htmlspecialchars($item['rango']) ?>: <?= htmlspecialchars($item['total']) ?></li>
        <?php endforeach; ?>
    </ul>
    <p class="help-text">Ejemplo de rango 25-30, ajustable a más rangos.</p>
</div>
<?php $content = ob_get_clean(); ?>

