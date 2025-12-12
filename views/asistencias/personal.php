<?php ob_start(); ?>
<div class="page-header">
    <h1>Mis asistencias</h1>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Hora entrada</th>
            <th>Hora salida</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($historial)): ?>
            <?php foreach ($historial as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['fecha']) ?></td>
                    <td><?= htmlspecialchars($item['hora_entrada']) ?></td>
                    <td><?= htmlspecialchars($item['hora_salida'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">Sin registros.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>

