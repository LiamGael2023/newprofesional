<?php
ob_start();
?>

<!-- Estadísticas -->
<div class="stats-grid">
    <div class="stat-card primary">
        <h4>Total Personas</h4>
        <div class="stat-value"><?= $totalPersonas ?></div>
    </div>

    <div class="stat-card success">
        <h4>Colegiados Activos</h4>
        <div class="stat-value"><?= $totalColegiados ?></div>
    </div>

    <div class="stat-card warning">
        <h4>Aportaciones Pendientes</h4>
        <div class="stat-value"><?= $aportacionesPendientes ?></div>
    </div>

    <div class="stat-card danger">
        <h4>Recaudado este Mes</h4>
        <div class="stat-value"><?= formatMoney($totalRecaudadoMes) ?></div>
    </div>
</div>

<!-- Últimos Pagos -->
<div class="card">
    <div class="card-header">
        <h3>Últimos Pagos Registrados</h3>
        <a href="<?= url('caja') ?>" class="btn btn-primary btn-sm">Ver todos</a>
    </div>
    <div class="card-body">
        <?php if (empty($ultimosPagos)): ?>
            <p class="text-muted">No hay pagos registrados</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
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
                            <td><?= e($pago['numero_recibo']) ?></td>
                            <td>
                                <strong><?= e($pago['codigo_colegiado']) ?></strong><br>
                                <small><?= e($pago['colegiado_nombre']) ?></small>
                            </td>
                            <td><?= formatDateTime($pago['fecha_pago']) ?></td>
                            <td><?= formatMoney($pago['monto_total']) ?></td>
                            <td><span class="badge badge-info"><?= e($pago['metodo_pago']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Deudores -->
<div class="card">
    <div class="card-header">
        <h3>Colegiados con Cuotas Vencidas</h3>
        <a href="<?= url('reportes/morosidad') ?>" class="btn btn-danger btn-sm">Ver reporte completo</a>
    </div>
    <div class="card-body">
        <?php if (empty($deudores)): ?>
            <p class="text-muted">No hay colegiados con cuotas vencidas</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
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
                            <td><?= e($deudor['codigo_colegiado']) ?></td>
                            <td><?= e($deudor['nombre_completo']) ?></td>
                            <td><span class="badge badge-danger"><?= $deudor['cuotas_vencidas'] ?></span></td>
                            <td><strong><?= formatMoney($deudor['total_deuda']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Dashboard';
include APP_PATH . '/views/layouts/main.php';
?>
