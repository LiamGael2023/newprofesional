<?php
ob_start();
$headerActions = '<a href="' . url('aportaciones') . '" class="btn"><i class="ti ti-arrow-left"></i> Volver</a>';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Generar Cuotas Mensuales</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h4 class="alert-title"><i class="ti ti-info-circle"></i> Información</h4>
            <div class="text-muted">
                Esta acción generará automáticamente las cuotas mensuales obligatorias para todos los colegiados activos.
            </div>
        </div>

        <form method="POST" action="<?= url('aportaciones/generar') ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">Periodo (Año-Mes)</label>
                    <input type="month" name="periodo" class="form-control" value="<?= date('Y-m') ?>" required>
                    <small class="form-hint">Formato: YYYY-MM (ej: 2025-01)</small>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success"><i class="ti ti-check"></i> Generar Cuotas</button>
                <a href="<?= url('aportaciones') ?>" class="btn">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">¿Cómo funciona?</h3>
    </div>
    <div class="card-body">
        <ol>
            <li>Selecciona el periodo (mes y año) para el cual deseas generar las cuotas</li>
            <li>El sistema buscará todos los colegiados con estado "ACTIVO"</li>
            <li>Se crearán automáticamente las cuotas mensuales obligatorias</li>
            <li>Las cuotas tendrán una fecha de vencimiento de 30 días</li>
            <li>Si ya existen cuotas para ese periodo, no se duplicarán</li>
        </ol>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Generar Cuotas Mensuales';
include APP_PATH . '/views/layouts/main.php';
?>
