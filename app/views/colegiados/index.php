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
                                    <a href="<?= url('colegiados/show/' . $col['id']) ?>" class="btn btn-sm btn-info"><i class="ti ti-eye"></i></a>
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