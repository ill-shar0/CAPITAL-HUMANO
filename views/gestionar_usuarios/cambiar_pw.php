<?php ob_start(); ?>
<div class="page-header">
    <h1>Cambiar contraseña (solo RRHH / Admin)</h1>
    <p class="help-text">La contraseña la define la empresa; se almacena hasheada y se muestra una sola vez.</p>
</div>

<form class="form-card">
    <label>Seleccionar usuario</label>
    <select>
        <option value="">Elija un usuario</option>
        <?php foreach ($users as $user): ?>
            <option value="<?= htmlspecialchars($user['user_id']) ?>">
                <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['rol']) ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <label>Nueva contraseña (generada por la empresa)</label>
    <input type="text" placeholder="Ingrese contraseña asignada">

    <button class="btn" type="submit">Actualizar contraseña</button>
</form>
<?php $content = ob_get_clean(); ?>

