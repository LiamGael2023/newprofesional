<?php
ob_start();
?>

<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reportes del Sistema</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Seleccione el tipo de reporte que desea generar:</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-link">
            <a href="<?= url('reportes/colegiados') ?>" class="d-block">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-lg rounded" style="background: #206bc4;">
                                <i class="ti ti-users" style="font-size: 2rem;"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Reporte de Colegiados</div>
                            <div class="text-muted">Lista completa de colegiados con filtros avanzados</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-link">
            <a href="<?= url('reportes/morosidad') ?>" class="d-block">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-lg rounded" style="background: #d63939;">
                                <i class="ti ti-alert-triangle" style="font-size: 2rem;"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Reporte de Morosidad</div>
                            <div class="text-muted">Colegiados con cuotas vencidas y deudas</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-link">
            <a href="<?= url('reportes/aportaciones') ?>" class="d-block">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-lg rounded" style="background: #f76707;">
                                <i class="ti ti-coins" style="font-size: 2rem;"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Reporte de Aportaciones</div>
                            <div class="text-muted">Historial de cuotas y aportaciones por periodo</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-link">
            <a href="<?= url('reportes/pagos') ?>" class="d-block">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-lg rounded" style="background: #2fb344;">
                                <i class="ti ti-cash" style="font-size: 2rem;"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Reporte de Pagos</div>
                            <div class="text-muted">Recibos de caja y métodos de pago</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-link">
            <a href="<?= url('reportes/estadisticas') ?>" class="d-block">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-lg rounded" style="background: #ae3ec9;">
                                <i class="ti ti-chart-bar" style="font-size: 2rem;"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Estadísticas</div>
                            <div class="text-muted">Gráficos y métricas del sistema</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-link">
            <a href="<?= url('reportes/auditoria') ?>" class="d-block">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-lg rounded" style="background: #4299e1;">
                                <i class="ti ti-file-text" style="font-size: 2rem;"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Auditoría</div>
                            <div class="text-muted">Historial de operaciones del sistema</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Exportar Reportes</h3>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Todos los reportes pueden ser:</p>
                <ul>
                    <li><i class="ti ti-printer"></i> <strong>Impresos</strong> directamente desde el navegador</li>
                    <li><i class="ti ti-file-spreadsheet"></i> <strong>Exportados a Excel</strong> para análisis adicional</li>
                    <li><i class="ti ti-file-text"></i> <strong>Guardados como PDF</strong> para distribución</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Reportes';
include APP_PATH . '/views/layouts/main.php';
?>
