<?php
/**
 * Configuración del Sistema - EJEMPLO
 * Copiar este archivo a config.php y ajustar los valores
 */

// Configuración de Base de Datos
// IMPORTANTE: Usar 127.0.0.1 en lugar de localhost para conexión TCP/IP
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306'); // Puerto común: 3306. En XAMPP con Apache puede ser 3307
define('DB_NAME', 'colegio_profesional');
define('DB_USER', 'root');
define('DB_PASS', ''); // Cambiar si tu MySQL tiene contraseña
define('DB_CHARSET', 'utf8mb4');

// Configuración de la Aplicación
define('APP_NAME', 'Sistema de Gestión - Colegio Profesional');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/newprofesional');
define('APP_ENV', 'development'); // development, production

// Rutas del Sistema
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Configuración de Sesión
define('SESSION_NAME', 'COLEGIO_SESSION');
define('SESSION_LIFETIME', 7200); // 2 horas en segundos

// Configuración de Seguridad
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 10);

// Zona Horaria
date_default_timezone_set('America/Lima');

// Configuración de Errores
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/error.log');
}

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/core/',
        APP_PATH . '/helpers/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
