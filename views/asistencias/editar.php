<?php // Vista: edición de una asistencia ?>
<?php
$asistencia = $asistencia ?? null;
ob_start();
?>

<div class="page-header">
    <h1>Editar asistencia</h1>
</div>

<form method="POST" class="form-card">
    <input type="hidden" name="action" value="update_asistencia">
    <input type="hidden" name="asis_id" value="<?= htmlspecialchars($asistencia['asis_id']) ?>">

    <label>Fecha</label>
    <input type="date" name="fecha"
           value="<?= htmlspecialchars($asistencia['asis_fecha']) ?>" required>

    <label>Hora de entrada</label>
    <input type="time" name="hora_entrada"
           value="<?= htmlspecialchars($asistencia['asis_hora_entrada']) ?>" required>

    <label>Hora de salida</label>
    <input type="time" name="hora_salida"
           value="<?= htmlspecialchars($asistencia['asis_hora_salida'] ?? '') ?>">

    <div class="actions-row">
        <button type="submit" class="btn">Guardar cambios</button>
        <a href="<?= BASE_URL ?>/index.php?page=gestionar_asistencias"
           class="btn secondary">Cancelar</a>
    </div>
</form>

<?php
