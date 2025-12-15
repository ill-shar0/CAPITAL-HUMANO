<?php
$filtros = is_array($filtros ?? null) ? $filtros : [];
$colaboradores = is_array($colaboradores ?? null) ? $colaboradores : [];
?>

<div class="page-header">
    <h1>Reporte colaborador y sueldo</h1>
    <p class="help-text">
        Filtros por sexo, edad, nombre, apellido y salario mínimo.
    </p>
</div>

<form class="filters" method="get" action="<?= BASE_URL ?>/index.php">
    <input type="hidden" name="page" value="reporte_colaborador_sueldo">

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
    <input type="number" name="salario_min" step="0.01"
           value="<?= htmlspecialchars($filtros['salario_min'] ?? '') ?>">

    <div class="actions-row">
        <button class="btn" type="submit">Buscar</button>

        <a class="btn secondary"
           href="<?= BASE_URL ?>/index.php?page=reporte_colaborador_sueldo">
            Limpiar
        </a>

        <a class="btn secondary"
            href="<?= BASE_URL ?>/index.php?page=reporte_colaborador_sueldo&export=csv">
                Exportar CSV (Excel)
        </a>
</button>

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
                    <td>$<?= htmlspecialchars($col['car_sueldo'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Sin resultados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (($totalPaginas ?? 1) > 1): ?>
<div class="pagination">

    <!-- Anterior -->
    <?php if ($paginaActual > 1): ?>
        <a class="btn secondary"
           href="<?= BASE_URL ?>/index.php?page=reporte_colaborador_sueldo&p=<?= $paginaActual - 1 ?>">
            «
        </a>
    <?php endif; ?>

    <!-- Números de página -->
    <?php
        $rango = 2; // páginas visibles a cada lado
        $inicio = max(1, $paginaActual - $rango);
        $fin = min($totalPaginas, $paginaActual + $rango);
    ?>

    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
        <?php if ($i === $paginaActual): ?>
            <span class="page-number active"><?= $i ?></span>
        <?php else: ?>
            <a class="page-number"
               href="<?= BASE_URL ?>/index.php?page=reporte_colaborador_sueldo&p=<?= $i ?>">
                <?= $i ?>
            </a>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- Siguiente -->
    <?php if ($paginaActual < $totalPaginas): ?>
        <a class="btn secondary"
           href="<?= BASE_URL ?>/index.php?page=reporte_colaborador_sueldo&p=<?= $paginaActual + 1 ?>">
            »
        </a>
    <?php endif; ?>

</div>
<?php endif; ?>
