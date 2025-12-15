<div class="page-header">
    <h1>Registrar asistencia</h1>
    <p class="help-text">Use Entrada/Salida para capturar fecha y hora exactas.</p>
</div>

<?php foreach ($messages as $m): ?>
    <div class="alert alert-success"><?= htmlspecialchars($m) ?></div>
<?php endforeach; ?>

<?php foreach ($errors as $e): ?>
    <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<!-- BOTONES -->
<div class="card">
    <div class="actions-row">
        <form method="post" class="inline-form">
            <input type="hidden" name="accion" value="entrada">
            <button class="btn primary">Registrar entrada</button>
        </form>

        <form method="post" class="inline-form">
            <input type="hidden" name="accion" value="salida">
            <button class="btn secondary">Registrar salida</button>
        </form>
    </div>

    <p class="help-text" style="margin-top:10px;">
        Hoy: <strong><?= $fechaActual ?></strong> — Hora actual: <strong><?= $horaActual ?></strong>
    </p>
</div>

<!-- TABLA HISTORIAL -->
<div class="card">
    <h3>Historial de asistencias</h3>

    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($historial)): ?>
            <tr>
                <td colspan="4" style="text-align:center;">No hay registros</td>
            </tr>
        <?php else: ?>
            <?php foreach ($historial as $h): ?>
                <?php
                    if ($h['hora_entrada'] && $h['hora_salida']) $estado = 'Completo';
                    elseif ($h['hora_entrada']) $estado = 'Pendiente de salida';
                    else $estado = '—';
                ?>
                <tr>
                    <td><?= htmlspecialchars($h['fecha']) ?></td>
                    <td><?= htmlspecialchars($h['hora_entrada'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($h['hora_salida'] ?? '—') ?></td>
                    <td><?= $estado ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODAL CONFIRMACIÓN -->
<?php if ($confirmAction): ?>
<div class="modal-overlay">
    <div class="modal-card">
        <h3>
            ¿Seguro que deseas registrar la <?= $confirmAction === 'entrada' ? 'ENTRADA' : 'SALIDA' ?>?
        </h3>

        <p class="help-text">
            Fecha: <strong><?= $fechaActual ?></strong><br>
            Hora: <strong><?= $horaActual ?></strong>
        </p>

        <div class="modal-actions">
            <form method="post">
                <input type="hidden" name="accion" value="<?= $confirmAction ?>">
                <input type="hidden" name="confirm" value="yes">
                <button class="btn primary">Sí, confirmar</button>
            </form>

            <a class="btn secondary" href="index.php?route=asistencias&page=registrar_asistencia">
                Cancelar
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
