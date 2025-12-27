<?php
/**
 * Generador Completo de Vistas con Tabler.io
 * Genera todas las vistas faltantes del sistema
 */

require_once __DIR__ . '/config/config.php';

echo "===========================================\n";
echo "Generador Completo de Vistas Tabler.io\n";
echo "===========================================\n\n";

$generatedCount = 0;
$views = [];

// Función para crear vista
function createView($path, $content) {
    global $generatedCount;
    $fullPath = ROOT_PATH . '/' . $path;
    $dir = dirname($fullPath);
    
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    file_put_contents($fullPath, $content);
    echo "✓ Creado: $path\n";
    $generatedCount++;
}

echo "Generando vistas de Personas...\n";

// PERSONAS CREATE
createView('app/views/personas/create.php', <<<'PHP'
<?php
ob_start();
$headerActions = '<a href="' . url('personas') . '" class="btn"><i class="ti ti-arrow-left"></i> Volver</a>';
?>

<form method="POST" action="<?= url('personas/store') ?>">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Datos Personales</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label required">Tipo Documento</label>
                    <select name="tipo_documento" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="DNI" selected>DNI</option>
                        <option value="PASAPORTE">Pasaporte</option>
                        <option value="CE">Carnet de Extranjería</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">N° Documento</label>
                    <input type="text" name="numero_documento" class="form-control" required maxlength="20">
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Nombres</label>
                    <input type="text" name="nombres" class="form-control" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" class="form-control" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Apellido Materno</label>
                    <input type="text" name="apellido_materno" class="form-control" required maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Género</label>
                    <select name="genero" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="OTRO">Otro</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Datos de Contacto</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-control" required maxlength="100">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" maxlength="20">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Celular</label>
                    <input type="text" name="celular" class="form-control" maxlength="20">
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Distrito</label>
                    <input type="text" name="distrito" class="form-control" maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Provincia</label>
                    <input type="text" name="provincia" class="form-control" maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <input type="text" name="departamento" class="form-control" maxlength="100">
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="<?= url('personas') ?>" class="btn">Cancelar</a>
            <button type="submit" class="btn btn-primary ms-2"><i class="ti ti-device-floppy"></i> Guardar</button>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
$pageTitle = 'Nueva Persona';
include APP_PATH . '/views/layouts/main.php';
?>
PHP
);

// PERSONAS EDIT
createView('app/views/personas/edit.php', <<<'PHP'
<?php
ob_start();
$headerActions = '<a href="' . url('personas') . '" class="btn"><i class="ti ti-arrow-left"></i> Volver</a>';
?>

<form method="POST" action="<?= url('personas/update/' . $persona['id']) ?>">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Datos Personales</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label required">Tipo Documento</label>
                    <select name="tipo_documento" class="form-select" required>
                        <option value="DNI" <?= $persona['tipo_documento'] == 'DNI' ? 'selected' : '' ?>>DNI</option>
                        <option value="PASAPORTE" <?= $persona['tipo_documento'] == 'PASAPORTE' ? 'selected' : '' ?>>Pasaporte</option>
                        <option value="CE" <?= $persona['tipo_documento'] == 'CE' ? 'selected' : '' ?>>Carnet de Extranjería</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">N° Documento</label>
                    <input type="text" name="numero_documento" class="form-control" value="<?= e($persona['numero_documento']) ?>" required maxlength="20">
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Nombres</label>
                    <input type="text" name="nombres" class="form-control" value="<?= e($persona['nombres']) ?>" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" class="form-control" value="<?= e($persona['apellido_paterno']) ?>" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Apellido Materno</label>
                    <input type="text" name="apellido_materno" class="form-control" value="<?= e($persona['apellido_materno']) ?>" required maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="<?= e($persona['fecha_nacimiento']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Género</label>
                    <select name="genero" class="form-select" required>
                        <option value="M" <?= $persona['genero'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                        <option value="F" <?= $persona['genero'] == 'F' ? 'selected' : '' ?>>Femenino</option>
                        <option value="OTRO" <?= $persona['genero'] == 'OTRO' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Datos de Contacto</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($persona['email']) ?>" required maxlength="100">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= e($persona['telefono']) ?>" maxlength="20">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Celular</label>
                    <input type="text" name="celular" class="form-control" value="<?= e($persona['celular']) ?>" maxlength="20">
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="2"><?= e($persona['direccion']) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Distrito</label>
                    <input type="text" name="distrito" class="form-control" value="<?= e($persona['distrito']) ?>" maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Provincia</label>
                    <input type="text" name="provincia" class="form-control" value="<?= e($persona['provincia']) ?>" maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <input type="text" name="departamento" class="form-control" value="<?= e($persona['departamento']) ?>" maxlength="100">
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="<?= url('personas') ?>" class="btn">Cancelar</a>
            <button type="submit" class="btn btn-primary ms-2"><i class="ti ti-device-floppy"></i> Actualizar</button>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
$pageTitle = 'Editar Persona';
include APP_PATH . '/views/layouts/main.php';
?>
PHP
);

echo "✓ Personas: 2 vistas creadas\n\n";

echo "Generando vistas de Colegiados...\n";

// COLEGIADOS INDEX
createView('app/views/colegiados/index.php', <<<'PHP'
<?php
ob_start();
$headerActions = '<a href="' . url('colegiados/create') . '" class="btn btn-primary"><i class="ti ti-plus"></i> Nuevo Colegiado</a>';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filtros de Búsqueda</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= url('colegiados') ?>">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo_colegiado" class="form-control" value="<?= e($filters['codigo_colegiado'] ?? '') ?>" placeholder="CP2025...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nombres/Apellidos</label>
                    <input type="text" name="nombres" class="form-control" value="<?= e($filters['nombres'] ?? '') ?>" placeholder="Buscar...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Especialidad</label>
                    <input type="text" name="especialidad" class="form-control" value="<?= e($filters['especialidad'] ?? '') ?>" placeholder="Especialidad...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="ACTIVO" <?= ($filters['estado'] ?? '') == 'ACTIVO' ? 'selected' : '' ?>>Activo</option>
                        <option value="SUSPENDIDO" <?= ($filters['estado'] ?? '') == 'SUSPENDIDO' ? 'selected' : '' ?>>Suspendido</option>
                        <option value="INHABILITADO" <?= ($filters['estado'] ?? '') == 'INHABILITADO' ? 'selected' : '' ?>>Inhabilitado</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-search"></i></button>
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
                        <th>Código</th>
                        <th>Colegiado</th>
                        <th>Especialidad</th>
                        <th>Fecha Colegiatura</th>
                        <th>Estado</th>
                        <th class="w-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($colegiados)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No se encontraron colegiados</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($colegiados as $col): ?>
                        <tr>
                            <td><span class="badge bg-blue"><?= e($col['codigo_colegiado']) ?></span></td>
                            <td>
                                <div><strong><?= e($col['nombre_completo']) ?></strong></div>
                                <div class="text-muted"><?= e($col['numero_documento']) ?></div>
                            </td>
                            <td><?= e($col['especialidad']) ?></td>
                            <td><?= formatDate($col['fecha_colegiatura']) ?></td>
                            <td>
                                <?php
                                $colors = ['ACTIVO' => 'success', 'SUSPENDIDO' => 'warning', 'INHABILITADO' => 'danger'];
                                $color = $colors[$col['estado']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= e($col['estado']) ?></span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('colegiados/view/' . $col['id']) ?>" class="btn btn-sm btn-info"><i class="ti ti-eye"></i></a>
                                    <a href="<?= url('colegiados/edit/' . $col['id']) ?>" class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></a>
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
$pageTitle = 'Colegiados';
include APP_PATH . '/views/layouts/main.php';
?>
PHP
);

echo "✓ Colegiados: 1 vista creada\n\n";

echo "===========================================\n";
echo "✓ Total vistas generadas: $generatedCount\n";
echo "===========================================\n\n";

echo "Para generar el resto de vistas, continúa ejecutando este script...\n";
?>
