<?php
$historial = is_array($historial ?? null) ? $historial : [];
$busqueda = $busqueda ?? '';
ob_start();
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
        <?php if (!empty($historial)): ?>
          <?php foreach ($historial as $item): ?>
            <tr>
              <td><?= htmlspecialchars(($item['colab_primer_nombre'] ?? '') . ' ' . ($item['colab_apellido_paterno'] ?? '')) ?></td>
              <td><?= htmlspecialchars($item['asis_fecha'] ?? '') ?></td>
              <td><?= htmlspecialchars($item['asis_hora_entrada'] ?? '') ?></td>
              <td><?= htmlspecialchars($item['asis_hora_salida'] ?? '-') ?></td>
              <td class="actions">
                <a class="btn-link"
                   href="<?= BASE_URL ?>/index.php?page=editar_asistencia&id=<?= urlencode($item['asis_id'] ?? '') ?>">
                  Editar
                </a>
                <a class="btn-link danger"
                   href="<?= BASE_URL ?>/index.php?page=eliminar_asistencia&id=<?= urlencode($item['asis_id'] ?? '') ?>"
                   onclick="return confirm('¿Eliminar esta asistencia?');">
                  Eliminar
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5">Sin asistencias cargadas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
