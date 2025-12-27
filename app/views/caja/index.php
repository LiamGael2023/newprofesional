<?php
ob_start();
$headerActions = '<a href="' . url('caja/create') . '" class="btn btn-primary"><i class="ti ti-plus"></i> Nuevo Pago</a>';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filtros de Búsqueda</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= url('caja') ?>">
            <div class="row g-2">
                <div class="col-md-2">
                    <label class="form-label">N° Recibo</label>
                    <input type="text" name="numero_recibo" class="form-control" value="<?= e($filters['numero_recibo'] ?? '') ?>" placeholder="REC-...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Colegiado</label>
                    <input type="text" name="codigo_colegiado" class="form-control" value="<?= e($filters['codigo_colegiado'] ?? '') ?>" placeholder="CP2025...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="<?= e($filters['fecha_desde'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="<?= e($filters['fecha_hasta'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Método</label>
                    <select name="metodo_pago" class="form-select">
                        <option value="">Todos</option>
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="TARJETA">Tarjeta</option>
                        <option value="TRANSFERENCIA">Transferencia</option>
                        <option value="YAPE">Yape</option>
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
                        <th>Recibo</th>
                        <th>Colegiado</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th class="w-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagos)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No se encontraron pagos</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($pagos as $pago): ?>
                        <tr>
                            <td><span class="badge bg-blue-lt"><?= e($pago['numero_recibo']) ?></span></td>
                            <td>
                                <div><strong><?= e($pago['codigo_colegiado']) ?></strong></div>
                                <div class="text-muted"><?= e($pago['nombre_completo']) ?></div>
                            </td>
                            <td><?= formatDateTime($pago['fecha_pago']) ?></td>
                            <td><strong><?= formatMoney($pago['monto_total']) ?></strong></td>
                            <td><span class="badge bg-cyan-lt"><?= e($pago['metodo_pago']) ?></span></td>
                            <td>
                                <span class="badge bg-<?= $pago['estado'] == 'PROCESADO' ? 'success' : 'danger' ?>">
                                    <?= e($pago['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('caja/recibo/' . $pago['id']) ?>" class="btn btn-sm btn-info" target="_blank">
                                        <i class="ti ti-printer"></i>
                                    </a>
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
$pageTitle = 'Caja - Pagos';
include APP_PATH . '/views/layouts/main.php';
?>
