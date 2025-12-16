<!-- Vista: gestión de cargos (crear/editar/listar) -->
<div class="page-header">
    <h1>Gestionar cargos</h1>
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

<div class="grid two-cols">
    <form class="form-card" method="post">
        <input type="hidden" name="action" value="create_cargo">
        <h3>Crear cargo</h3>
        <label>Nombre</label>
        <input type="text" name="nombre_cargo" required pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
        <label>Departamento</label>
        <input type="text" name="departamento_cargo" pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
        <label>Sueldo</label>
        <input type="text" name="sueldo_cargo" required pattern="^[0-9]+(\.[0-9]{1,2})?$" title="Solo números y hasta 2 decimales">
        <label>Ocupación</label>
        <input type="text" name="ocupacion" pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
        <button class="btn" type="submit">Crear</button>
    </form>

    <div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Departamento</th>
                    <th>Sueldo</th>
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
                            <td class="actions">
                                <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=ver_cargo&id=<?= urlencode($cargo['cargo_id']) ?>">Ver</a>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="action" value="delete_cargo">
                                    <input type="hidden" name="cargo_id" value="<?= htmlspecialchars($cargo['cargo_id']) ?>">
                                    <button class="btn-link danger" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="action" value="update_cargo">
                                    <input type="hidden" name="cargo_id" value="<?= htmlspecialchars($cargo['cargo_id']) ?>">
                                    <input type="text" name="nombre_cargo" value="<?= htmlspecialchars($cargo['nombre_cargo']) ?>" placeholder="Nombre" required pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
                                    <input type="text" name="departamento_cargo" value="<?= htmlspecialchars($cargo['departamento_cargo']) ?>" placeholder="Departamento" pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
                                    <input type="text" name="sueldo_cargo" value="<?= htmlspecialchars($cargo['sueldo_cargo']) ?>" placeholder="Sueldo" required pattern="^[0-9]+(\.[0-9]{1,2})?$" title="Solo números y hasta 2 decimales">
                                    <input type="text" name="ocupacion" value="<?= htmlspecialchars($cargo['ocupacion']) ?>" placeholder="Ocupación" pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
                                    <button class="btn secondary" type="submit">Actualizar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No hay cargos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

