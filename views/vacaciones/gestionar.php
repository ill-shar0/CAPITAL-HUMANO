<?php ob_start(); ?>

<h1>Gestionar Vacaciones</h1>

<table>
    <thead>
        <tr>
            <th>Colaborador</th>
            <th>Cargo</th>
            <th>Días trabajados</th>
            <th>Días válidos</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($colaboradores as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['nombre']) ?></td>
            <td><?= htmlspecialchars($c['colab_car_cargo']) ?></td>
            <td><?= $c['dias_trabajados'] ?></td>
            <td><?= $c['dias_vacaciones_validos'] ?></td>
            <td>
                <a href="?page=generar_resuelto&id=<?= $c['colab_id'] ?>">
                    Generar Resuelto
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php $content = ob_get_clean(); ?>