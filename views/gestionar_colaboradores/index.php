<?php ob_start(); ?>
<div class="page-header">
    <h1>Gestionar colaboradores</h1>
    <button class="btn">Crear colaborador</button>
    <p class="help-text">El estado por defecto es Activo. Puede generar usuario con rol colaborador.</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Sexo</th>
            <th>Correo</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($colaboradores)): ?>
            <?php foreach ($colaboradores as $col): ?>
                <tr>
                    <td><?= htmlspecialchars($col['colab_id']) ?></td>
                    <td><?= htmlspecialchars($col['primer_nombre'] . ' ' . $col['apellido_paterno']) ?></td>
                    <td><?= htmlspecialchars($col['sexo']) ?></td>
                    <td><?= htmlspecialchars($col['correo']) ?></td>
                    <td><?= htmlspecialchars($col['estado_colaborador']) ?></td>
                    <td class="actions">
                        <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=ver_colaborador&id=<?= urlencode($col['colab_id']) ?>">Ver</a>
                        <button class="btn-link">Actualizar</button>
                        <button class="btn-link danger">Eliminar (manda a historial)</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No hay colaboradores registrados.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>

