<?php
ob_start();
?>

<!-- Estadísticas -->
<div class="row row-deck row-cards">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Personas</div>
                </div>
                <div class="h1 mb-3"><?= $totalPersonas ?></div>
                <div class="d-flex mb-2">
                    <div>Registradas en el sistema</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Colegiados Activos</div>
                </div>
                <div class="h1 mb-3"><?= $totalColegiados ?></div>
                <div class="d-flex mb-2">
                    <div>Con estado activo</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Aportaciones Pendientes</div>
                </div>
                <div class="h1 mb-3"><?= $aportacionesPendientes ?></div>
                <div class="d-flex mb-2">
                    <div>Por cobrar</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Recaudado este Mes</div>
                </div>
                <div class="h1 mb-3"><?= formatMoney($totalRecaudadoMes) ?></div>
                <div class="d-flex mb-2">
                    <div><?= date('F Y') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Últimos Pagos -->
<div class="row row-cards mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Últimos Pagos Registrados</h3>
                <div class="card-actions">
                    <a href="<?= url('caja') ?>" class="btn btn-primary">
                        Ver todos
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($ultimosPagos)): ?>
                    <div class="empty">
                        <p class="empty-title">No hay pagos registrados</p>
                        <p class="empty-subtitle text-muted">
                            Los pagos aparecerán aquí cuando se registren.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Recibo</th>
                                    <th>Colegiado</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimosPagos as $pago): ?>
                                <tr>
                                    <td><span class="badge bg-blue-lt"><?= e($pago['numero_recibo']) ?></span></td>
                                    <td>
                                        <div><strong><?= e($pago['codigo_colegiado']) ?></strong></div>
                                        <div class="text-muted"><?= e($pago['colegiado_nombre']) ?></div>
                                    </td>
                                    <td><?= formatDateTime($pago['fecha_pago']) ?></td>
                                    <td><strong><?= formatMoney($pago['monto_total']) ?></strong></td>
                                    <td><span class="badge bg-cyan-lt"><?= e($pago['metodo_pago']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Deudores -->
<div class="row row-cards mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Colegiados con Cuotas Vencidas</h3>
                <div class="card-actions">
                    <a href="<?= url('reportes/morosidad') ?>" class="btn btn-danger">
                        Ver reporte completo
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($deudores)): ?>
                    <div class="empty">
                        <p class="empty-title">No hay colegiados con cuotas vencidas</p>
                        <p class="empty-subtitle text-muted">
                            ¡Excelente! Todos los colegiados están al día.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Colegiado</th>
                                    <th>Cuotas Vencidas</th>
                                    <th>Total Deuda</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deudores as $deudor): ?>
                                <tr>
                                    <td><span class="badge bg-blue-lt"><?= e($deudor['codigo_colegiado']) ?></span></td>
                                    <td><?= e($deudor['nombre_completo']) ?></td>
                                    <td><span class="badge bg-red"><?= $deudor['cuotas_vencidas'] ?></span></td>
                                    <td><strong class="text-red"><?= formatMoney($deudor['total_deuda']) ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Dashboard';
include APP_PATH . '/views/layouts/main.php';
?>
