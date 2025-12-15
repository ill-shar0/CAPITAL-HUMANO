<!-- Vista: formulario para generar PDF de resuelto de vacaciones -->
<div class="page-header">
  <h1>Generar resuelto</h1>
  <p class="help-text">Resuelto pre-redactado. Complete los campos y genere PDF.</p>
</div>

<?php
$diasDisponiblesRaw = $colaborador['dias_disponibles'] ?? $colaborador['vac_dias_vacaciones_validos'] ?? null;
$diasDisponibles = $diasDisponiblesRaw !== null ? (int)$diasDisponiblesRaw : null;
$minimo = 7;
?>

<?php if (!empty($messages)): ?>
  <!-- Mensajes de éxito -->
  <?php foreach ($messages as $msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
  <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <!-- Mensajes de error -->
  <?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= htmlspecialchars($err) ?></div>
  <?php endforeach; ?>
<?php endif; ?>

<?php if (empty($colaborador)): ?>
  <div class="alert alert-error">No se encontró el colaborador para generar el resuelto.</div>
  <a class="btn secondary" href="<?= BASE_URL ?>/index.php?page=gestionar_vacaciones">Volver</a>
<?php else: ?>

  <div class="grid two-cols" style="align-items:flex-start; gap:16px;">
    <div class="card">
      <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <strong>Detalle del colaborador</strong>
        <span class="badge"><?= htmlspecialchars($colaborador['colab_car_cargo'] ?? 'Cargo no asignado') ?></span>
      </div>
      <div class="card-body">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($colaborador['colab_primer_nombre'] ?? '') ?></p>
        <p><strong>Apellido:</strong> <?= htmlspecialchars($colaborador['colab_apellido_paterno'] ?? '') ?></p>
        <p><strong>Cédula:</strong> <?= htmlspecialchars($colaborador['colab_cedula'] ?? '') ?></p>
        <p><strong>Días disponibles:</strong> <?= htmlspecialchars($diasDisponibles ?? 0) ?></p>
        <p><strong>Días trabajados (aprox.):</strong> <?= htmlspecialchars($colaborador['dias_trabajados'] ?? '0') ?></p>

        <div class="alert alert-info" style="margin-top:12px;">
          <strong>Requisitos:</strong>
          <ul class="help-list" style="margin:6px 0 0 0;">
            <li>Mínimo 7 días por solicitud.</li>
            <li>No pedir más de los días válidos disponibles.</li>
          </ul>
          <?php if ($diasDisponibles !== null && $diasDisponibles < $minimo): ?>
            <div class="alert alert-error" style="margin-top:8px;">El colaborador no cumple el mínimo de días para solicitar vacaciones.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <form class="form-card" method="post" action="<?= BASE_URL ?>/index.php?page=generar_resuelto&id=<?= urlencode($colaborador['colab_id']) ?>">
      <input type="hidden" name="action" value="create_resuelto">
      <input type="hidden" name="colab_id" value="<?= htmlspecialchars($colaborador['colab_id']) ?>">

      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
        <h3 style="margin:0;">Resuelto (PDF)</h3>
        <span class="help-text">Pre-llenado automático</span>
      </div>

      <label>Nombre</label>
      <input type="text" value="<?= htmlspecialchars($colaborador['colab_primer_nombre'] ?? '') ?>" readonly>

      <label>Apellido</label>
      <input type="text" value="<?= htmlspecialchars($colaborador['colab_apellido_paterno'] ?? '') ?>" readonly>

      <label>Cargo</label>
      <input type="text" value="<?= htmlspecialchars($colaborador['colab_car_cargo'] ?? '') ?>" readonly>

      <label>Días solicitados</label>
      <select name="dias_vacaciones" required>
        <option value="">Seleccione</option>
        <?php
          $opciones = [7, 14, 21, 30];
          $mostroOpcion = false;
          foreach ($opciones as $opt) {
              if ($opt < $minimo) {
                  continue;
              }
              if ($diasDisponibles !== null && $opt > $diasDisponibles) {
                  continue;
              }
              echo '<option value="' . $opt . '">' . $opt . ' días</option>';
              $mostroOpcion = true;
          }
        ?>
        <?php if (!$mostroOpcion): ?>
          <option value="" disabled>No hay opciones permitidas (disp. <?= htmlspecialchars($diasDisponibles ?? 0) ?>)</option>
        <?php endif; ?>
      </select>

      <label>Periodo (inicio - fin)</label>
      <div class="grid two-cols">
        <input type="date" name="periodo_inicio" required>
        <input type="date" name="periodo_fin" required>
      </div>

      <div class="actions-row" style="justify-content:flex-end;">
        <a class="btn secondary" href="<?= BASE_URL ?>/index.php?page=gestionar_vacaciones">Cancelar</a>
        <button class="btn" type="submit">Crear PDF</button>
      </div>
    </form>
  </div>

<?php endif; ?>
