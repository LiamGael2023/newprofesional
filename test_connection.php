<?php
/**
 * Script de prueba de conexión a la base de datos
 * Ejecutar desde línea de comandos: php test_connection.php
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

echo "========================================\n";
echo "Prueba de Conexión a la Base de Datos\n";
echo "========================================\n\n";

echo "Configuración:\n";
echo "- Host: " . DB_HOST . "\n";
echo "- Puerto: " . DB_PORT . "\n";
echo "- Base de Datos: " . DB_NAME . "\n";
echo "- Usuario: " . DB_USER . "\n";
echo "- Charset: " . DB_CHARSET . "\n\n";

echo "Intentando conectar...\n\n";

try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    echo "✓ Conexión exitosa!\n\n";

    // Verificar versión de MySQL
    $stmt = $pdo->query('SELECT VERSION() as version');
    $result = $stmt->fetch();
    echo "Versión MySQL: " . $result['version'] . "\n\n";

    // Verificar tablas existentes
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        echo "⚠ ADVERTENCIA: No se encontraron tablas en la base de datos.\n";
        echo "Por favor, importa el schema:\n";
        echo "mysql -u root -p --port=" . DB_PORT . " < database/schema.sql\n\n";
    } else {
        echo "Tablas encontradas (" . count($tables) . "):\n";
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
        echo "\n";

        // Verificar usuario admin
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM usuarios WHERE username = 'admin'");
        $result = $stmt->fetch();
        if ($result['count'] > 0) {
            echo "✓ Usuario administrador encontrado\n";
            echo "\n  Puedes acceder con:\n";
            echo "  Usuario: admin\n";
            echo "  Contraseña: admin123\n\n";
        } else {
            echo "⚠ No se encontró el usuario administrador\n\n";
        }

        // Verificar roles
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles");
        $result = $stmt->fetch();
        echo "✓ Roles configurados: " . $result['count'] . "\n";

        // Verificar tipos de aportación
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM tipos_aportacion");
        $result = $stmt->fetch();
        echo "✓ Tipos de aportación: " . $result['count'] . "\n\n";
    }

    echo "========================================\n";
    echo "La base de datos está lista para usar!\n";
    echo "========================================\n";

} catch (PDOException $e) {
    echo "✗ Error de conexión:\n";
    echo $e->getMessage() . "\n\n";

    echo "Posibles soluciones:\n";
    echo "1. Verifica que MySQL esté corriendo en el puerto " . DB_PORT . "\n";
    echo "2. Verifica el usuario y contraseña en config/config.php\n";
    echo "3. Asegúrate de que la base de datos '" . DB_NAME . "' existe\n";
    echo "4. Si usas XAMPP/WAMP, verifica el puerto en el panel de control\n\n";

    exit(1);
}
