<?php ob_start(); ?>
<div class="page-header">
    <h1>Historial de cargos</h1>
    <?php if ($colaborador): ?>
        <p><?= htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['apellido_paterno']) ?> - <?= htmlspecialchars($colaborador['cedula']) ?></p>
    <?php endif; ?>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Cargo</th>
            <th>Departamento</th>
            <th>Sueldo</th>
            <th>Periodo</th>
            <th>Activo</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($historialCargos)): ?>
            <?php foreach ($historialCargos as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nombre_cargo']) ?></td>
                    <td><?= htmlspecialchars($item['departamento_cargo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($item['sueldo_cargo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($item['periodo'] ?? '') ?></td>
                    <td><?= ($item['activo'] === '1') ? 'Activo' : 'Histórico' ?></td>
                    <td><?= htmlspecialchars($item['fecha_creacion'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">Sin historial de cargos.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>

