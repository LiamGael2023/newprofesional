<?php
ob_start();
$headerActions = '<button onclick="window.print()" class="btn btn-primary d-print-none"><i class="ti ti-printer"></i> Imprimir</button>';
?>

<div class="row row-cards mb-3 d-print-none">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Aportaciones</div>
                <div class="h1 mb-0"><?= $totalAportaciones ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Monto Total</div>
                <div class="h1 mb-0 text-blue"><?= formatMoney($montoTotal) ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Pagadas</div>
                <div class="h1 mb-0 text-green"><?= $totalPagadas ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Pendientes</div>
                <div class="h1 mb-0 text-orange"><?= $totalPendientes ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3 d-print-none">
    <div class="card-header">
        <h3 class="card-title">Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= url('reportes/aportaciones') ?>">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Periodo</label>
                    <input type="month" name="periodo" class="form-control" value="<?= e($filters['periodo'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="PENDIENTE" <?= ($filters['estado'] ?? '') == 'PENDIENTE' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="VENCIDO" <?= ($filters['estado'] ?? '') == 'VENCIDO' ? 'selected' : '' ?>>Vencido</option>
                        <option value="PAGADO" <?= ($filters['estado'] ?? '') == 'PAGADO' ? 'selected' : '' ?>>Pagado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($tipos as $tipo): ?>
                        <option value="<?= $tipo['id'] ?>" <?= ($filters['tipo'] ?? '') == $tipo['id'] ? 'selected' : '' ?>>
                            <?= e($tipo['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
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
        <h3 class="card-title">Reporte de Aportaciones</h3>
        <div class="card-subtitle">Generado el <?= formatDateTime(date('Y-m-d H:i:s')) ?></div>
    </div>
    <div class="card-body">
        <?php if (empty($aportaciones)): ?>
            <div class="empty">
                <p class="empty-title">No se encontraron aportaciones</p>
                <p class="empty-subtitle text-muted">Ajuste los filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Colegiado</th>
                            <th>Tipo</th>
                            <th>Periodo</th>
                            <th>Monto</th>
                            <th>Mora</th>
                            <th>Total</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subtotalMonto = 0;
                        $subtotalMora = 0;
                        $subtotalTotal = 0;
                        ?>
                        <?php foreach ($aportaciones as $index => $apt): ?>
                        <?php
                        $subtotalMonto += $apt['monto'];
                        $subtotalMora += $apt['mora'];
                        $subtotalTotal += $apt['monto_total'];
                        ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <div><strong><?= e($apt['codigo_colegiado']) ?></strong></div>
                                <div class="text-muted small"><?= e($apt['nombre_completo']) ?></div>
                            </td>
                            <td><?= e($apt['tipo_nombre']) ?></td>
                            <td><?= e($apt['periodo']) ?></td>
                            <td><?= formatMoney($apt['monto']) ?></td>
                            <td><?= $apt['mora'] > 0 ? '<span class="text-red">'.formatMoney($apt['mora']).'</span>' : '-' ?></td>
                            <td><strong><?= formatMoney($apt['monto_total']) ?></strong></td>
                            <td><?= formatDate($apt['fecha_vencimiento']) ?></td>
                            <td>
                                <?php
                                $colors = ['PENDIENTE' => 'warning', 'VENCIDO' => 'danger', 'PAGADO' => 'success', 'ANULADO' => 'secondary'];
                                $color = $colors[$apt['estado']] ?? 'info';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= e($apt['estado']) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-end"><strong>TOTALES:</strong></td>
                            <td><strong><?= formatMoney($subtotalMonto) ?></strong></td>
                            <td><strong class="text-red"><?= formatMoney($subtotalMora) ?></strong></td>
                            <td><strong><?= formatMoney($subtotalTotal) ?></strong></td>
                            <td colspan="2">
                                <strong>Registros: <?= count($aportaciones) ?></strong>
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
$pageTitle = 'Reporte de Aportaciones';
include APP_PATH . '/views/layouts/main.php';
?>
