<div class="page-header">
  <h1>Gestionar vacaciones</h1>
  <p class="help-text">Cálculo: 1 día de vacaciones por cada 11 días trabajados. Solicitudes mínimo 7 días.</p>
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
              <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=generar_resuelto&id=<?= urlencode($item['colab_id']) ?>">
                Generar Resuelto
              </a>
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