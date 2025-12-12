<?php ob_start(); ?>
<div class="page-header">
    <h1>Gestionar usuarios</h1>
    <button class="btn">Crear usuario</button>
    <p class="help-text">Password autogenerado, estado por defecto activo.</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['rol']) ?></td>
                    <td><?= htmlspecialchars($user['estado_usuario']) ?></td>
                    <td class="actions">
                        <button class="btn-link">Ver</button>
                        <button class="btn-link">Actualizar permisos</button>
                        <button class="btn-link danger">Cambiar estado</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No hay usuarios registrados.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>

