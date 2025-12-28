<?php
/**
 * Script para verificar y actualizar credenciales del administrador
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Actualizar Admin</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
.container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; margin: 10px 0; }
.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0; }
.info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 4px; margin: 10px 0; }
.btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px 0 0; border: none; cursor: pointer; font-size: 16px; }
.btn:hover { background: #2980b9; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
th { background: #3498db; color: white; }
code { background: #ecf0f1; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🔧 Actualizar Credenciales del Administrador</h1>";

// Cargar configuración
require_once __DIR__ . '/config/config.php';

try {
    // Conectar a la base de datos
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<div class='success'>✓ Conexión a base de datos exitosa</div>";

    // Verificar si existe el usuario admin
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        echo "<div class='info'><strong>Usuario admin encontrado:</strong></div>";
        echo "<table>";
        echo "<tr><th>Campo</th><th>Valor Actual</th></tr>";
        echo "<tr><td>ID</td><td>" . htmlspecialchars($admin['id']) . "</td></tr>";
        echo "<tr><td>Username</td><td>" . htmlspecialchars($admin['username']) . "</td></tr>";
        echo "<tr><td>Email</td><td>" . htmlspecialchars($admin['email']) . "</td></tr>";
        echo "<tr><td>Rol ID</td><td>" . htmlspecialchars($admin['rol_id']) . "</td></tr>";
        echo "<tr><td>Estado</td><td>" . htmlspecialchars($admin['estado']) . "</td></tr>";
        echo "<tr><td>Hash Actual</td><td><code style='font-size: 11px;'>" . htmlspecialchars(substr($admin['password'], 0, 50)) . "...</code></td></tr>";
        echo "</table>";

        // Si se envió el formulario para actualizar
        if (isset($_POST['update_password'])) {
            $newPassword = $_POST['new_password'];
            $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 10]);

            $updateStmt = $pdo->prepare("UPDATE usuarios SET password = ?, estado = 'ACTIVO' WHERE username = 'admin'");
            $updateStmt->execute([$newHash]);

            echo "<div class='success'>";
            echo "<h3>✅ Contraseña actualizada exitosamente!</h3>";
            echo "<p><strong>Nuevas credenciales:</strong></p>";
            echo "<ul>";
            echo "<li><strong>Username:</strong> <code>admin</code></li>";
            echo "<li><strong>Password:</strong> <code>" . htmlspecialchars($newPassword) . "</code></li>";
            echo "<li><strong>Nuevo Hash:</strong> <code style='font-size: 11px;'>" . htmlspecialchars(substr($newHash, 0, 50)) . "...</code></li>";
            echo "</ul>";
            echo "<a href='" . APP_URL . "/public/' class='btn'>Ir al Sistema →</a>";
            echo "</div>";

        } else {
            // Mostrar formulario para actualizar contraseña
            echo "<div class='info'>";
            echo "<h3>Actualizar Contraseña</h3>";
            echo "<p>Ingresa la nueva contraseña para el usuario admin:</p>";
            echo "<form method='POST' action=''>";
            echo "<input type='password' name='new_password' placeholder='Nueva contraseña' required style='padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 4px;'>";
            echo "<button type='submit' name='update_password' class='btn'>Actualizar Contraseña</button>";
            echo "</form>";
            echo "</div>";

            // Botón rápido para admin123
            echo "<div class='info'>";
            echo "<h3>Opción Rápida</h3>";
            echo "<p>O usa la contraseña predeterminada <code>admin123</code>:</p>";
            echo "<form method='POST' action=''>";
            echo "<input type='hidden' name='new_password' value='admin123'>";
            echo "<button type='submit' name='update_password' class='btn'>Configurar contraseña como 'admin123'</button>";
            echo "</form>";
            echo "</div>";
        }

    } else {
        echo "<div class='error'>✗ Usuario admin NO encontrado en la base de datos</div>";

        // Ofrecer crear el usuario
        if (isset($_POST['create_admin'])) {
            // Primero verificar que existe el rol de administrador
            $stmt = $pdo->prepare("SELECT id FROM roles WHERE nombre = 'Administrador' LIMIT 1");
            $stmt->execute();
            $rol = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$rol) {
                echo "<div class='error'>✗ No existe el rol 'Administrador'. Debes importar el schema.sql completo.</div>";
            } else {
                $password = 'admin123';
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

                $insertStmt = $pdo->prepare("
                    INSERT INTO usuarios (username, email, password, rol_id, estado)
                    VALUES (?, ?, ?, ?, 'ACTIVO')
                ");
                $insertStmt->execute(['admin', 'admin@colegio.pe', $hash, $rol['id']]);

                echo "<div class='success'>";
                echo "<h3>✅ Usuario administrador creado exitosamente!</h3>";
                echo "<p><strong>Credenciales:</strong></p>";
                echo "<ul>";
                echo "<li><strong>Username:</strong> <code>admin</code></li>";
                echo "<li><strong>Password:</strong> <code>admin123</code></li>";
                echo "<li><strong>Email:</strong> <code>admin@colegio.pe</code></li>";
                echo "</ul>";
                echo "<a href='" . APP_URL . "/public/' class='btn'>Ir al Sistema →</a>";
                echo "</div>";
            }

        } else {
            echo "<div class='info'>";
            echo "<h3>Crear Usuario Administrador</h3>";
            echo "<form method='POST' action=''>";
            echo "<button type='submit' name='create_admin' class='btn'>Crear usuario 'admin' con contraseña 'admin123'</button>";
            echo "</form>";
            echo "</div>";
        }
    }

} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<strong>✗ Error de base de datos:</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "</div>";
echo "</body></html>";
?>
