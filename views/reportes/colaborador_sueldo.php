<?php ob_start(); ?>
<div class="page-header">
    <h1>Reporte colaborador y sueldo</h1>
    <p class="help-text">Filtros por sexo, edad, nombre, apellido, salario. Exportable a Excel.</p>
</div>

<form class="filters">
    <label>Sexo</label>
    <select name="sexo">
        <option value="">Todos</option>
        <option value="M" <?= ($filtros['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>M</option>
        <option value="F" <?= ($filtros['sexo'] ?? '') === 'F' ? 'selected' : '' ?>>F</option>
    </select>
    <label>Edad mínima</label>
    <input type="number" name="edad_min" value="<?= htmlspecialchars($filtros['edad_min'] ?? '') ?>">
    <label>Edad máxima</label>
    <input type="number" name="edad_max" value="<?= htmlspecialchars($filtros['edad_max'] ?? '') ?>">
    <label>Nombre</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($filtros['nombre'] ?? '') ?>">
    <label>Apellido</label>
    <input type="text" name="apellido" value="<?= htmlspecialchars($filtros['apellido'] ?? '') ?>">
    <label>Salario mínimo</label>
    <input type="number" name="salario_min" value="<?= htmlspecialchars($filtros['salario_min'] ?? '') ?>">
    <div class="actions-row">
        <button class="btn" type="submit">Buscar</button>
        <button class="btn secondary" type="button">Exportar Excel</button>
    </div>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Sexo</th>
            <th>Salario</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($colaboradores)): ?>
            <?php foreach ($colaboradores as $col): ?>
                <tr>
                    <td><?= htmlspecialchars($col['primer_nombre'] . ' ' . $col['apellido_paterno']) ?></td>
                    <td><?= htmlspecialchars($col['sexo']) ?></td>
                    <td><?= htmlspecialchars($col['car_sueldo'] ?? $col['sueldo'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">Sin resultados.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <button class="btn secondary">Anterior</button>
    <button class="btn secondary">Siguiente</button>
</div>
<?php $content = ob_get_clean(); ?>

