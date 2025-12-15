<?php ob_start(); ?>

<div class="page-header">
    <h1>Gestionar asistencias</h1>

    <!-- Búsqueda -->
    <form method="GET">
        <input type="hidden" name="page" value="gestionar_asistencias">
        <input
            type="text"
            name="q"
            placeholder="Buscar historial (colaborador)"
            value="<?= htmlspecialchars($busqueda ?? '') ?>"
        >
        <button type="submit">Buscar</button>
    </form>

    <p class="help-text">
        Ordenado de más reciente a más antiguo. RRHH puede editar/eliminar.
    </p>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Colaborador</th>
            <th>Fecha</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($historial)): ?>
            <?php foreach ($historial as $item): ?>
                <tr>
                    <td>
                        <!-- Mostrar nombre completo del colaborador -->
                        <?= htmlspecialchars(
                            $item['colab_primer_nombre'] . ' ' .
                            $item['colab_apellido_paterno']
                        ) ?>
                    </td>
                    <td><?= htmlspecialchars($item['asis_fecha']) ?></td>
                    <td><?= htmlspecialchars($item['asis_hora_entrada']) ?></td>
                    <td><?= htmlspecialchars($item['asis_hora_salida'] ?? '') ?></td>
                    <!-- Acciones: Editar y Eliminar -->
                    <td class="actions">
                        <!-- Enlaces para editar y eliminar la asistencia -->
                        <a href="?page=editar_asistencia&id=<?= urlencode($item['asis_id']) ?>">
                            Editar
                        </a>
                        <a href="?page=eliminar_asistencia&id=<?= urlencode($item['asis_id']) ?>"
                           onclick="return confirm('¿Eliminar esta asistencia?');">
                            Eliminar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Sin asistencias cargadas.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


