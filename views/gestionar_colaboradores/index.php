<!-- Vista: alta y listado de colaboradores -->
<div class="page-header">
    <h1>Gestionar colaboradores</h1>
    <p class="help-text">El estado por defecto es Activo. Puede generar usuario con rol colaborador.</p>
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

<section class="section-block">
    <h2>Crear colaborador</h2>
    <form class="form-card" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create_colaborador">
        <label>Primer nombre</label>
        <input type="text" name="primer_nombre" required pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
        <label>Segundo nombre</label>
        <input type="text" name="segundo_nombre" required pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
        <label>Apellido paterno</label>
        <input type="text" name="apellido_paterno" required pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
        <label>Apellido materno</label>
        <input type="text" name="apellido_materno" required pattern="^[A-Za-z\s]+$" title="Solo letras y espacios">
        <label>Sexo</label>
        <select name="sexo" required>
            <option value="">Seleccione</option>
            <option value="M">M</option>
            <option value="F">F</option>
        </select>
        <label>Cédula</label>
        <input type="text" name="cedula" required>
        <label>Fecha de nacimiento</label>
        <input type="date" name="fecha_nac" required>
        <label>Correo</label>
        <input type="email" name="correo" required>
        <label>Teléfono</label>
        <input type="text" name="telefono" required>
        <label>Celular</label>
        <input type="text" name="celular" required>
        <label>Dirección</label>
        <input type="text" name="direccion" required>
        <label>Foto perfil</label>
        <input type="file" name="foto_perfil" accept="image/png,image/jpeg">
        <label>Sueldo</label>
        <input type="text" name="car_sueldo" required>
        <label>Cargo</label>
        <select name="car_cargo" required>
            <option value="">Seleccione cargo</option>
            <?php foreach (($cargos ?? []) as $cargo): ?>
                <option value="<?= htmlspecialchars($cargo['nombre_cargo'] ?? '') ?>">
                    <?= htmlspecialchars($cargo['nombre_cargo'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label>¿Crear usuario colaborador?</label>
        <select name="crear_usuario">
            <option value="no">No</option>
            <option value="si">Sí</option>
        </select>
        <button class="btn" type="submit">Crear colaborador</button>
    </form>
</section>

<section class="section-block">
    <h2>Listado de colaboradores</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Sexo</th>
                <th>Correo</th>
                <th>Estado</th>
                <th>Acciones</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($colaboradores)): ?>
                <?php foreach ($colaboradores as $col): ?>
                    <tr>
                        <td><?= htmlspecialchars($col['colab_id']) ?></td>
                        <td><?= htmlspecialchars(($col['primer_nombre'] ?? '') . ' ' . ($col['apellido_paterno'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($col['sexo'] ?? '') ?></td>
                        <td><?= htmlspecialchars($col['correo'] ?? '') ?></td>
                        <td><?= htmlspecialchars($col['estado_colaborador'] ?? '') ?></td>
                        <td class="actions">
                            <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=ver_colaborador&id=<?= urlencode($col['colab_id']) ?>">Ver</a>
                            <form method="post" class="inline-form">
                                <input type="hidden" name="action" value="delete_colaborador">
                                <input type="hidden" name="colab_id" value="<?= htmlspecialchars($col['colab_id']) ?>">
                                <button class="btn-link danger" type="submit">Eliminar (historial)</button>
                            </form>
                        </td>
                        <td>
                            <?php if (!empty($col['foto_perfil'])): ?>
                                <?php $fotoPath = ltrim($col['foto_perfil'], '/'); ?>
                                <a href="<?= BASE_URL ?>/<?= htmlspecialchars($fotoPath) ?>" target="_blank">
                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($fotoPath) ?>"
                                         style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
                                </a>
                            <?php else: ?>
                                    —
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No hay colaboradores registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>


