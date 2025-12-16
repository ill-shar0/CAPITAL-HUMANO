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
        <style>
            .clean-table {
                font-size: 13px;
            }
            .clean-table th,
            .clean-table td { border: 0; padding: 8px 6px; vertical-align: middle; }
            .clean-table tbody tr { border-bottom: 1px solid #f2f2f2; }
            .actions-col {
                display: flex;
                gap: 8px;
                align-items: center;
                flex-wrap: wrap;
            }
            .actions-col .btn-link { white-space: nowrap; font-size: 12px; }
        </style>
        <table class="table clean-table">
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
                            <td class="actions-col">
                                <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=ver_cargo&id=<?= urlencode($cargo['cargo_id']) ?>">Ver</a>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="action" value="delete_cargo">
                                    <input type="hidden" name="cargo_id" value="<?= htmlspecialchars($cargo['cargo_id']) ?>">
                                    <button class="btn-link danger" type="submit">Eliminar</button>
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

