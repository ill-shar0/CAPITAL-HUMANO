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
  <title>Login - Capital Humano</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body class="auth-body">

  <div class="auth-card">
    <div class="auth-brand">Capital Humano</div>
    <h1>Ingreso</h1>

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

    <p class="auth-hint">Si no puedes entrar, pide a RRHH que te regenere contraseña.</p>
  </div>
</body>
</html>
