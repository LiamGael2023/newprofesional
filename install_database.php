<?php
/**
 * Instalador automático de la base de datos
 * Ejecutar desde navegador: http://localhost/newprofesional/install_database.php
 */

// Solo permitir en modo desarrollo
require_once __DIR__ . '/config/config.php';

if (APP_ENV !== 'development') {
    die('Este script solo se puede ejecutar en modo desarrollo.');
}

// Función para mostrar mensajes
function showMessage($message, $type = 'info') {
    $colors = [
        'success' => '#27ae60',
        'error' => '#e74c3c',
        'warning' => '#f39c12',
        'info' => '#3498db'
    ];
    $color = $colors[$type] ?? $colors['info'];
    echo "<div style='padding: 10px; margin: 10px 0; background: {$color}; color: white; border-radius: 4px;'>";
    echo $message;
    echo "</div>";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador de Base de Datos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f4f6f9;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #ecf0f1;
            border-left: 4px solid #3498db;
        }
        .code {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            overflow-x: auto;
        }
        button {
            background: #27ae60;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        button:hover {
            background: #229954;
        }
        .danger-button {
            background: #e74c3c;
        }
        .danger-button:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 Instalador de Base de Datos</h1>
        <p>Sistema de Gestión de Colegio Profesional</p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<div class='step'><h3>Ejecutando instalación...</h3></div>";

            try {
                // Conectar al servidor MySQL
                $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHARSET;
                $pdo = new PDO($dsn, DB_USER, DB_PASS);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                showMessage("✓ Conexión al servidor MySQL exitosa", 'success');

                // Verificar si la base de datos existe y si hay que eliminarla
                if (isset($_POST['drop_existing'])) {
                    $pdo->exec("DROP DATABASE IF EXISTS " . DB_NAME);
                    showMessage("✓ Base de datos anterior eliminada", 'warning');
                }

                // Crear la base de datos
                $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                showMessage("✓ Base de datos '" . DB_NAME . "' creada", 'success');

                // Seleccionar la base de datos
                $pdo->exec("USE " . DB_NAME);

                // Leer y ejecutar el archivo SQL
                $sqlFile = __DIR__ . '/database/schema.sql';
                if (!file_exists($sqlFile)) {
                    throw new Exception("No se encontró el archivo database/schema.sql");
                }

                $sql = file_get_contents($sqlFile);

                // Eliminar comentarios y dividir en statements
                $sql = preg_replace('/--.*$/m', '', $sql);
                $sql = preg_replace('/#.*$/m', '', $sql);

                // Ejecutar el SQL completo
                $pdo->exec($sql);

                showMessage("✓ Schema SQL ejecutado correctamente", 'success');

                // Verificar que todo se haya creado
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

                showMessage("✓ Tablas creadas: " . count($tables), 'success');

                // Verificar usuario admin
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM usuarios WHERE username = 'admin'");
                $adminExists = $stmt->fetch()['count'] > 0;

                if ($adminExists) {
                    showMessage("✓ Usuario administrador creado correctamente", 'success');
                    echo "<div style='margin: 20px 0; padding: 20px; background: #d4edda; border: 2px solid #27ae60; border-radius: 4px;'>";
                    echo "<h3 style='color: #155724; margin-top: 0;'>¡Instalación Completada!</h3>";
                    echo "<p>Puedes acceder al sistema con las siguientes credenciales:</p>";
                    echo "<div class='code'>";
                    echo "URL: " . APP_URL . "/public/<br>";
                    echo "Usuario: admin<br>";
                    echo "Contraseña: admin123";
                    echo "</div>";
                    echo "<p style='margin-top: 15px;'><strong>IMPORTANTE:</strong> Por seguridad, elimina o renombra este archivo (install_database.php) después de la instalación.</p>";
                    echo "<a href='" . APP_URL . "/public/' style='display: inline-block; margin-top: 10px; padding: 12px 24px; background: #27ae60; color: white; text-decoration: none; border-radius: 4px;'>Ir al Sistema →</a>";
                    echo "</div>";
                } else {
                    showMessage("⚠ No se pudo crear el usuario administrador. Verifica el archivo schema.sql", 'warning');
                }

            } catch (Exception $e) {
                showMessage("✗ Error: " . $e->getMessage(), 'error');
                echo "<div class='code' style='margin-top: 10px;'>";
                echo htmlspecialchars($e->getTraceAsString());
                echo "</div>";
            }

        } else {
            // Mostrar formulario
            ?>
            <div class="step">
                <h3>Configuración Actual:</h3>
                <p><strong>Host:</strong> <?= DB_HOST ?></p>
                <p><strong>Puerto:</strong> <?= DB_PORT ?></p>
                <p><strong>Base de Datos:</strong> <?= DB_NAME ?></p>
                <p><strong>Usuario:</strong> <?= DB_USER ?></p>
            </div>

            <div class="step">
                <h3>⚠️ Advertencia</h3>
                <p>Este script creará/recreará la base de datos <strong><?= DB_NAME ?></strong>.</p>
                <p>Si marcas la opción "Eliminar base de datos existente", se borrarán todos los datos actuales.</p>
            </div>

            <form method="POST">
                <label style="display: block; margin: 20px 0;">
                    <input type="checkbox" name="drop_existing" value="1">
                    <strong style="color: #e74c3c;">Eliminar base de datos existente (si existe)</strong>
                </label>

                <button type="submit">Instalar Base de Datos</button>
            </form>

            <div class="step" style="margin-top: 30px;">
                <h3>Instalación Manual (Alternativa)</h3>
                <p>Si prefieres instalar manualmente, ejecuta estos comandos:</p>
                <div class="code">
                    mysql -u root -p --port=<?= DB_PORT ?> &lt; database/schema.sql
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
