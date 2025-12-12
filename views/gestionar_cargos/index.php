<?php ob_start(); ?>
<div class="page-header">
    <h1>Gestionar cargos</h1>
    <button class="btn">Crear cargo</button>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Departamento</th>
            <th>Sueldo</th>
            <th>Asignaciones</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($cargos)): ?>
            <?php foreach ($cargos as $cargo): ?>
                <tr>
                    <td><?= htmlspecialchars($cargo['cargo_id']) ?></td>
                    <td><?= htmlspecialchars($cargo['nombre_cargo']) ?></td>
                    <td><?= htmlspecialchars($cargo['departamento_cargo']) ?></td>
                    <td><?= htmlspecialchars($cargo['sueldo_cargo']) ?></td>
                    <td><?= htmlspecialchars($cargo['num_asignaciones'] ?? '0') ?></td>
                    <td class="actions">
                        <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=ver_cargo&id=<?= urlencode($cargo['cargo_id']) ?>">Ver</a>
                        <button class="btn-link">Actualizar</button>
                        <button class="btn-link danger">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No hay cargos registrados.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>

