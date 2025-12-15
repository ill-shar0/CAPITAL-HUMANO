<?php ob_start(); ?>

<div class="page-header">
  <h1>Gestionar vacaciones</h1>
  <p class="help-text">Cálculo: 1 día de vacaciones por cada 11 días trabajados. Solicitudes mínimo 7 días.</p>
</div>

<div class="alert alert-info">
  <strong>Requisitos:</strong>
  <ul class="help-list">
    <li>Mínimo 7 días por solicitud.</li>
    <li>No pedir más de los días válidos disponibles.</li>
    <li>Solo RRHH / Administrador pueden generar resueltos.</li>
  </ul>
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

<table class="table">
  <thead>
    <tr>
      <th>Colaborador</th>
      <th>Cargo</th>
      <th>Días trabajados</th>
      <th>Días válidos</th>
      <th>Estado</th>
      <th>Acción</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($vacaciones)): ?>
      <?php foreach ($vacaciones as $item): ?>
        <tr>
          <td><?= htmlspecialchars(($item['primer_nombre'] ?? '') . ' ' . ($item['apellido_paterno'] ?? '')) ?></td>
          <td><?= htmlspecialchars($item['car_cargo'] ?? '') ?></td>
          <td><?= htmlspecialchars($item['dias_trabajados'] ?? '') ?></td>
          <td><?= htmlspecialchars($item['dias_vacaciones_validos'] ?? '') ?></td>
          <td><?= htmlspecialchars($item['estado_vacaciones'] ?? '') ?></td>
          <td>
            <?php if (!empty($item['colab_id'])): ?>
              <?php $disp = (int)($item['dias_vacaciones_validos'] ?? 0); ?>
              <?php if ($disp < 7): ?>
                <span class="help-text">No cumple mínimo (disp. <?= $disp ?> días)</span>
              <?php else: ?>
                <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=generar_resuelto&id=<?= urlencode($item['colab_id']) ?>">
                  Generar Resuelto
                </a>
                <div class="help-text">Mín. 7, máx. <?= $disp ?> días</div>
              <?php endif; ?>
            <?php else: ?>
              <span class="help-text">Sin ID</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="6">No hay datos de vacaciones.</td></tr>
    <?php endif; ?>
  </tbody>
</table>


