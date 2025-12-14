<?php
require_once __DIR__ . '/../config/app.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Capital Humano | Sistema de Gestión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/main.css">
</head>

<body class="public-body">

  <!-- Topbar pública -->
  <header class="public-topbar">
    <div class="brand">Capital Humano</div>

    <nav class="public-nav">
      <a class="nav-link" href="#beneficios">Beneficios</a>
      <a class="nav-link" href="#modulos">Módulos</a>
      <a class="nav-link" href="#seguridad">Seguridad</a>
      <a class="btn primary" href="login.php">Ingresar</a>
    </nav>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div class="hero-content">
      <h1>Gestiona tu talento de forma simple, segura y profesional</h1>
      <p>
        Un sistema integral para administrar colaboradores, cargos, vacaciones, reportes y auditoría.
        Diseñado para equipos de RRHH que necesitan control, trazabilidad y rapidez.
      </p>

      <div class="hero-actions">
        <a class="btn primary" href="#demo">Solicitar demo</a>
        <a class="btn secondary" href="#modulos">Ver módulos</a>
      </div>

      <div class="hero-stats">
        <div class="stat">
          <div class="stat-number">+12</div>
          <div class="stat-label">Módulos integrados</div>
        </div>
        <div class="stat">
          <div class="stat-number">100%</div>
          <div class="stat-label">Trazabilidad (auditoría)</div>
        </div>
        <div class="stat">
          <div class="stat-number">1</div>
          <div class="stat-label">Plataforma central</div>
        </div>
      </div>
    </div>

    <div class="hero-card">
      <h3>¿Qué resuelve?</h3>
      <ul class="checklist">
        <li>Control de colaboradores y cargos.</li>
        <li>Solicitudes y gestión de vacaciones.</li>
        <li>Roles y permisos por perfil.</li>
        <li>Registro de actividad (auditoría).</li>
        <li>Reportes y estadísticas para decisiones.</li>
      </ul>

      <div class="hero-card-cta" id="demo">
        <p class="small-muted">¿Listo para verlo en acción?</p>
        <a class="btn primary" href="login.php">Entrar al sistema</a>
      </div>
    </div>
  </section>

  <!-- Beneficios -->
  <section class="section" id="beneficios">
    <div class="section-header">
      <h2>Beneficios para RRHH</h2>
      <p class="help-text">Más organización, menos papeles, mejor control.</p>
    </div>

    <div class="grid-cards">
      <div class="card">
        <h3>Centralización</h3>
        <p>Todo en un solo lugar: colaboradores, cargos, estados, solicitudes y reportes.</p>
      </div>
      <div class="card">
        <h3>Control y permisos</h3>
        <p>Roles para asegurar que cada usuario solo vea lo que le corresponde.</p>
      </div>
      <div class="card">
        <h3>Rapidez</h3>
        <p>Procesos ágiles para gestionar vacaciones y consultar información en segundos.</p>
      </div>
      <div class="card">
        <h3>Auditoría</h3>
        <p>Registro de acciones críticas para trazabilidad y cumplimiento.</p>
      </div>
      <div class="card">
        <h3>Reportes</h3>
        <p>Datos claros para tomar decisiones: resumen por áreas, tendencias, totales.</p>
      </div>
      <div class="card">
        <h3>Escalable</h3>
        <p>Estructura modular para ir agregando funcionalidades sin romper el sistema.</p>
      </div>
    </div>
  </section>

  <!-- Módulos -->
  <section class="section" id="modulos">
    <div class="section-header">
      <h2>Módulos principales</h2>
      <p class="help-text">Componentes clave del sistema de Capital Humano.</p>
    </div>

    <div class="modules">
      <div class="module">Usuarios Administrativos</div>
      <div class="module">Roles y Permisos</div>
      <div class="module">Colaboradores</div>
      <div class="module">Cargos</div>
      <div class="module">Estatus</div>
      <div class="module">Vacaciones</div>
      <div class="module">Reportes</div>
      <div class="module">Estadísticas</div>
      <div class="module">Auditoría</div>
      <div class="module">Seguridad</div>
      <div class="module">Configuración</div>
      <div class="module">Soporte / Ayuda</div>
    </div>
  </section>

  <!-- Seguridad -->
  <section class="section" id="seguridad">
    <div class="section-header">
      <h2>Seguridad y buenas prácticas</h2>
      <p class="help-text">Diseñado para proteger datos sensibles de RRHH.</p>
    </div>

    <div class="grid-cards">
      <div class="card">
        <h3>Autenticación</h3>
        <p>Ingreso controlado por usuario y contraseña, con validación desde servidor.</p>
      </div>
      <div class="card">
        <h3>Sesiones</h3>
        <p>Acceso protegido por sesión: el sistema bloquea páginas privadas sin login.</p>
      </div>
      <div class="card">
        <h3>Auditoría</h3>
        <p>Registro de acciones críticas: quién hizo qué y cuándo.</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="public-footer">
    <div>
      <strong>Capital Humano</strong> — Sistema de gestión para RRHH.
    </div>
    <div class="small-muted">
      © <?= date('Y') ?> • Demo académica
    </div>
  </footer>

</body>
</html>
