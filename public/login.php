<?php
require_once __DIR__ . '/../config/app.php'; // bootstrap (BASE_URL, render)
require_once __DIR__ . '/../config/db.php'; // conexión PDO
require_once __DIR__ . '/../middleware/auth.php'; // helpers auth
require_once __DIR__ . '/../models/User.php'; // modelo usuario
require_once __DIR__ . '/../services/PasswordService.php'; // hash/verificación

// Si ya está logueado, redirige al panel
if (current_user()) {
  header('Location: index.php');
  exit;
}

$error = ''; // mensaje de error

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  $user = User::findByUsername($username); // busca credenciales

  if (
    $user &&
    ($user['estado_usuario'] ?? '0') === '1' && // debe estar activo
    PasswordService::verify($password, $user['password_hash']) // valida password
  ) {
    // set sesión mínima
    $_SESSION['user'] = [
      'user_id' => $user['user_id'],
      'username' => $user['username'],
      'rol' => $user['rol'],
      'colab_id' => $user['colab_id'] ?? null,
    ];
    header('Location: index.php'); // ir a dashboard
    exit;
  }

  $error = 'Credenciales inválidas o usuario inactivo.'; // feedback
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ingreso - Capital Humano</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/main.css">
</head>

<body class="auth-body">

  <!-- Barra arriba -->
  <header class="public-topbar auth-topbar">
    <div class="brand">Capital Humano</div>
    <nav class="public-nav">
      <a class="nav-link" href="home.php#beneficios">Beneficios</a>
      <a class="nav-link" href="home.php#modulos">Módulos</a>
      <a class="nav-link" href="home.php#seguridad">Seguridad</a>
      <a class="btn primary" href="home.php">Inicio</a>
    </nav>
  </header>

  <!-- Contenedor para centrar SOLO el card -->
  <main class="auth-wrap">
    <div class="auth-card">
      <h1>Iniciar sesión</h1>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" class="auth-form" autocomplete="off">
        <label>Usuario</label>
        <input type="text" name="username" required placeholder="Tu usuario">

        <label>Contraseña</label>
        <input type="password" name="password" required placeholder="••••••••">

        <button class="btn auth-btn" type="submit">Ingresar</button>
      </form>

      <p class="auth-hint">Si no puedes ingresar, pide a RRHH que te regenere contraseña.</p>
    </div>
  </main>

  <!-- Footer centrado -->
  <footer class="auth-footer">
    © <?= date('Y') ?> Capital Humano
  </footer>

</body>
</html>
