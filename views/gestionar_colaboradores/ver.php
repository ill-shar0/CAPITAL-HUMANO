<!-- Vista: detalle de colaborador y su cargo -->
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <h1 style="margin:0;">Detalle de colaborador</h1>
    <?php if ($colaborador): ?>
        <button class="btn secondary" id="btn-edit-colab" type="button">Editar perfil</button>
    <?php endif; ?>
</div>

<style>
/* Layout tipo ficha */
.profile-card {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 20px;
    align-items: start;
}
@media (max-width: 768px) {
    .profile-card {
        grid-template-columns: 1fr;
    }
}
.avatar-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}
.avatar-circle {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #ddd;
    background: #f4f4f4;
}
.form-grid-auto {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
}
.form-grid-auto label {
    font-weight: 600;
    margin-bottom: 4px;
    display: inline-block;
}
.form-grid-auto .full-row {
    grid-column: 1 / -1;
}
.form-grid-auto input,
.form-grid-auto select {
    width: 100%;
    box-sizing: border-box;
    padding: 8px 10px;
}
.names-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
    background: #f8f9fb;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    padding: 12px;
}
</style>

<?php if (!empty($messages)): ?>
    <!-- Avisos de éxito (flash) -->
    <?php foreach ($messages as $msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endforeach; ?>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <!-- Avisos de error (flash) -->
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($colaborador): ?>
    <div class="card profile-card">
        <div class="avatar-box">
            <?php if (!empty($colaborador['foto_perfil'])): ?>
                <?php $fotoPath = ltrim($colaborador['foto_perfil'], '/'); ?>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($fotoPath) ?>" alt="Foto de perfil" class="avatar-circle">
            <?php else: ?>
                <div class="avatar-circle" style="display:flex;align-items:center;justify-content:center;color:#888;font-size:14px;">Sin foto</div>
            <?php endif; ?>
            <p style="margin:0;font-weight:600;"><?= htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['apellido_paterno']) ?></p>
            <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=ver_historial_cargos&id=<?= urlencode($colaborador['colab_id']) ?>">Ver historial de cargos</a>
            <?php if ($cargoActual): ?>
                <small style="color:#555;">Cargo actual: <a href="<?= BASE_URL ?>/index.php?page=ver_cargo&id=<?= urlencode($cargoActual['cargo_id']) ?>"><?= htmlspecialchars($cargoActual['nombre_cargo']) ?></a></small>
            <?php else: ?>
                <a class="btn secondary" href="<?= BASE_URL ?>/index.php?page=asignar_cargo&colab=<?= urlencode($colaborador['colab_id']) ?>">Asignar Cargo</a>
            <?php endif; ?>
        </div>

        <form id="form-edit-colab" method="post" action="<?= BASE_URL ?>/index.php?page=gestionar_colaboradores" enctype="multipart/form-data" class="form-grid-auto">
            <input type="hidden" name="action" value="update_colaborador">
            <input type="hidden" name="colab_id" value="<?= htmlspecialchars($colaborador['colab_id']) ?>">
            <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($colaborador['foto_perfil'] ?? '') ?>">

            <div class="full-row names-grid">
                <div>
                    <label>Primer nombre</label>
                    <input data-edit="1" type="text" name="primer_nombre" value="<?= htmlspecialchars($colaborador['primer_nombre'] ?? '') ?>" required pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
                </div>
                <div>
                    <label>Segundo nombre</label>
                    <input data-edit="1" type="text" name="segundo_nombre" value="<?= htmlspecialchars($colaborador['segundo_nombre'] ?? '') ?>" required pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
                </div>
                <div>
                    <label>Apellido paterno</label>
                    <input data-edit="1" type="text" name="apellido_paterno" value="<?= htmlspecialchars($colaborador['apellido_paterno'] ?? '') ?>" required pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
                </div>
                <div>
                    <label>Apellido materno</label>
                    <input data-edit="1" type="text" name="apellido_materno" value="<?= htmlspecialchars($colaborador['apellido_materno'] ?? '') ?>" required pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
                </div>
            </div>

            <div>
                <label>Sexo</label>
                <select data-edit="1" name="sexo" required>
                    <option value="">Seleccione</option>
                    <option value="M" <?= ($colaborador['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>M</option>
                    <option value="F" <?= ($colaborador['sexo'] ?? '') === 'F' ? 'selected' : '' ?>>F</option>
                </select>
            </div>

            <div>
                <label>Cédula</label>
                <input data-edit="1" type="text" name="cedula" value="<?= htmlspecialchars($colaborador['cedula'] ?? '') ?>" required>
            </div>

            <div>
                <label>Fecha de nacimiento</label>
                <input data-edit="1" type="date" name="fecha_nac" value="<?= htmlspecialchars($colaborador['fecha_nac'] ?? '') ?>" required>
            </div>

            <div>
                <label>Correo</label>
                <input data-edit="1" type="email" name="correo" value="<?= htmlspecialchars($colaborador['correo'] ?? '') ?>" required>
            </div>

            <div>
                <label>Teléfono</label>
                <input data-edit="1" type="text" name="telefono" value="<?= htmlspecialchars($colaborador['telefono'] ?? '') ?>" required>
            </div>

            <div>
                <label>Celular</label>
                <input data-edit="1" type="text" name="celular" value="<?= htmlspecialchars($colaborador['celular'] ?? '') ?>" required>
            </div>

            <div class="full-row">
                <label>Dirección</label>
                <input data-edit="1" type="text" name="direccion" value="<?= htmlspecialchars($colaborador['direccion'] ?? '') ?>" required>
            </div>

            <div>
                <label>Foto perfil (opcional)</label>
                <input data-edit="1" type="file" name="foto_perfil" accept="image/png,image/jpeg">
            </div>

            <div>
                <label>Sueldo</label>
                <input data-edit="1" type="text" name="car_sueldo" value="<?= htmlspecialchars($colaborador['car_sueldo'] ?? '') ?>" required pattern="^[0-9]+(\.[0-9]{1,2})?$" title="Solo números y hasta 2 decimales">
            </div>

            <div>
                <label>Cargo</label>
                <select data-edit="1" name="car_cargo" required>
                    <option value="">Seleccione cargo</option>
                    <?php foreach (($cargos ?? []) as $cargo): ?>
                        <option value="<?= htmlspecialchars($cargo['nombre_cargo'] ?? '') ?>" <?= ($colaborador['car_cargo'] ?? '') === ($cargo['nombre_cargo'] ?? '') ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cargo['nombre_cargo'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Estado</label>
                <?php $estados = ['Activo', 'Vacaciones', 'Licencia', 'Incapacitado']; ?>
                <select data-edit="1" name="estado_colaborador" required>
                    <?php foreach ($estados as $estado): ?>
                        <option value="<?= $estado ?>" <?= ($colaborador['estado_colaborador'] ?? '') === $estado ? 'selected' : '' ?>><?= $estado ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="full-row" style="margin-top:8px;">
                <button class="btn" type="submit">Guardar cambios</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('btn-edit-colab');
    var form = document.getElementById('form-edit-colab');
    if (!btn || !form) return;

    var editable = form.querySelectorAll('[data-edit="1"]');
    var isEditing = false;
    lockInputs(true);

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        isEditing = !isEditing;
        lockInputs(!isEditing);
        btn.textContent = isEditing ? 'Cancelar edición' : 'Editar perfil';
        if (isEditing) {
            var first = form.querySelector('[data-edit="1"]:not([type="file"])');
            if (first) first.focus();
        } else {
            form.reset();
        }
    });

    function lockInputs(lock) {
        editable.forEach(function (el) {
            el.disabled = lock;
        });
    }
});
</script>

