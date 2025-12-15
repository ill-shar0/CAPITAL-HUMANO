<!-- Vista: detalle de colaborador y su cargo -->
<div class="page-header">
    <h1>Detalle de colaborador</h1>
    <?php if ($colaborador): ?>
        <div style="display:flex;align-items:center;gap:12px;">
            <?php if (!empty($colaborador['foto_perfil'])): ?>
                <?php $fotoPath = ltrim($colaborador['foto_perfil'], '/'); ?>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($fotoPath) ?>"
                     alt="Foto de perfil"
                     style="width:48px;height:48px;object-fit:cover;border-radius:50%;border:1px solid #ddd;">
            <?php else: ?>
                <div style="width:48px;height:48px;border-radius:50%;border:1px solid #ddd;display:flex;align-items:center;justify-content:center;font-size:12px;color:#666;">Sin foto</div>
            <?php endif; ?>
            <p style="margin:0;"><?= htmlspecialchars($colaborador['primer_nombre'] . ' ' . $colaborador['apellido_paterno']) ?></p>
        </div>
    <?php else: ?>
        <p>No se encontró colaborador.</p>
    <?php endif; ?>
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

<?php if ($colaborador): ?>
    <div class="grid two-cols">
        <div>
            <h3>Datos personales</h3>
            <ul class="detail-list">
                <li>Cédula: <?= htmlspecialchars($colaborador['cedula']) ?></li>
                <li>Sexo: <?= htmlspecialchars($colaborador['sexo']) ?></li>
                <li>Correo: <?= htmlspecialchars($colaborador['correo']) ?></li>
                <li>Teléfono: <?= htmlspecialchars($colaborador['telefono'] ?? '') ?></li>
                <li>Estado: <?= htmlspecialchars($colaborador['estado_colaborador']) ?></li>
                <li>Fecha nac: <?= htmlspecialchars($colaborador['fecha_nac'] ?? '') ?></li>
            </ul>
            <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=ver_historial_cargos&id=<?= urlencode($colaborador['colab_id']) ?>">Ver historial de cargos</a>
        </div>
        <div>
            <h3>Cargo actual</h3>
            <?php if ($cargoActual): ?>
                <p><a href="<?= BASE_URL ?>/index.php?page=ver_cargo&id=<?= urlencode($cargoActual['cargo_id']) ?>"><?= htmlspecialchars($cargoActual['nombre_cargo']) ?></a></p>
                <p>Periodo: <?= htmlspecialchars($cargoActual['periodo'] ?? '') ?></p>
            <?php else: ?>
                <a class="btn" href="<?= BASE_URL ?>/index.php?page=asignar_cargo&colab=<?= urlencode($colaborador['colab_id']) ?>">Asignar Cargo</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <h3>Sueldo y cargo activo</h3>
        <p>Sueldo actual: <?= htmlspecialchars($colaborador['car_sueldo'] ?? 'No definido') ?></p>
        <?php if ($cargoActual): ?>
            <p>Cargo: <a href="<?= BASE_URL ?>/index.php?page=ver_cargo&id=<?= urlencode($cargoActual['cargo_id']) ?>"><?= htmlspecialchars($cargoActual['nombre_cargo']) ?></a></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

