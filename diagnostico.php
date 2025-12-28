<?php
/**
 * Diagnóstico Completo del Sistema
 * Verifica todos los componentes y muestra errores detallados
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<html><head><title>Diagnóstico del Sistema</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { background: white; padding: 20px; border-radius: 8px; max-width: 1200px; margin: 0 auto; }
h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
.step { margin: 20px 0; padding: 15px; border-left: 4px solid #3498db; background: #ecf0f1; }
.success { background: #d4edda; border-left-color: #28a745; color: #155724; }
.error { background: #f8d7da; border-left-color: #dc3545; color: #721c24; }
.warning { background: #fff3cd; border-left-color: #ffc107; color: #856404; }
.code { background: #2c3e50; color: #ecf0f1; padding: 10px; border-radius: 4px; overflow-x: auto; }
pre { margin: 0; }
.btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
.btn:hover { background: #2980b9; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🔍 Diagnóstico Completo del Sistema</h1>";

$errors = [];
$warnings = [];
$success = [];

// ==================== PASO 1: PHP ====================
echo "<div class='step'>";
echo "<h2>1️⃣ Verificando PHP</h2>";

$phpVersion = phpversion();
echo "<p><strong>Versión de PHP:</strong> $phpVersion</p>";

if (version_compare($phpVersion, '8.1.0', '>=')) {
    echo "<p class='success'>✓ PHP 8.1+ detectado</p>";
    $success[] = "PHP version correcta";
} else {
    echo "<p class='error'>✗ PHP 8.1+ requerido. Tienes: $phpVersion</p>";
    $errors[] = "PHP version incorrecta";
}

// Verificar extensiones
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json'];
echo "<p><strong>Extensiones requeridas:</strong></p><ul>";
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<li style='color: green;'>✓ $ext</li>";
    } else {
        echo "<li style='color: red;'>✗ $ext (FALTA)</li>";
        $errors[] = "Extensión $ext no encontrada";
    }
}
echo "</ul>";
echo "</div>";

// ==================== PASO 2: ARCHIVOS ====================
echo "<div class='step'>";
echo "<h2>2️⃣ Verificando Archivos del Sistema</h2>";

$root = __DIR__;
$required_files = [
    'config/config.php' => 'Configuración principal',
    'app/core/Database.php' => 'Clase Database',
    'app/core/Model.php' => 'Clase Model',
    'app/core/Controller.php' => 'Clase Controller',
    'app/core/Router.php' => 'Clase Router',
    'app/helpers/functions.php' => 'Funciones helper',
    'public/index.php' => 'Punto de entrada',
    'app/views/layouts/main.php' => 'Layout principal',
];

$missing_files = [];
echo "<ul>";
foreach ($required_files as $file => $desc) {
    $path = $root . '/' . $file;
    if (file_exists($path)) {
        echo "<li style='color: green;'>✓ $file - $desc</li>";
    } else {
        echo "<li style='color: red;'>✗ $file - $desc (NO ENCONTRADO)</li>";
        $missing_files[] = $file;
        $errors[] = "Archivo faltante: $file";
    }
}
echo "</ul>";

if (empty($missing_files)) {
    echo "<p class='success'>✓ Todos los archivos principales existen</p>";
} else {
    echo "<p class='error'>✗ Faltan " . count($missing_files) . " archivos</p>";
}
echo "</div>";

// ==================== PASO 3: CONFIGURACIÓN ====================
echo "<div class='step'>";
echo "<h2>3️⃣ Verificando Configuración</h2>";

if (file_exists($root . '/config/config.php')) {
    require_once $root . '/config/config.php';

    echo "<p><strong>Configuración cargada:</strong></p>";
    echo "<ul>";
    echo "<li>DB_HOST: " . (defined('DB_HOST') ? DB_HOST : '<span style="color:red;">NO DEFINIDO</span>') . "</li>";
    echo "<li>DB_PORT: " . (defined('DB_PORT') ? DB_PORT : '<span style="color:red;">NO DEFINIDO</span>') . "</li>";
    echo "<li>DB_NAME: " . (defined('DB_NAME') ? DB_NAME : '<span style="color:red;">NO DEFINIDO</span>') . "</li>";
    echo "<li>DB_USER: " . (defined('DB_USER') ? DB_USER : '<span style="color:red;">NO DEFINIDO</span>') . "</li>";
    echo "<li>APP_URL: " . (defined('APP_URL') ? APP_URL : '<span style="color:red;">NO DEFINIDO</span>') . "</li>";
    echo "</ul>";

    if (defined('DB_HOST') && defined('DB_PORT') && defined('DB_NAME') && defined('DB_USER')) {
        echo "<p class='success'>✓ Configuración básica OK</p>";
        $success[] = "Configuración cargada";
    } else {
        echo "<p class='error'>✗ Faltan constantes en config.php</p>";
        $errors[] = "Configuración incompleta";
    }
} else {
    echo "<p class='error'>✗ Archivo config/config.php NO ENCONTRADO</p>";
    $errors[] = "Archivo de configuración faltante";
}
echo "</div>";

// ==================== PASO 4: MYSQL ====================
echo "<div class='step'>";
echo "<h2>4️⃣ Verificando Conexión MySQL</h2>";

if (defined('DB_HOST') && defined('DB_PORT')) {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS ?? '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "<p class='success'>✓ Conexión al servidor MySQL exitosa</p>";

        // Verificar versión
        $stmt = $pdo->query('SELECT VERSION() as version');
        $version = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p><strong>Versión MySQL:</strong> " . $version['version'] . "</p>";

        // Verificar base de datos
        if (defined('DB_NAME')) {
            $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
            $dbExists = $stmt->fetch();

            if ($dbExists) {
                echo "<p class='success'>✓ Base de datos '" . DB_NAME . "' existe</p>";

                // Conectar a la BD
                $pdo->exec("USE " . DB_NAME);

                // Verificar tablas
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (count($tables) > 0) {
                    echo "<p class='success'>✓ Base de datos tiene " . count($tables) . " tablas</p>";
                    echo "<details><summary>Ver tablas</summary><ul>";
                    foreach ($tables as $table) {
                        echo "<li>$table</li>";
                    }
                    echo "</ul></details>";
                    $success[] = "Base de datos configurada";
                } else {
                    echo "<p class='error'>✗ Base de datos vacía (sin tablas)</p>";
                    echo "<p><strong>SOLUCIÓN:</strong> Importa el schema SQL</p>";
                    echo "<div class='code'><pre>mysql -u root -p --port=" . DB_PORT . " " . DB_NAME . " < database/schema.sql</pre></div>";
                    $errors[] = "Base de datos vacía";
                }
            } else {
                echo "<p class='error'>✗ Base de datos '" . DB_NAME . "' NO EXISTE</p>";
                echo "<p><strong>SOLUCIÓN:</strong> Importa el schema SQL</p>";
                echo "<div class='code'><pre>mysql -u root -p --port=" . DB_PORT . " < database/schema.sql</pre></div>";
                $errors[] = "Base de datos no existe";
            }
        }
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Error de conexión MySQL: " . $e->getMessage() . "</p>";
        echo "<p><strong>Posibles causas:</strong></p>";
        echo "<ul>";
        echo "<li>MySQL no está corriendo</li>";
        echo "<li>Puerto incorrecto (actual: " . DB_PORT . ")</li>";
        echo "<li>Usuario/contraseña incorrectos</li>";
        echo "</ul>";
        $errors[] = "No se puede conectar a MySQL";
    }
} else {
    echo "<p class='error'>✗ Configuración de base de datos incompleta</p>";
    $errors[] = "Configuración DB incompleta";
}
echo "</div>";

// ==================== PASO 5: PERMISOS ====================
echo "<div class='step'>";
echo "<h2>5️⃣ Verificando Permisos de Archivos</h2>";

$writable_dirs = ['logs'];
echo "<ul>";
foreach ($writable_dirs as $dir) {
    $path = $root . '/' . $dir;
    if (is_dir($path)) {
        if (is_writable($path)) {
            echo "<li style='color: green;'>✓ $dir/ (escritura OK)</li>";
        } else {
            echo "<li style='color: red;'>✗ $dir/ (sin permisos de escritura)</li>";
            $warnings[] = "Directorio $dir sin permisos de escritura";
        }
    } else {
        echo "<li style='color: orange;'>⚠ $dir/ (no existe, se creará automáticamente)</li>";
    }
}
echo "</ul>";
echo "</div>";

// ==================== PASO 6: .HTACCESS ====================
echo "<div class='step'>";
echo "<h2>6️⃣ Verificando Apache/Nginx</h2>";

if (file_exists($root . '/public/.htaccess')) {
    echo "<p class='success'>✓ Archivo .htaccess existe</p>";

    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if (in_array('mod_rewrite', $modules)) {
            echo "<p class='success'>✓ mod_rewrite habilitado</p>";
            $success[] = "mod_rewrite activo";
        } else {
            echo "<p class='error'>✗ mod_rewrite NO habilitado</p>";
            $errors[] = "mod_rewrite deshabilitado";
        }
    } else {
        echo "<p class='warning'>⚠ No se puede verificar mod_rewrite (posible Nginx o CLI)</p>";
    }
} else {
    echo "<p class='error'>✗ Archivo public/.htaccess NO ENCONTRADO</p>";
    $errors[] = ".htaccess faltante";
}
echo "</div>";

// ==================== RESUMEN ====================
echo "<div class='step'>";
echo "<h2>📊 Resumen del Diagnóstico</h2>";

$total = count($success) + count($errors) + count($warnings);

echo "<p><strong>Éxitos:</strong> <span style='color: green; font-size: 24px;'>" . count($success) . "</span></p>";
echo "<p><strong>Errores:</strong> <span style='color: red; font-size: 24px;'>" . count($errors) . "</span></p>";
echo "<p><strong>Advertencias:</strong> <span style='color: orange; font-size: 24px;'>" . count($warnings) . "</span></p>";

if (count($errors) === 0) {
    echo "<div class='success' style='padding: 20px; margin: 20px 0;'>";
    echo "<h3>✅ ¡Sistema Listo!</h3>";
    echo "<p>Todos los componentes están funcionando correctamente.</p>";
    echo "<p><a href='" . (defined('APP_URL') ? APP_URL . '/public/' : '/public/') . "' class='btn'>Acceder al Sistema →</a></p>";
    echo "</div>";
} else {
    echo "<div class='error' style='padding: 20px; margin: 20px 0;'>";
    echo "<h3>❌ Problemas Encontrados</h3>";
    echo "<ol>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ol>";
    echo "</div>";
}

echo "</div>";

// ==================== SOLUCIONES RÁPIDAS ====================
if (count($errors) > 0) {
    echo "<div class='step'>";
    echo "<h2>🔧 Soluciones Rápidas</h2>";

    echo "<h3>Opción 1: Instalador Automático</h3>";
    echo "<p>Usa el instalador web para configurar todo automáticamente:</p>";
    echo "<div class='code'><pre>http://localhost/colegio/install_database.php</pre></div>";
    echo "<p><a href='install_database.php' class='btn'>Abrir Instalador →</a></p>";

    echo "<h3>Opción 2: Verificar MySQL</h3>";
    echo "<p>Ejecuta el verificador de puertos:</p>";
    echo "<div class='code'><pre>php check_mysql.php</pre></div>";

    echo "<h3>Opción 3: Comando Manual</h3>";
    echo "<p>Importa la base de datos manualmente:</p>";
    echo "<div class='code'><pre>mysql -u root -p --port=3307 < database/schema.sql</pre></div>";

    echo "</div>";
}

// ==================== INFORMACIÓN DEL SISTEMA ====================
echo "<div class='step'>";
echo "<h2>ℹ️ Información del Sistema</h2>";
echo "<ul>";
echo "<li><strong>Sistema Operativo:</strong> " . PHP_OS . "</li>";
echo "<li><strong>Servidor Web:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido') . "</li>";
echo "<li><strong>Ruta del proyecto:</strong> " . $root . "</li>";
echo "<li><strong>Hora del servidor:</strong> " . date('Y-m-d H:i:s') . "</li>";
echo "</ul>";
echo "</div>";

echo "</div>";
echo "</body></html>";
?>
