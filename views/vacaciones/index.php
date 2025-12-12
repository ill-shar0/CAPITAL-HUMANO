<?php ob_start(); ?>
<div class="page-header">
    <h1>Gestionar vacaciones</h1>
    <p class="help-text">Cálculo: 1 día de vacaciones por cada 11 días trabajados. Solicitudes mínimo 7 días.</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Colaborador</th>
            <th>Cargo</th>
            <th>Días trabajados</th>
            <th>Días válidos</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($vacaciones)): ?>
            <?php foreach ($vacaciones as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['primer_nombre'] . ' ' . $item['apellido_paterno']) ?></td>
                    <td><?= htmlspecialchars($item['car_cargo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($item['dias_trabajados']) ?></td>
                    <td><?= htmlspecialchars($item['dias_vacaciones_validos']) ?></td>
                    <td><?= htmlspecialchars($item['estado_vacaciones']) ?></td>
                    <td>
                        <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=generar_resuelto&id=<?= urlencode($item['colab_id']) ?>">Generar Resuelto</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No hay datos de vacaciones.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>

