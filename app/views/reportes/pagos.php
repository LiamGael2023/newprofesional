<?php
ob_start();
$headerActions = '<button onclick="window.print()" class="btn btn-primary d-print-none"><i class="ti ti-printer"></i> Imprimir</button>';
?>

<div class="row row-cards mb-3 d-print-none">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Pagos</div>
                <div class="h1 mb-0"><?= $totalPagos ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Monto Total</div>
                <div class="h1 mb-0 text-green"><?= formatMoney($montoTotal) ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Efectivo</div>
                <div class="h1 mb-0 text-blue"><?= formatMoney($montoEfectivo) ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Digital</div>
                <div class="h1 mb-0 text-cyan"><?= formatMoney($montoDigital) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3 d-print-none">
    <div class="card-header">
        <h3 class="card-title">Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= url('reportes/pagos') ?>">
            <div class="row g-2">
                <div class="col-md-2">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="<?= e($filters['fecha_desde'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="<?= e($filters['fecha_hasta'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Método de Pago</label>
                    <select name="metodo_pago" class="form-select">
                        <option value="">Todos</option>
                        <option value="EFECTIVO" <?= ($filters['metodo_pago'] ?? '') == 'EFECTIVO' ? 'selected' : '' ?>>Efectivo</option>
                        <option value="TARJETA" <?= ($filters['metodo_pago'] ?? '') == 'TARJETA' ? 'selected' : '' ?>>Tarjeta</option>
                        <option value="TRANSFERENCIA" <?= ($filters['metodo_pago'] ?? '') == 'TRANSFERENCIA' ? 'selected' : '' ?>>Transferencia</option>
                        <option value="YAPE" <?= ($filters['metodo_pago'] ?? '') == 'YAPE' ? 'selected' : '' ?>>Yape/Plin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Usuario</label>
                    <select name="usuario_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($usuarios as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= ($filters['usuario_id'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                            <?= e($user['username']) ?>
                        </option>
                        <?php endforeach; ?>
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

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Reporte de Pagos</h3>
        <div class="card-subtitle">Generado el <?= formatDateTime(date('Y-m-d H:i:s')) ?></div>
    </div>
    <div class="card-body">
        <?php if (empty($pagos)): ?>
            <div class="empty">
                <p class="empty-title">No se encontraron pagos</p>
                <p class="empty-subtitle text-muted">Ajuste los filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Recibo</th>
                            <th>Fecha/Hora</th>
                            <th>Colegiado</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subtotal = 0;
                        ?>
                        <?php foreach ($pagos as $index => $pago): ?>
                        <?php $subtotal += $pago['monto_total']; ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><span class="badge bg-blue-lt"><?= e($pago['numero_recibo']) ?></span></td>
                            <td>
                                <div><?= formatDate($pago['fecha_pago']) ?></div>
                                <div class="text-muted small"><?= date('H:i:s', strtotime($pago['fecha_pago'])) ?></div>
                            </td>
                            <td>
                                <div><strong><?= e($pago['codigo_colegiado']) ?></strong></div>
                                <div class="text-muted small"><?= e($pago['nombre_completo']) ?></div>
                            </td>
                            <td><strong><?= formatMoney($pago['monto_total']) ?></strong></td>
                            <td><span class="badge bg-cyan-lt"><?= e($pago['metodo_pago']) ?></span></td>
                            <td><?= e($pago['usuario_nombre']) ?></td>
                            <td>
                                <span class="badge bg-<?= $pago['estado'] == 'PROCESADO' ? 'success' : 'danger' ?>">
                                    <?= e($pago['estado']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                            <td><strong><?= formatMoney($subtotal) ?></strong></td>
                            <td colspan="3">
                                <strong>Registros: <?= count($pagos) ?></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if (!empty($detalleMetodos)): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <h4>Resumen por Método de Pago</h4>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Método</th>
                                <th>Cantidad</th>
                                <th>Monto Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalleMetodos as $metodo): ?>
                            <tr>
                                <td><strong><?= e($metodo['metodo_pago']) ?></strong></td>
                                <td><?= $metodo['cantidad'] ?> pagos</td>
                                <td><strong><?= formatMoney($metodo['total']) ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Reporte de Pagos';
include APP_PATH . '/views/layouts/main.php';
?>
