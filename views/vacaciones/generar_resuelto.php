<div class="page-header">
  <h1>Generar resuelto</h1>
  <p class="help-text">Resuelto pre-redactado. Complete los campos y genere PDF.</p>
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

<?php if (empty($colaborador)): ?>
  <div class="alert alert-error">No se encontró el colaborador para generar el resuelto.</div>
  <a class="btn secondary" href="<?= BASE_URL ?>/index.php?page=gestionar_vacaciones">Volver</a>
<?php else: ?>

  <form class="form-card" method="post" action="<?= BASE_URL ?>/index.php?page=generar_resuelto&id=<?= urlencode($colaborador['colab_id']) ?>">
    <input type="hidden" name="action" value="create_resuelto">
    <input type="hidden" name="colab_id" value="<?= htmlspecialchars($colaborador['colab_id']) ?>">

    <label>Colaborador</label>
    <input
      type="text"
      value="<?= htmlspecialchars(($colaborador['colab_primer_nombre'] ?? '') . ' ' . ($colaborador['colab_apellido_paterno'] ?? '')) ?>"
      readonly
    >

    <label>Cédula</label>
    <input type="text" value="<?= htmlspecialchars($colaborador['colab_cedula'] ?? '') ?>" readonly>

    <label>Cargo</label>
    <input type="text" value="<?= htmlspecialchars($colaborador['colab_car_cargo'] ?? '') ?>" readonly>

    <label>Días de vacaciones</label>
    <select name="dias_vacaciones" required>
      <option value="">Seleccione</option>
      <option value="7">7 días</option>
      <option value="14">14 días</option>
      <option value="21">21 días</option>
      <option value="30">30 días</option>
    </select>

    <label>Periodo (inicio - fin)</label>
    <div class="grid two-cols">
      <input type="date" name="periodo_inicio" required>
      <input type="date" name="periodo_fin" required>
    </div>

    <div class="actions-row">
      <button class="btn" type="submit">Generar PDF</button>
      <a class="btn secondary" href="<?= BASE_URL ?>/index.php?page=gestionar_vacaciones">Cancelar</a>
    </div>
  </form>

<?php endif; ?>

