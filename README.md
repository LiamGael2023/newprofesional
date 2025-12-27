# Sistema de Gestión de Colegio Profesional

Sistema completo de gestión para Colegios Profesionales desarrollado con PHP 8.1 MVC puro y MySQL 8.0.

> **🚀 [Guía de Instalación Rápida](INSTALL.md)** | **🔧 [Solución de Problemas](TROUBLESHOOTING.md)**

## Características Principales

### Stack Técnico
- **Backend**: PHP 8.1 con arquitectura MVC pura (sin frameworks)
- **Base de Datos**: MySQL 8.0
- **Frontend**: HTML5, CSS3, JavaScript vanilla

### Módulos Implementados

#### 📋 Módulo de Personas
- Registro completo de personas (datos personales, contacto, ubicación)
- Búsqueda y filtrado avanzado
- Validación de documentos únicos
- Gestión de estados (Activo/Inactivo)
- CRUD completo con auditoría

#### 🎓 Módulo de Colegiados
- Conversión de personas a colegiados
- Generación automática de código de colegiado (formato: CPYYYY####)
- Registro de datos profesionales (especialidad, universidad, títulos)
- Tipos de colegiatura: Ordinario, Vitalicio, Honorario
- Estados: Activo, Suspendido, Retirado, Inhabilitado
- Generación automática de derecho de incorporación

#### 💰 Módulo de Aportaciones/Cuotas
- Gestión de tipos de aportación (mensual, trimestral, semestral, anual, único)
- **Generación automática de cuotas mensuales** mediante stored procedure
- Cálculo automático de moras por días de retraso
- Gestión de descuentos
- Estados: Pendiente, Pagado, Vencido, Anulado
- Filtrado por periodo, estado y colegiado

#### 🏦 Módulo de Caja (Procesamiento de Pagos)
- Registro de pagos con múltiples métodos (Efectivo, Tarjeta, Transferencia, Depósito, Yape, Plin)
- Generación automática de número de recibo
- Pago de múltiples aportaciones en un solo recibo
- Anulación de pagos con motivo y trazabilidad
- Resumen de caja por periodo y método de pago
- Impresión de recibos

#### 📊 Módulo de Reportes
- **Reporte de Colegiados**: Listado completo con filtros y estadísticas
- **Reporte de Aportaciones**: Por periodo y estado
- **Reporte de Pagos/Recaudación**: Con totales por método de pago
- **Reporte de Morosidad**: Colegiados con cuotas vencidas y deudas
- **Reporte de Auditoría**: Seguimiento de todas las operaciones
- **Estadísticas Generales**: Dashboard con métricas clave

#### 🔐 Módulo de Seguridad
- Sistema de autenticación con hash bcrypt
- Gestión de roles y permisos (JSON-based)
- Roles predefinidos: Administrador, Contador, Secretario, Consulta
- Control de acceso basado en permisos
- Auditoría completa de operaciones
- Registro de IP y user agent
- Protección CSRF

## Estructura del Proyecto

```
newprofesional/
├── app/
│   ├── controllers/      # Controladores MVC
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── PersonasController.php
│   │   ├── ColegiadosController.php
│   │   ├── AportacionesController.php
│   │   ├── CajaController.php
│   │   └── ReportesController.php
│   ├── models/          # Modelos de datos
│   │   ├── Usuario.php
│   │   ├── Persona.php
│   │   ├── Colegiado.php
│   │   ├── TipoAportacion.php
│   │   ├── Aportacion.php
│   │   └── Pago.php
│   ├── views/           # Vistas
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── personas/
│   │   ├── colegiados/
│   │   ├── aportaciones/
│   │   ├── caja/
│   │   └── reportes/
│   ├── core/            # Núcleo del framework
│   │   ├── Database.php
│   │   ├── Model.php
│   │   ├── Controller.php
│   │   └── Router.php
│   └── helpers/         # Funciones auxiliares
│       └── functions.php
├── config/              # Configuración
│   └── config.php
├── database/            # Scripts SQL
│   └── schema.sql
├── public/              # Archivos públicos
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   ├── img/
│   ├── index.php       # Punto de entrada
│   └── .htaccess
├── logs/               # Archivos de log
└── README.md

```

## Instalación

### Requisitos Previos
- PHP 8.1 o superior
- MySQL 8.0 o superior
- Apache/Nginx con mod_rewrite habilitado
- Extensiones PHP: PDO, pdo_mysql

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <repository-url>
   cd newprofesional
   ```

2. **Configurar la base de datos**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

   Esto creará:
   - La base de datos `colegio_profesional`
   - Todas las tablas necesarias
   - Datos iniciales (roles, usuario admin, tipos de aportación, configuración)
   - Triggers para auditoría
   - Vistas útiles
   - Stored procedures

3. **Configurar la aplicación**

   Editar `config/config.php` con tus credenciales:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_PORT', '3306'); // Cambiar a 3307 si usas XAMPP/WAMP con puerto personalizado
   define('DB_NAME', 'colegio_profesional');
   define('DB_USER', 'root');
   define('DB_PASS', 'tu_password');
   define('APP_URL', 'http://localhost/newprofesional');
   ```

   **Nota:** Si usas el puerto 3307, asegúrate de importar la base de datos con:
   ```bash
   mysql -u root -p --port=3307 < database/schema.sql
   ```

4. **Configurar permisos**
   ```bash
   chmod -R 755 public
   chmod -R 775 logs
   ```

5. **Configurar Apache**

   Asegurarse de que el DocumentRoot apunte a la carpeta `public/` o ajustar la configuración en `.htaccess`

6. **Acceder al sistema**

   Abrir en el navegador: `http://localhost/newprofesional/public/`

   **Credenciales por defecto:**
   - Usuario: `admin`
   - Contraseña: `admin123`

## Base de Datos

### Tablas Principales

- **usuarios**: Usuarios del sistema con roles
- **roles**: Roles y permisos
- **personas**: Registro de personas
- **colegiados**: Colegiados profesionales
- **tipos_aportacion**: Tipos de cuotas/aportaciones
- **aportaciones**: Cuotas generadas
- **pagos**: Pagos procesados en caja
- **detalle_pagos**: Detalle de cada pago
- **auditoria**: Registro de operaciones
- **configuracion**: Parámetros del sistema

### Stored Procedures

- **sp_generar_cuotas_mensuales**: Genera automáticamente las cuotas mensuales para todos los colegiados activos
- **sp_calcular_mora**: Calcula y actualiza las moras de las aportaciones vencidas

### Triggers

- Auditoría automática en INSERT/UPDATE de personas
- Auditoría de cambios de estado en aportaciones
- Actualización automática de estado de aportación al registrar pago

## Funcionalidades Destacadas

### Generación Automática de Cuotas
```sql
CALL sp_generar_cuotas_mensuales('2025-01');
```
Genera automáticamente las cuotas del mes para todos los colegiados activos.

### Cálculo de Moras
```sql
CALL sp_calcular_mora();
```
Calcula y actualiza las moras según los días de retraso y la configuración del sistema.

### Sistema de Auditoría
Todas las operaciones importantes quedan registradas automáticamente con:
- Usuario que realizó la acción
- Fecha y hora
- Datos anteriores y nuevos (JSON)
- IP y user agent

### Control de Permisos
Sistema flexible basado en JSON que permite definir permisos granulares por rol:
```json
{
  "permisos": ["personas", "colegiados", "reportes"]
}
```

## Configuración del Sistema

Los parámetros del sistema se gestionan desde la tabla `configuracion`:

- **mora_diaria**: Monto de mora por día de retraso (S/ 2.00)
- **dias_vencimiento**: Días para vencimiento de cuotas (30)
- **generar_cuotas_auto**: Activar generación automática mensual
- **nombre_colegio**: Nombre de la institución
- **ruc**, **direccion**, **telefono**, **email**: Datos de contacto

## Seguridad

### Implementado
- Hash de contraseñas con bcrypt
- Validación de entrada en todos los formularios
- Protección contra SQL Injection (PDO con prepared statements)
- Protección XSS (escapado de salida con htmlspecialchars)
- Auditoría completa de operaciones
- Control de acceso basado en roles
- Sesiones seguras

### Headers de Seguridad
```apache
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
```

## Desarrollo

### Arquitectura MVC

**Model**: Gestión de datos y lógica de negocio
```php
class Persona extends Model {
    protected $table = 'personas';
    // Métodos específicos del modelo
}
```

**View**: Presentación de datos
```php
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
```

**Controller**: Lógica de control
```php
class PersonasController extends Controller {
    public function index() {
        $this->requirePermission('personas');
        // Lógica del controlador
    }
}
```

### Routing
El sistema utiliza un router simple basado en URL amigables:
```
/controller/method/param1/param2
```

Ejemplos:
- `/personas` → PersonasController::index()
- `/personas/edit/5` → PersonasController::edit(5)
- `/caja/recibo/123` → CajaController::recibo(123)

## Personalización

### Agregar un Nuevo Módulo

1. Crear el modelo en `app/models/`
2. Crear el controlador en `app/controllers/`
3. Crear las vistas en `app/views/`
4. Agregar permisos en la tabla `roles`
5. Agregar enlace en el menú (`app/views/layouts/main.php`)

### Agregar un Nuevo Reporte

1. Agregar método en `ReportesController`
2. Crear la vista en `app/views/reportes/`
3. Implementar la consulta SQL necesaria

## Mantenimiento

### Logs
Los logs del sistema se guardan en:
- `logs/app.log`: Log de aplicación
- `logs/error.log`: Errores PHP

### Backup de Base de Datos
```bash
mysqldump -u root -p colegio_profesional > backup_$(date +%Y%m%d).sql
```

### Limpieza de Auditoría
Se recomienda purgar registros antiguos de auditoría periódicamente:
```sql
DELETE FROM auditoria WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

## Soporte

Para reportar problemas o sugerencias, crear un issue en el repositorio.

## Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

## Créditos

Desarrollado como sistema completo de gestión para Colegios Profesionales.
- PHP 8.1 MVC Puro
- MySQL 8.0
- 2025
