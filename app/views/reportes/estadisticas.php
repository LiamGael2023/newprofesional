<?php
ob_start();
$headerActions = '<button onclick="window.print()" class="btn btn-primary d-print-none"><i class="ti ti-printer"></i> Imprimir</button>';
?>

<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Estadísticas del Sistema</h3>
                <div class="card-subtitle">Periodo: <?= date('Y') ?></div>
            </div>
        </div>
    </div>

    <!-- Métricas Principales -->
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Colegiados</div>
                    <div class="ms-auto lh-1">
                        <div class="avatar avatar-sm bg-blue-lt rounded">
                            <i class="ti ti-users"></i>
                        </div>
                    </div>
                </div>
                <div class="h1 mb-3"><?= $stats['total_colegiados'] ?? 0 ?></div>
                <div class="d-flex mb-2">
                    <div class="text-green d-flex align-items-center">
                        <i class="ti ti-check"></i> <?= $stats['colegiados_activos'] ?? 0 ?> activos
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Recaudación Total</div>
                    <div class="ms-auto lh-1">
                        <div class="avatar avatar-sm bg-green-lt rounded">
                            <i class="ti ti-cash"></i>
                        </div>
                    </div>
                </div>
                <div class="h1 mb-3"><?= formatMoney($stats['recaudacion_total'] ?? 0) ?></div>
                <div class="d-flex mb-2">
                    <div class="text-muted">
                        Este mes: <?= formatMoney($stats['recaudacion_mes'] ?? 0) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Cuotas Pendientes</div>
                    <div class="ms-auto lh-1">
                        <div class="avatar avatar-sm bg-orange-lt rounded">
                            <i class="ti ti-alert-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="h1 mb-3"><?= $stats['cuotas_pendientes'] ?? 0 ?></div>
                <div class="d-flex mb-2">
                    <div class="text-orange">
                        <?= formatMoney($stats['monto_pendiente'] ?? 0) ?> por cobrar
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Tasa de Morosidad</div>
                    <div class="ms-auto lh-1">
                        <div class="avatar avatar-sm bg-red-lt rounded">
                            <i class="ti ti-trending-down"></i>
                        </div>
                    </div>
                </div>
                <div class="h1 mb-3"><?= number_format($stats['tasa_morosidad'] ?? 0, 1) ?>%</div>
                <div class="d-flex mb-2">
                    <div class="text-red">
                        <?= $stats['total_morosos'] ?? 0 ?> colegiados
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row row-cards">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Colegiados por Estado</h3>
            </div>
            <div class="card-body">
                <canvas id="chartEstados" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recaudación Mensual (<?= date('Y') ?>)</h3>
            </div>
            <div class="card-body">
                <canvas id="chartRecaudacion" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Métodos de Pago</h3>
            </div>
            <div class="card-body">
                <canvas id="chartMetodos" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Evolución de Colegiados</h3>
            </div>
            <div class="card-body">
                <canvas id="chartEvolucion" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tablas Detalladas -->
<div class="row row-cards">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top 10 Deudores</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($topDeudores)): ?>
                    <div class="empty">
                        <p class="empty-title">No hay deudores</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Colegiado</th>
                                    <th>Cuotas</th>
                                    <th>Deuda</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topDeudores as $deudor): ?>
                                <tr>
                                    <td>
                                        <div><strong><?= e($deudor['codigo_colegiado']) ?></strong></div>
                                        <div class="text-muted small"><?= e($deudor['nombre_completo']) ?></div>
                                    </td>
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

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Resumen por Tipo de Aportación</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($resumenTipos)): ?>
                    <div class="empty">
                        <p class="empty-title">No hay datos</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Generadas</th>
                                    <th>Pagadas</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resumenTipos as $tipo): ?>
                                <tr>
                                    <td><strong><?= e($tipo['nombre']) ?></strong></td>
                                    <td><?= $tipo['total_generadas'] ?></td>
                                    <td><span class="text-green"><?= $tipo['total_pagadas'] ?></span></td>
                                    <td><strong><?= formatMoney($tipo['monto_total']) ?></strong></td>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
// Gráfico de Estados
const ctxEstados = document.getElementById('chartEstados').getContext('2d');
new Chart(ctxEstados, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($estadosColegiados ?? [], 'estado')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($estadosColegiados ?? [], 'total')) ?>,
            backgroundColor: ['#2fb344', '#6c757d', '#f76707']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Gráfico de Recaudación
const ctxRecaudacion = document.getElementById('chartRecaudacion').getContext('2d');
new Chart(ctxRecaudacion, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($recaudacionMensual ?? [], 'mes')) ?>,
        datasets: [{
            label: 'Recaudación',
            data: <?= json_encode(array_column($recaudacionMensual ?? [], 'total')) ?>,
            backgroundColor: '#2fb344'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de Métodos de Pago
const ctxMetodos = document.getElementById('chartMetodos').getContext('2d');
new Chart(ctxMetodos, {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($metodosPago ?? [], 'metodo_pago')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($metodosPago ?? [], 'total')) ?>,
            backgroundColor: ['#206bc4', '#4299e1', '#2fb344', '#f76707']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Gráfico de Evolución
const ctxEvolucion = document.getElementById('chartEvolucion').getContext('2d');
new Chart(ctxEvolucion, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($evolucionColegiados ?? [], 'mes')) ?>,
        datasets: [{
            label: 'Colegiados',
            data: <?= json_encode(array_column($evolucionColegiados ?? [], 'total')) ?>,
            borderColor: '#206bc4',
            backgroundColor: 'rgba(32, 107, 196, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Estadísticas';
include APP_PATH . '/views/layouts/main.php';
?>
