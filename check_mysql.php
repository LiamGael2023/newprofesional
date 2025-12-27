<?php
/**
 * Script para verificar puertos comunes de MySQL
 */

echo "Verificando puertos comunes de MySQL...\n\n";

$ports = [3306, 3307, 3308];
$host = '127.0.0.1';

foreach ($ports as $port) {
    echo "Probando puerto $port... ";

    $connection = @fsockopen($host, $port, $errno, $errstr, 1);

    if ($connection) {
        echo "✓ ABIERTO - MySQL probablemente está escuchando aquí\n";
        fclose($connection);

        // Intentar conectar con PDO
        try {
            $pdo = new PDO("mysql:host=$host;port=$port", 'root', '');
            echo "  └─ ✓ Conexión PDO exitosa con usuario 'root' sin contraseña\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Access denied') !== false) {
                echo "  └─ ⚠ Puerto correcto pero verifica usuario/contraseña\n";
            } else {
                echo "  └─ ✗ " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "✗ Cerrado\n";
    }
}

echo "\n";
echo "Si ningún puerto está abierto:\n";
echo "1. Inicia MySQL/XAMPP/WAMP\n";
echo "2. Verifica el puerto en la configuración (my.ini o my.cnf)\n";
echo "3. Asegúrate de que no haya firewall bloqueando\n";
