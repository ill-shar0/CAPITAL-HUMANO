<div class="page-header">
    <h1>Gestionar usuarios</h1>
    <p class="help-text">Password autogenerado, estado por defecto activo.</p>
</div>

<?php if (!empty($messages)): ?>
    <?php foreach ($messages as $msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="users-page">
    <form class="form-card" method="post">
        <input type="hidden" name="action" value="create_user">
        <h3>Crear usuario</h3>

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Rol</label>
        <select name="rol" required>
            <option value="administrador">administrador</option>
            <option value="recursos_humanos">recursos_humanos</option>
            <option value="colaborador">colaborador</option>
        </select>

        <button class="btn" type="submit">Crear (autogenera contraseña)</button>
    </form>

    <div>
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

                            <td>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="action" value="update_role_state">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                                    <input type="hidden" name="estado" value="<?= htmlspecialchars($user['estado_usuario']) ?>">

                                    <select name="rol">
                                        <option value="administrador" <?= $user['rol'] === 'administrador' ? 'selected' : '' ?>>administrador</option>
                                        <option value="recursos_humanos" <?= $user['rol'] === 'recursos_humanos' ? 'selected' : '' ?>>recursos_humanos</option>
                                        <option value="colaborador" <?= $user['rol'] === 'colaborador' ? 'selected' : '' ?>>colaborador</option>
                                    </select>

                                    <button class="btn secondary" type="submit">Actualizar</button>
                                </form>
                            </td>

                            <td><?= ($user['estado_usuario'] === '1') ? 'Activo' : 'Inactivo' ?></td>

                            <td class="actions">
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="action" value="toggle_estado">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                                    <input type="hidden" name="estado_actual" value="<?= htmlspecialchars($user['estado_usuario']) ?>">

                                    <button class="btn-link danger" type="submit">Cambiar estado</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No hay usuarios registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
