<?php
$asistencia = $asistencia ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Asistencia</title>
</head>
<body>
    <h1>Editar Asistencia</h1>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= $asistencia['asis_id'] ?>">
        
        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" id="fecha" value="<?= $asistencia['asis_fecha'] ?>" required><br><br>

        <label for="hora_entrada">Hora de Entrada:</label>
        <input type="time" name="hora_entrada" id="hora_entrada" value="<?= $asistencia['asis_hora_entrada'] ?>" required><br><br>

        <label for="hora_salida">Hora de Salida:</label>
        <input type="time" name="hora_salida" id="hora_salida" value="<?= $asistencia['asis_hora_salida'] ?>"><br><br>

        <button type="submit">Actualizar Asistencia</button>
    </form>
</body>
</html>