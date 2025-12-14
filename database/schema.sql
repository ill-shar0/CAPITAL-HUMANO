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
  colab_ primer_nombre VARCHAR(100) NOT NULL,
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
  his_col_sexo VARCHAR(10),
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
  col_carg_id VARCHAR(36) NOT NULL,
  cal_carg_id VARCHAR(36) NOT NULL,
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
CREATE INDEX idx_colaboradores_cedula ON colaboradores (cedula);
CREATE INDEX idx_asistencias_colab_fecha ON asistencias (colab_id, fecha);

