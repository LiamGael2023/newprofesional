<?php
/**
 * Generador de Vistas con Tabler.io
 * Este script genera todas las vistas faltantes del sistema
 */

require_once __DIR__ . '/config/config.php';

echo "===========================================\n";
echo "Generador de Vistas con Tabler.io\n";
echo "===========================================\n\n";

$views = [];

// ===== PERSONAS =====

$views['app/views/personas/index.php'] = <<<'PHP'
<?php
ob_start();
$headerActions = '<a href="' . url('personas/create') . '" class="btn btn-primary"><i class="ti ti-plus"></i> Nueva Persona</a>';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filtros de Búsqueda</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= url('personas') ?>">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">N° Documento</label>
                    <input type="text" name="numero_documento" class="form-control" value="<?= e($filters['numero_documento'] ?? '') ?>" placeholder="Buscar por documento">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nombres/Apellidos</label>
                    <input type="text" name="nombres" class="form-control" value="<?= e($filters['nombres'] ?? '') ?>" placeholder="Buscar por nombre">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="ACTIVO" <?= ($filters['estado'] ?? '') == 'ACTIVO' ? 'selected' : '' ?>>Activo</option>
                        <option value="INACTIVO" <?= ($filters['estado'] ?? '') == 'INACTIVO' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-search"></i> Buscar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Nombres y Apellidos</th>
                        <th>Email</th>
                        <th>Celular</th>
                        <th>Estado</th>
                        <th class="w-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($personas)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No se encontraron personas</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($personas as $persona): ?>
                        <tr>
                            <td>
                                <span class="badge bg-blue-lt"><?= e($persona['tipo_documento']) ?></span>
                                <div><strong><?= e($persona['numero_documento']) ?></strong></div>
                            </td>
                            <td>
                                <div><?= e($persona['nombres']) ?></div>
                                <div class="text-muted"><?= e($persona['apellido_paterno'] . ' ' . $persona['apellido_materno']) ?></div>
                            </td>
                            <td><?= e($persona['email']) ?></td>
                            <td><?= e($persona['celular']) ?></td>
                            <td>
                                <span class="badge bg-<?= $persona['estado'] == 'ACTIVO' ? 'success' : 'secondary' ?>">
                                    <?= e($persona['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('personas/view/' . $persona['id']) ?>" class="btn btn-sm btn-info"><i class="ti ti-eye"></i></a>
                                    <a href="<?= url('personas/edit/' . $persona['id']) ?>" class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Personas';
include APP_PATH . '/views/layouts/main.php';
?>
PHP;

// ===== LOGIN =====

$views['app/views/auth/login.php'] = <<<'PHP'
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Iniciar Sesión - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>
</head>
<body class="d-flex flex-column bg-white">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <h1>🎓 Colegio Profesional</h1>
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Iniciar Sesión</h2>
                    <?php if (isset($errors['login'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <div class="d-flex">
                            <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                            <div><?= e($errors['login'][0]) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <form method="POST" action="<?= url('auth/login') ?>" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="username" value="<?= e($username ?? '') ?>" placeholder="Ingresa tu usuario" autofocus required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center text-muted mt-3">
                Usuario por defecto: <strong>admin</strong> / Contraseña: <strong>admin123</strong>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>
PHP;

// Generar archivos
foreach ($views as $path => $content) {
    $fullPath = ROOT_PATH . '/' . $path;
    $dir = dirname($fullPath);

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    file_put_contents($fullPath, $content);
    echo "✓ Creado: $path\n";
}

echo "\n===========================================\n";
echo "✓ Vistas generadas exitosamente!\n";
echo "===========================================\n";
?>
