<?php
$historial = is_array($historial ?? null) ? $historial : [];
$busqueda = $busqueda ?? '';
?>

<div class="asistencias-page">

  <div class="page-header">
    <h1>Gestionar asistencias</h1>

    <form method="GET" class="search-bar" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="page" value="gestionar_asistencias">

      <input type="text" name="q" placeholder="Buscar colaborador"
             value="<?= htmlspecialchars($busqueda) ?>">

      <button class="btn" type="submit">Buscar</button>
    </form>

    <p class="help-text">
      Ordenado de más reciente a más antiguo. Solo RRHH/Admin puede editar/eliminar.
    </p>
  </div>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>Colaborador</th>
          <th>Fecha</th>
          <th>Entrada</th>
          <th>Salida</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($historial)): ?>
          <tr>
            <td colspan="5" style="text-align:center;">No hay asistencias registradas</td>
          </tr>
        <?php else: ?>
          <?php foreach ($historial as $h): ?>
            <?php
              $nombre = trim(($h['colab_primer_nombre'] ?? '') . ' ' . ($h['colab_apellido_paterno'] ?? ''));
              $fecha = $h['asis_fecha'] ?? '—';
              $entrada = $h['asis_hora_entrada'] ?? '—';
              $salida = $h['asis_hora_salida'] ?? '—';
              $id = $h['asis_id'] ?? '';
            ?>
            <tr>
              <td><?= htmlspecialchars($nombre ?: '—') ?></td>
              <td><?= htmlspecialchars($fecha) ?></td>
              <td><?= htmlspecialchars($entrada) ?></td>
              <td><?= htmlspecialchars($salida) ?></td>
              <td class="actions">
                <?php if (!empty($id)): ?>
                  <a class="btn-link" href="<?= BASE_URL ?>/index.php?page=editar_asistencia&id=<?= urlencode($id) ?>">Editar</a>
                  <form method="post" action="<?= BASE_URL ?>/index.php?page=eliminar_asistencia" class="inline-form" style="display:inline;" onsubmit="return confirm('¿Eliminar esta asistencia?');">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <button class="btn-link danger" type="submit">Eliminar</button>
                  </form>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

