<?php
/**
 * Script para verificar y reparar la estructura de la base de datos
 */

require_once __DIR__ . '/config/config.php';

echo "========================================\n";
echo "Verificación de Base de Datos\n";
echo "========================================\n\n";

try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✓ Conectado al servidor MySQL\n\n";

    // Verificar si existe la base de datos
    $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    $exists = $stmt->fetch();

    if (!$exists) {
        echo "⚠ La base de datos '" . DB_NAME . "' NO EXISTE\n\n";
        echo "Creando base de datos...\n";
        $pdo->exec("CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✓ Base de datos creada\n\n";
    } else {
        echo "✓ Base de datos '" . DB_NAME . "' existe\n\n";
    }

    // Conectar a la base de datos específica
    $pdo->exec("USE " . DB_NAME);

    // Tablas requeridas
    $requiredTables = [
        'roles',
        'usuarios',
        'personas',
        'colegiados',
        'tipos_aportacion',
        'aportaciones',
        'pagos',
        'detalle_pagos',
        'auditoria',
        'configuracion'
    ];

    // Verificar tablas
    echo "Verificando tablas...\n";
    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $missingTables = array_diff($requiredTables, $existingTables);

    if (empty($missingTables)) {
        echo "✓ Todas las tablas existen (" . count($existingTables) . ")\n\n";
    } else {
        echo "✗ Faltan " . count($missingTables) . " tablas:\n";
        foreach ($missingTables as $table) {
            echo "  - $table\n";
        }
        echo "\n⚠ IMPORTANTE: Debes importar el schema completo\n";
        echo "Ejecuta: mysql -u root -p --port=" . DB_PORT . " " . DB_NAME . " < database/schema.sql\n\n";
        exit(1);
    }

    // Verificar columnas críticas
    echo "Verificando estructura de tablas...\n";

    $criticalColumns = [
        'personas' => ['estado', 'numero_documento', 'nombres', 'apellido_paterno', 'apellido_materno'],
        'colegiados' => ['estado', 'codigo_colegiado', 'persona_id'],
        'aportaciones' => ['estado', 'colegiado_id', 'periodo', 'monto_total'],
        'pagos' => ['estado', 'numero_recibo', 'colegiado_id', 'monto_total']
    ];

    $errors = [];
    foreach ($criticalColumns as $table => $columns) {
        $stmt = $pdo->query("DESCRIBE $table");
        $tableColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $missing = array_diff($columns, $tableColumns);
        if (!empty($missing)) {
            $errors[] = "Tabla '$table' le faltan columnas: " . implode(', ', $missing);
        } else {
            echo "  ✓ $table\n";
        }
    }

    if (!empty($errors)) {
        echo "\n✗ ERRORES ENCONTRADOS:\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
        echo "\n⚠ SOLUCIÓN: La base de datos está incompleta o corrupta.\n";
        echo "Elimina la base de datos actual e importa nuevamente:\n\n";
        echo "mysql -u root -p --port=" . DB_PORT . " -e \"DROP DATABASE IF EXISTS " . DB_NAME . ";\"\n";
        echo "mysql -u root -p --port=" . DB_PORT . " < database/schema.sql\n\n";
        exit(1);
    }

    echo "\n";

    // Verificar datos iniciales
    echo "Verificando datos iniciales...\n";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles");
    $count = $stmt->fetch()['count'];
    echo "  Roles: $count\n";
    if ($count == 0) {
        echo "    ⚠ No hay roles. Importa el schema completo.\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM usuarios");
    $count = $stmt->fetch()['count'];
    echo "  Usuarios: $count\n";
    if ($count == 0) {
        echo "    ⚠ No hay usuarios. Importa el schema completo.\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tipos_aportacion");
    $count = $stmt->fetch()['count'];
    echo "  Tipos de aportación: $count\n";
    if ($count == 0) {
        echo "    ⚠ No hay tipos de aportación. Importa el schema completo.\n";
    }

    echo "\n========================================\n";
    echo "✓ Verificación completada exitosamente!\n";
    echo "========================================\n";

} catch (PDOException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
