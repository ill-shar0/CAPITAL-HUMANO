<?php
$messages = $messages ?? [];
$errors = $errors ?? [];
$historial = $historial ?? [];
$user = current_user();
$rolLabel = $user['usu_rol'] ?? $user['rol'] ?? '—';
?>

<!-- Vista: historial personal de asistencias -->
<div class="page-header">
  <div>
    <h1>Mis asistencias</h1>
    <p class="help-text">Vista personal según tu rol: <strong><?= htmlspecialchars($rolLabel) ?></strong></p>
  </div>
</div>

<?php foreach ($messages as $m): ?>
  <div class="alert alert-success"><?= htmlspecialchars($m) ?></div>
<?php endforeach; ?>

<?php foreach ($errors as $e): ?>
  <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<div class="card">
  <table class="table">
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Hora entrada</th>
        <th>Hora salida</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($historial)): ?>
        <tr>
          <td colspan="4" style="text-align:center;">Sin registros.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($historial as $item): ?>
          <?php
            $entrada = $item['hora_entrada'] ?? null;
            $salida  = $item['hora_salida'] ?? null;
            if ($entrada && $salida) {
              $estado = 'Completo';
            } elseif ($entrada) {
              $estado = 'Pendiente de salida';
            } else {
              $estado = '—';
            }
          ?>
          <tr>
            <td><?= htmlspecialchars($item['fecha'] ?? '—') ?></td>
            <td><?= htmlspecialchars($entrada ?? '—') ?></td>
            <td><?= htmlspecialchars($salida ?? '—') ?></td>
            <td><?= htmlspecialchars($estado) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
</table>


