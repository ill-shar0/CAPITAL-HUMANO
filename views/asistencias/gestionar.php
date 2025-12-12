<?php ob_start(); ?>
<div class="page-header">
    <h1>Gestionar asistencias</h1>
    <input type="text" placeholder="Buscar historial (colaborador)">
    <p class="help-text">Ordenado de más reciente a más antiguo. RRHH puede editar/eliminar.</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Colaborador</th>
            <th>Fecha</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($historial)): ?>
            <?php foreach ($historial as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['colab_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($item['fecha']) ?></td>
                    <td><?= htmlspecialchars($item['hora_entrada']) ?></td>
                    <td><?= htmlspecialchars($item['hora_salida'] ?? '') ?></td>
                    <td class="actions">
                        <button class="btn-link">Editar</button>
                        <button class="btn-link danger">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">Sin asistencias cargadas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>

