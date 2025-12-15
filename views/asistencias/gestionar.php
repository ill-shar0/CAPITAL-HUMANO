<<<<<<< HEAD
<?php
$historial = is_array($historial ?? null) ? $historial : [];
$busqueda = $busqueda ?? '';
ob_start();
?>

<div class="asistencias-page">

  <div class="page-header">
=======
<div class="page-header">
>>>>>>> 91616df4df6ad8d1c4e7185da467ba67478297ff
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

