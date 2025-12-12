<?php ob_start(); ?>
<div class="page-header">
    <h1>Generar resuelto</h1>
    <p class="help-text">Resuelto pre-redactado. Complete los campos y genere PDF.</p>
</div>

<form class="form-card">
    <label>Colaborador</label>
    <input type="text" value="<?= $colaborador ? htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['apellido_paterno']) : '' ?>" readonly>

    <label>Cédula</label>
    <input type="text" value="<?= $colaborador ? htmlspecialchars($colaborador['cedula']) : '' ?>" readonly>

    <label>Cargo</label>
    <input type="text" value="<?= $colaborador ? htmlspecialchars($colaborador['car_cargo'] ?? '') : '' ?>" readonly>

    <label>Días de vacaciones</label>
    <select>
        <option value="7">7 días</option>
        <option value="14">14 días</option>
        <option value="21">21 días</option>
        <option value="30">30 días</option>
    </select>

    <label>Periodo (inicio - fin)</label>
    <div class="grid two-cols">
        <input type="date" name="periodo_inicio">
        <input type="date" name="periodo_fin">
    </div>

    <button class="btn" type="submit">Generar PDF</button>
</form>
<?php $content = ob_get_clean(); ?>

