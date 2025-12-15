<!-- Vista: detalle de un cargo y colaboradores asignados -->
<div class="page-header">
    <h1>Detalle de cargo</h1>
    <?php if ($cargo): ?>
        <p><?= htmlspecialchars($cargo['cargo_id']) ?> - <?= htmlspecialchars($cargo['nombre_cargo']) ?></p>
    <?php else: ?>
        <p>No se encontró el cargo.</p>
    <?php endif; ?>
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

<?php if ($cargo): ?>
    <div class="grid two-cols">
        <div class="card">
            <h3>Información</h3>
            <p>Departamento: <?= htmlspecialchars($cargo['departamento_cargo']) ?></p>
            <p>Sueldo: <?= htmlspecialchars($cargo['sueldo_cargo']) ?></p>
            <p>Ocupación: <?= htmlspecialchars($cargo['ocupacion']) ?></p>
        </div>
        <div class="card">
            <h3>Acciones</h3>
            <a class="btn" href="<?= BASE_URL ?>/index.php?page=asignar_cargo&cargo=<?= urlencode($cargo['cargo_id']) ?>">Asignar a colaborador</a>
        </div>
    </div>

    <h3>Colaboradores asignados</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Periodo</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($colaboradores)): ?>
                <?php foreach ($colaboradores as $col): ?>
                    <tr>
                        <td><?= htmlspecialchars($col['colab_id']) ?></td>
                        <td><?= htmlspecialchars($col['primer_nombre'] . ' ' . $col['apellido_paterno']) ?></td>
                        <td><?= htmlspecialchars($col['periodo'] ?? '') ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="action" value="remove_assignment">
                                <input type="hidden" name="cargo_id" value="<?= htmlspecialchars($cargo['cargo_id']) ?>">
                                <input type="hidden" name="colab_id" value="<?= htmlspecialchars($col['colab_id']) ?>">
                                <button class="btn-link danger" type="submit">Quitar cargo</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">
                        No hay colaboradores asignados a este cargo.
                        <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=asignar_cargo&cargo=<?= urlencode($cargo['cargo_id']) ?>">Asignar a colaborador</a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

