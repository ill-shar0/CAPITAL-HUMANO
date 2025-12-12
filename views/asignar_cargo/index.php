<?php ob_start(); ?>
<div class="page-header">
    <h1>Asignar cargo</h1>
    <p class="help-text">Seleccione cargo, colaborador y periodo (Permanente, Eventual o Interino).</p>
</div>

<form class="form-card">
    <label>Cargo</label>
    <select>
        <?php foreach ($cargos as $cargo): ?>
            <option value="<?= htmlspecialchars($cargo['cargo_id']) ?>"><?= htmlspecialchars($cargo['nombre_cargo']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Colaborador</label>
    <input type="text" placeholder="Buscar colaborador">

    <label>Periodo</label>
    <div class="radio-row">
        <label><input type="radio" name="periodo" value="Permanente"> Permanente</label>
        <label><input type="radio" name="periodo" value="Eventual"> Eventual</label>
        <label><input type="radio" name="periodo" value="Interino"> Interino</label>
    </div>

    <button class="btn" type="submit">Asignar</button>
</form>
<?php $content = ob_get_clean(); ?>

