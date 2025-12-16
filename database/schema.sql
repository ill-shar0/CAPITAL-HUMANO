CREATE DATABASE capital_humano;
USE capital_humano;

CREATE TABLE IF NOT EXISTS usuarios (
  user_id VARCHAR(36) PRIMARY KEY DEFAULT (CAST(UUID_SHORT() AS CHAR)),
  username VARCHAR(100) NOT NULL UNIQUE,
  usu_password_hash VARCHAR(255) NOT NULL,
  usu_rol VARCHAR(30) NOT NULL,
  usu_estado_usuario VARCHAR(5) NOT NULL DEFAULT '1',
  usu_colab_id VARCHAR(36),
  usu_fecha_creacion VARCHAR(30),
  usu_ultima_actualizacion VARCHAR(30)
) ;

CREATE TABLE IF NOT EXISTS colaboradores (
  colab_id VARCHAR(36) PRIMARY KEY DEFAULT (CAST(UUID_SHORT() AS CHAR)),
  colab_primer_nombre VARCHAR(100) NOT NULL,
  segundo_nombre VARCHAR(100),
  colab_apellido_paterno VARCHAR(100) NOT NULL,
  colab_apellido_materno VARCHAR(100),
  colab_sexo VARCHAR(10),
  colab_cedula VARCHAR(50) UNIQUE,
  colab_fecha_nac VARCHAR(30),
  colab_correo VARCHAR(150),
  colab_telefono VARCHAR(50),
  colab_celular VARCHAR(50),
  colab_direccion VARCHAR(200),
  colab_foto_perfil VARCHAR(255),
  colab_car_sueldo VARCHAR(50),
  colab_car_cargo VARCHAR(100),
  colab_estado_colaborador VARCHAR(30) NOT NULL DEFAULT 'Activo', 
  colab_fecha_creacion VARCHAR(30), 
  colab_ultima_actualizacion VARCHAR(30)
);

CREATE TABLE IF NOT EXISTS historial_colaboradores (
  historial_id VARCHAR(36) PRIMARY KEY DEFAULT (CAST(UUID_SHORT() AS CHAR)),
  his_col_id VARCHAR(36),
  his_col_primer_nombre VARCHAR(100),
  his_col_segundo_nombre VARCHAR(100),
  his_col_apellido_paterno VARCHAR(100),
  his_col_apellido_materno VARCHAR(100),
  his_col_sexo VARCHAR(10), --
  his_col_cedula VARCHAR(50),
  his_col_fecha_nac VARCHAR(30),
  his_col_correo VARCHAR(150),
  his_col_telefono VARCHAR(50),
  his_col_celular VARCHAR(50),
  his_col_direccion VARCHAR(200),
  his_col_foto_perfil VARCHAR(255),
  his_col_car_sueldo VARCHAR(50),
  his_col_car_cargo VARCHAR(100),
  his_col_estado_colaborador VARCHAR(30),
  his_col_fecha_creacion VARCHAR(30),
  his_col_ultima_actualizacion VARCHAR(30),
  his_col_fecha_salida VARCHAR(30)
);

CREATE TABLE IF NOT EXISTS cargos (
  cargo_id VARCHAR(36) PRIMARY KEY DEFAULT (CAST(UUID_SHORT() AS CHAR)),
  carg_nombre_cargo VARCHAR(150) NOT NULL,
  carg_departamento_cargo VARCHAR(150),
  carg_sueldo_cargo VARCHAR(50),
  carg_ocupacion VARCHAR(255),
  carg_fecha_creacion VARCHAR(30),
  carg_ultima_actualizacion VARCHAR(30)
) ;

CREATE TABLE IF NOT EXISTS colaborador_cargo (
  col_cargo_id VARCHAR(36) PRIMARY KEY DEFAULT (CAST(UUID_SHORT() AS CHAR)),
  col_carg_periodo VARCHAR(20),
  col_carg_activo VARCHAR(5) DEFAULT '1',
  col_carg_fecha_creacion VARCHAR(30),
  col_carg_ultima_actualizacion VARCHAR(30)
) ;

CREATE TABLE IF NOT EXISTS asistencias (
  asis_id VARCHAR(36) PRIMARY KEY DEFAULT (CAST(UUID_SHORT() AS CHAR)),
  asis_colab_id VARCHAR(36) NOT NULL,
  asis_fecha VARCHAR(30) NOT NULL,
  asis_hora_entrada VARCHAR(20),
  asis_hora_salida VARCHAR(20),
  asis_fecha_creacion VARCHAR(30),
  asis_ultima_actualizacion VARCHAR(30)
) ;

CREATE TABLE IF NOT EXISTS vacaciones (
  vac_id VARCHAR(36) PRIMARY KEY DEFAULT (CAST(UUID_SHORT() AS CHAR)),
  vac_colab_id VARCHAR(36) NOT NULL,
  vac_dias_trabajados VARCHAR(20) DEFAULT '0',
  vac_dias_vacaciones_validos VARCHAR(20) DEFAULT '0',
  vac_estado_vacaciones VARCHAR(20) DEFAULT 'No válido',
  vac_dias_vacaciones_tomados VARCHAR(20) DEFAULT '0',
  vac_fecha_creacion VARCHAR(30),
  vac_ultima_actualizacion VARCHAR(30)
) ;

CREATE TABLE IF NOT EXISTS resueltos (
  resuelto_id VARCHAR(36) PRIMARY KEY DEFAULT (CAST(UUID_SHORT() AS CHAR)),
  resu_colab_id VARCHAR(36) NOT NULL,
  resu_dias_vacaciones VARCHAR(20),
  resu_periodo_inicio VARCHAR(30),
  resu_periodo_fin VARCHAR(30),
  resu_pdf_path VARCHAR(255),
  resu_fecha_creacion VARCHAR(30),
  resu_ultima_actualizacion VARCHAR(30)
) ;

CREATE TABLE IF NOT EXISTS auditoria (
  aud_id VARCHAR(36) PRIMARY KEY DEFAULT (CAST(UUID_SHORT() AS CHAR)),
  aud_actor_user_id VARCHAR(36),
  aud_target_tipo VARCHAR(50),
  aud_target_id VARCHAR(36),
  aud_detalle VARCHAR(255),
  aud_fecha VARCHAR(30)
) ;

CREATE INDEX idx_usuarios_username ON usuarios (username);
CREATE INDEX idx_colaboradores_cedula ON colaboradores (colab_cedula);
CREATE INDEX idx_asistencias_colab_fecha ON asistencias (asis_colab_id, asis_fecha);




-- =========================
-- Inserciones para pruebas
-- =========================
-- Colaboradores
-- =========================
SET @colab_admin := CAST(UUID_SHORT() AS CHAR);
INSERT INTO colaboradores (
  colab_id, colab_primer_nombre, segundo_nombre, colab_apellido_paterno, colab_apellido_materno,
  colab_sexo, colab_cedula, colab_fecha_nac, colab_correo, colab_telefono, colab_celular,
  colab_direccion, colab_foto_perfil, colab_car_sueldo, colab_car_cargo,
  colab_estado_colaborador, colab_fecha_creacion, colab_ultima_actualizacion
) VALUES (
  @colab_admin, 'Juan', 'Marciso', 'Perez', 'Gonzales',
  'M', '8-501-1234', '1985-03-14', 'juan.perez@demo.com', '2301-1111', '6500-1111',
  'Ciudad de Panamá, Calle 50, Torre Apto 10B', 'uploads/fotos/juan_perez.jpg', '2500', 'Director TI',
  'Activo', NOW(), NOW()
);

SET @colab_rrhh := CAST(UUID_SHORT() AS CHAR);
INSERT INTO colaboradores (
  colab_id, colab_primer_nombre, segundo_nombre, colab_apellido_paterno, colab_apellido_materno,
  colab_sexo, colab_cedula, colab_fecha_nac, colab_correo, colab_telefono, colab_celular,
  colab_direccion, colab_foto_perfil, colab_car_sueldo, colab_car_cargo,
  colab_estado_colaborador, colab_fecha_creacion, colab_ultima_actualizacion
) VALUES (
  @colab_rrhh, 'Ana', 'Maria', 'Peña', 'Valdez',
  'F', '8-601-2345', '1988-07-22', 'ana.pena@demo.com', '2301-2222', '6500-2222',
  'Panamá Oeste, Arraiján, Calle 3', 'uploads/fotos/ana_pena.jpg', '1800', 'Jefa RRHH',
  'Activo', NOW(), NOW()
);

SET @colab_14 := CAST(UUID_SHORT() AS CHAR);
INSERT INTO colaboradores (
  colab_id, colab_primer_nombre, segundo_nombre, colab_apellido_paterno, colab_apellido_materno,
  colab_sexo, colab_cedula, colab_fecha_nac, colab_correo, colab_telefono, colab_celular,
  colab_direccion, colab_foto_perfil, colab_car_sueldo, colab_car_cargo,
  colab_estado_colaborador, colab_fecha_creacion, colab_ultima_actualizacion
) VALUES (
  @colab_14, 'Luis', 'Alberto', 'Castro', 'Rios',
  'M', '8-701-3456', '1990-11-05', 'luis.castro@demo.com', '2301-3333', '6500-3333',
  'San Miguelito, Brisas del Golf, Casa 12', 'uploads/fotos/luis_castro.jpg', '1200', 'Analista',
  'Activo', NOW(), NOW()
);

SET @colab_30 := CAST(UUID_SHORT() AS CHAR);
INSERT INTO colaboradores (
  colab_id, colab_primer_nombre, segundo_nombre, colab_apellido_paterno, colab_apellido_materno,
  colab_sexo, colab_cedula, colab_fecha_nac, colab_correo, colab_telefono, colab_celular,
  colab_direccion, colab_foto_perfil, colab_car_sueldo, colab_car_cargo,
  colab_estado_colaborador, colab_fecha_creacion, colab_ultima_actualizacion
) VALUES (
  @colab_30, 'Mariana', 'Elena', 'Torres', 'Diaz',
  'F', '8-801-4567', '1992-02-18', 'mariana.torres@demo.com', '2301-4444', '6500-4444',
  'Panamá, Costa del Este, PH Vista', 'uploads/fotos/mariana_torres.jpg', '1500', 'Coordinadora',
  'Activo', NOW(), NOW()
);


INSERT INTO usuarios (
  user_id, username, usu_password_hash, usu_rol,
  usu_estado_usuario, usu_colab_id, usu_fecha_creacion, usu_ultima_actualizacion
) VALUES (
  CAST(UUID_SHORT() AS CHAR),
  'j.perez',
  '$2y$10$851PfJUKpz1OVZoC0HpJTuHcvjTGpO5Aark7hTYk3cstUGb8MHPee', -- AdminPass123
  'administrador',
  '1',
  @colab_admin,
  NOW(),
  NOW()
);

INSERT INTO usuarios (
  user_id, username, usu_password_hash, usu_rol,
  usu_estado_usuario, usu_colab_id, usu_fecha_creacion, usu_ultima_actualizacion
) VALUES (
  CAST(UUID_SHORT() AS CHAR),
  'a.pena',
  '$2y$10$G3ygxqdMPL78AJgk7juUs.jGYZ0l.iqPWw2h5y7.X2GhF.VRuwA02', -- recursos12345
  'recursos_humanos',
  '1',
  @colab_rrhh,
  NOW(),
  NOW()
);

INSERT INTO usuarios (
  user_id, username, usu_password_hash, usu_rol,
  usu_estado_usuario, usu_colab_id, usu_fecha_creacion, usu_ultima_actualizacion
) VALUES (
  CAST(UUID_SHORT() AS CHAR),
  'l.castro',
  '$2y$10$Jx6SQK1v3WJ2mEFjYL0z7uxQHY9ORl5dDGVh2Uw8A7G6zeiZKlQeq', -- colaborador12345
  'colaborador',
  '1',
  @colab_14,
  NOW(),
  NOW()
);

INSERT INTO usuarios (
  user_id, username, usu_password_hash, usu_rol,
  usu_estado_usuario, usu_colab_id, usu_fecha_creacion, usu_ultima_actualizacion
) VALUES (
  CAST(UUID_SHORT() AS CHAR),
  'm.torres',
  '$2y$10$Jx6SQK1v3WJ2mEFjYL0z7uxQHY9ORl5dDGVh2Uw8A7G6zeiZKlQeq', -- colaborador12345
  'colaborador',
  '1',
  @colab_30,
  NOW(),
  NOW()
);

-- =========================
-- Vacaciones (uno con 14 días válidos, otro con 30)
-- =========================
INSERT INTO vacaciones (
  vac_id, vac_colab_id, vac_dias_trabajados,
  vac_dias_vacaciones_validos, vac_estado_vacaciones,
  vac_dias_vacaciones_tomados, vac_fecha_creacion, vac_ultima_actualizacion
) VALUES (
  CAST(UUID_SHORT() AS CHAR),
  @colab_14,
  '220',   -- días trabajados
  '14',    -- días válidos
  'Válido',
  '0',     -- tomados
  NOW(),
  NOW()
);

INSERT INTO vacaciones (
  vac_id, vac_colab_id, vac_dias_trabajados,
  vac_dias_vacaciones_validos, vac_estado_vacaciones,
  vac_dias_vacaciones_tomados, vac_fecha_creacion, vac_ultima_actualizacion
) VALUES (
  CAST(UUID_SHORT() AS CHAR),
  @colab_30,
  '365',
  '30',
  'Válido',
  '0',
  NOW(),
  NOW()
);
-- ==============================
-- Listado de usuarios y contraseñas
--===============================
-- Usuario / contraseña:
-- j.perez / AdminPass123
-- a.pena / recursos12345
-- l.castro / colaborador12345
-- m.torres / colaborador12345
-- Hashes bcrypt ya generados
-- ==============================