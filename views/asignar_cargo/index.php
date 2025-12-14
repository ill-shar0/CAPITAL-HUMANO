<?php ob_start(); ?>
<div class="page-header">
    <h1>Asignar cargo</h1>
    <p class="help-text">Seleccione cargo, colaborador y periodo (Permanente, Eventual o Interino).</p>
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

<form class="form-card" method="post">
    <input type="hidden" name="action" value="assign_cargo">

    <label>Cargo</label>
    <select name="cargo_id">
        <?php foreach ($cargos as $cargo): ?>
            <option value="<?= htmlspecialchars($cargo['cargo_id']) ?>"><?= htmlspecialchars($cargo['nombre_cargo']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Colaborador</label>
    <select name="colab_id">
        <?php foreach ($colaboradores as $col): ?>
            <option value="<?= htmlspecialchars($col['colab_id']) ?>">
                <?= htmlspecialchars($col['primer_nombre'] . ' ' . $col['apellido_paterno']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Periodo</label>
    <div class="radio-row">
        <label><input type="radio" name="periodo" value="Permanente" checked> Permanente</label>
        <label><input type="radio" name="periodo" value="Eventual"> Eventual</label>
        <label><input type="radio" name="periodo" value="Interino"> Interino</label>
    </div>

    <button class="btn" type="submit">Asignar</button>
</form>
<?php $content = ob_get_clean(); ?>

