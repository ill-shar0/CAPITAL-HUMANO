<div class="page-header">
    <h1>Cambiar contraseña</h1>
    <p class="help-text">La contraseña la define la empresa; se almacena hasheada y se muestra una sola vez.</p>
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

<form class="form-card" method="post">
    <input type="hidden" name="action" value="change_pw">

    <label>Seleccionar usuario</label>
    <select name="user_id" required>
        <option value="">Elija un usuario</option>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['user_id']) ?>">
                    <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['rol']) ?>)
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>

    <label>Nueva contraseña (generada por la empresa)</label>
    <input type="text" name="new_password" placeholder="Ingrese contraseña asignada" required>

    <button class="btn" type="submit">Actualizar contraseña</button>
</form>