# Sistema de Gestión de Capital Humano

## Descripción del Proyecto

El **Sistema de Gestión de Capital Humano** es una aplicación web desarrollada para facilitar la administración de colaboradores dentro de una organización. El sistema permite gestionar información de colaboradores, registrar asistencias (entradas y salidas), administrar vacaciones y controlar el acceso mediante roles de usuario, garantizando seguridad, trazabilidad y eficiencia en los procesos administrativos.

El proyecto ha sido desarrollado como parte de un trabajo académico, aplicando principios de arquitectura MVC, buenas prácticas de programación y modelado de software.

---

## Resumen – Requisitos del Sistema

### Requisitos de Software
Para el correcto funcionamiento del sistema se requiere el siguiente entorno:

- **Sistema Operativo**: Windows, Linux o macOS  
- **Servidor Web**: Apache (recomendado mediante WAMP, XAMPP o LAMP)
- **Lenguaje de Programación**: PHP 8.0 o superior
- **Base de Datos**: MySQL o MariaDB
- **Gestor de Base de Datos**: phpMyAdmin (opcional, recomendado)
- **Navegador Web**: Google Chrome, Mozilla Firefox o Microsoft Edge
- **Extensiones PHP habilitadas**:
  - PDO
  - PDO_MySQL
  - OpenSSL
  - mbstring
- **Dependencias externas**:
  - DomPDF (para la generación de documentos PDF)

### Requisitos de Hardware
- Procesador: 1.6 GHz o superior
- Memoria RAM: 4 GB mínimo
- Espacio en disco: 1 GB libre
- Conexión a red local o internet (según el entorno de uso)

---

## Funcionalidades Principales

- Gestión de colaboradores (crear, editar, visualizar y desactivar)
- Registro de asistencias (entrada y salida)
- Gestión de vacaciones y generación de resueltos en PDF
- Control de acceso basado en roles:
  - Administrador
  - Recursos Humanos
  - Colaborador
- Auditoría de acciones realizadas en el sistema
- Visualización de historial de asistencias
- Interfaz web amigable y estructurada

---

## Arquitectura del Sistema

El sistema sigue el patrón **Modelo – Vista – Controlador (MVC)**:

- **Modelos**: Lógica de negocio y acceso a datos
- **Vistas**: Interfaz de usuario
- **Controladores**: Manejo de peticiones y coordinación entre vistas y modelos

---

## Instalación

1. Clonar o copiar el proyecto en el directorio del servidor web: C:\wamp64\www\CAPITAL-HUMANO
2. Crear la base de datos en MySQL e importar el script SQL proporcionado.
3. Configurar la conexión a la base de datos en: config/db.php
4. Verificar que las extensiones necesarias de PHP estén habilitadas.
5. Instalar DomPDF (si no está incluido): composer require dompdf/dompdf
6. Acceder al sistema desde el navegador: http://localhost/CAPITAL-HUMANO/public

## Estructura del Proyecto

CAPITAL-HUMANO/
│
├── config/          # Configuración general y conexión a la BD
├── controllers/     # Controladores del sistema
├── models/          # Modelos de datos
├── views/           # Vistas del sistema
├── public/          # Punto de entrada y recursos públicos
├── services/        # Servicios (autorización, contraseñas, auditoría)
├── middleware/      # Autenticación y validaciones
├── public/          # Archivos principales del sistema
└── routes.php       # Definición de rutas

## Seguridad

- **Autenticación** de usuarios
- **Autorización** por roles
- **Validación** de formularios
- **Protección** contra accesos no autorizados

## Autores
Proyecto desarrollado por estudiantes de Ingeniería de Software del Grupo 1SF131, II Semestre del 2025:
-Chong, Stephany, 8-1010-2350
-Luo, Anie, 8-1016-414
-Takata, Gabriela, 8-991-822
-Zheng, Alexandra, 8-1009-1083
