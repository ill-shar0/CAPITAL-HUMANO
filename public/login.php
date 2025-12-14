<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/PasswordService.php';

if (current_user()) {
  header('Location: index.php');
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  $user = User::findByUsername($username);

  if ($user && ($user['estado_usuario'] ?? '0') === '1' && PasswordService::verify($password, $user['password_hash'])) {
    $_SESSION['user'] = [
      'user_id' => $user['user_id'],
      'username' => $user['username'],
      'rol' => $user['rol'],
      'colab_id' => $user['colab_id'] ?? null,
    ];
    header('Location: index.php');
    exit;
  }

  $error = 'Credenciales inválidas o usuario inactivo.';
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
<body class="login3-body">

  <header class="public-topbar">
    <div class="brand">Capital Humano</div>
    <nav class="public-nav">
      <a class="nav-link" href="home.php#beneficios">Beneficios</a>
      <a class="nav-link" href="home.php#modulos">Módulos</a>
      <a class="nav-link" href="home.php#seguridad">Seguridad</a>
      <a class="btn primary" href="home.php">Inicio</a>
    </nav>
  </header>

  <main class="login3-main">
    <section class="login3-panel">

      <h1 class="login3-title">Iniciar sesión</h1>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" class="login3-form" autocomplete="off">
        <label>Usuario</label>
        <input type="text" name="username" required placeholder="Tu usuario">

        <label>Contraseña</label>
        <input type="password" name="password" required placeholder="••••••••">

        <button class="btn blue" type="submit">Ingresar</button>
      </form>

      <p class="login3-foot">
        Si no puedes ingresar, pide a RRHH que te regenere contraseña.
      </p>

    </section>
  </main>

  <footer class="login3-footer">
    <span>© <?= date('Y') ?> Capital Humano</span>
  </footer>

</body>
</html>
