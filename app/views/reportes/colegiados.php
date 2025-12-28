<?php
ob_start();
$headerActions = '<button onclick="window.print()" class="btn btn-primary d-print-none"><i class="ti ti-printer"></i> Imprimir</button>';
?>

<div class="row row-cards mb-3 d-print-none">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Colegiados</div>
                <div class="h1 mb-0"><?= $totalColegiados ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Activos</div>
                <div class="h1 mb-0 text-green"><?= $totalActivos ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Inactivos</div>
                <div class="h1 mb-0 text-red"><?= $totalInactivos ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Suspendidos</div>
                <div class="h1 mb-0 text-orange"><?= $totalSuspendidos ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3 d-print-none">
    <div class="card-header">
        <h3 class="card-title">Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= url('reportes/colegiados') ?>">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="ACTIVO" <?= ($filters['estado'] ?? '') == 'ACTIVO' ? 'selected' : '' ?>>Activo</option>
                        <option value="INACTIVO" <?= ($filters['estado'] ?? '') == 'INACTIVO' ? 'selected' : '' ?>>Inactivo</option>
                        <option value="SUSPENDIDO" <?= ($filters['estado'] ?? '') == 'SUSPENDIDO' ? 'selected' : '' ?>>Suspendido</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Año de Colegiación</label>
                    <select name="anio" class="form-select">
                        <option value="">Todos</option>
                        <?php for ($year = date('Y'); $year >= 2000; $year--): ?>
                        <option value="<?= $year ?>" <?= ($filters['anio'] ?? '') == $year ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Búsqueda</label>
                    <input type="text" name="busqueda" class="form-control" placeholder="Nombre, DNI, Código..." value="<?= e($filters['busqueda'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-search"></i> Buscar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Reporte de Colegiados</h3>
        <div class="card-subtitle">Generado el <?= formatDateTime(date('Y-m-d H:i:s')) ?></div>
    </div>
    <div class="card-body">
        <?php if (empty($colegiados)): ?>
            <div class="empty">
                <p class="empty-title">No se encontraron colegiados</p>
                <p class="empty-subtitle text-muted">Ajuste los filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>DNI</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Celular</th>
                            <th>F. Colegiación</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($colegiados as $index => $col): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><span class="badge bg-blue-lt"><?= e($col['codigo_colegiado']) ?></span></td>
                            <td><?= e($col['dni']) ?></td>
                            <td><?= e($col['nombre_completo']) ?></td>
                            <td><?= e($col['email']) ?></td>
                            <td><?= e($col['celular']) ?></td>
                            <td><?= formatDate($col['fecha_colegiacion']) ?></td>
                            <td>
                                <?php
                                $colors = ['ACTIVO' => 'success', 'INACTIVO' => 'secondary', 'SUSPENDIDO' => 'warning'];
                                $color = $colors[$col['estado']] ?? 'info';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= e($col['estado']) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8" class="text-end">
                                <strong>Total de registros: <?= count($colegiados) ?></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Reporte de Colegiados';
include APP_PATH . '/views/layouts/main.php';
?>
