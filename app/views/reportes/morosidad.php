<?php
ob_start();
$headerActions = '<button onclick="window.print()" class="btn btn-primary d-print-none"><i class="ti ti-printer"></i> Imprimir</button>';
?>

<div class="row row-cards mb-3 d-print-none">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Deudores</div>
                <div class="h1 mb-0"><?= $totalDeudores ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Deuda</div>
                <div class="h1 mb-0 text-red"><?= formatMoney($totalDeuda) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Colegiados con Cuotas Vencidas</h3>
    </div>
    <div class="card-body">
        <?php if (empty($deudores)): ?>
            <div class="empty">
                <p class="empty-title">No hay colegiados con cuotas vencidas</p>
                <p class="empty-subtitle text-muted">¡Excelente! Todos están al día.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Colegiado</th>
                            <th>Email</th>
                            <th>Celular</th>
                            <th>Cuotas Vencidas</th>
                            <th>Días Mora</th>
                            <th>Total Deuda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deudores as $deudor): ?>
                        <tr>
                            <td><span class="badge bg-blue-lt"><?= e($deudor['codigo_colegiado']) ?></span></td>
                            <td>
                                <div><strong><?= e($deudor['nombre_completo']) ?></strong></div>
                                <div class="text-muted">Primera cuota vencida: <?= formatDate($deudor['primera_cuota_vencida']) ?></div>
                            </td>
                            <td><?= e($deudor['email']) ?></td>
                            <td><?= e($deudor['celular']) ?></td>
                            <td><span class="badge bg-red"><?= $deudor['cuotas_vencidas'] ?></span></td>
                            <td><?= $deudor['dias_mora'] ?> días</td>
                            <td><strong class="text-red"><?= formatMoney($deudor['total_deuda']) ?></strong></td>
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
$pageTitle = 'Reporte de Morosidad';
include APP_PATH . '/views/layouts/main.php';
?>
