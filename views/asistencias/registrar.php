<?php
// ✅ Evitar warnings si algo no llega desde el controller
$messages = $messages ?? [];
$errors = $errors ?? [];
$historial = $historial ?? [];
$fechaActual = $fechaActual ?? date('Y-m-d');
$horaActual  = $horaActual ?? date('H:i:s');
$confirmAction = $confirmAction ?? null;
?>

<!-- Vista: registrar entrada/salida y ver historial personal -->
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

<!-- ✅ BOTONES (UN SOLO BLOQUE) -->
<div class="card">
  <div class="actions-row asistencia-actions" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:8px;">
    <form method="post" class="inline-form">
      <input type="hidden" name="accion" value="entrada">
      <button class="btn primary" type="submit">Registrar entrada</button>
    </form>

    <form method="post" class="inline-form">
      <input type="hidden" name="accion" value="salida">
      <button class="btn secondary" type="submit">Registrar salida</button>
    </form>
  </div>

  <p class="help-text" style="margin-top:10px;">
    Hoy: <strong><?= htmlspecialchars($fechaActual) ?></strong> — Hora actual: <strong><?= htmlspecialchars($horaActual) ?></strong>
  </p>
</div>

<!-- ✅ TABLA HISTORIAL -->
<div class="card">
  <h3 style="margin-top:0;">Historial de asistencias</h3>

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
            $entrada = $h['hora_entrada'] ?? null;
            $salida  = $h['hora_salida'] ?? null;

            if ($entrada && $salida) $estado = 'Completo';
            elseif ($entrada) $estado = 'Pendiente de salida';
            else $estado = '—';
          ?>
          <tr>
            <td><?= htmlspecialchars($h['fecha'] ?? '—') ?></td>
            <td><?= htmlspecialchars($entrada ?? '—') ?></td>
            <td><?= htmlspecialchars($salida ?? '—') ?></td>
            <td><?= htmlspecialchars($estado) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- ✅ MODAL CONFIRMACIÓN (OVERLAY REAL) -->
<?php if (!empty($confirmAction)): ?>
  <div class="modal-overlay">
    <div class="modal-card">
      <h3 style="margin-top:0;">
        ¿Seguro que deseas registrar la <?= $confirmAction === 'entrada' ? 'ENTRADA' : 'SALIDA' ?>?
      </h3>

      <p class="help-text" style="margin: 10px 0 16px;">
        Fecha: <strong><?= htmlspecialchars($fechaActual) ?></strong><br>
        Hora: <strong><?= htmlspecialchars($horaActual) ?></strong>
      </p>

      <div class="modal-actions">
        <form method="post" class="inline-form">
          <input type="hidden" name="accion" value="<?= htmlspecialchars($confirmAction) ?>">
          <input type="hidden" name="confirm" value="yes">
          <button class="btn primary" type="submit">Sí, confirmar</button>
        </form>

        <a class="btn secondary" href="index.php?page=registrar_asistencia">Cancelar</a>
      </div>
    </div>
  </div>
<?php endif; ?>
