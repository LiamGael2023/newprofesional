<?php
ob_start();
?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <form method="POST" action="<?= url('aportaciones/create') ?>">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nueva Aportación Manual</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Colegiado</label>
                            <select name="colegiado_id" class="form-select" required id="colegiado-select">
                                <option value="">Seleccione un colegiado...</option>
                                <?php foreach ($colegiados as $col): ?>
                                <option value="<?= $col['id'] ?>"
                                        data-codigo="<?= e($col['codigo_colegiado']) ?>"
                                        data-nombre="<?= e($col['nombre_completo']) ?>">
                                    <?= e($col['codigo_colegiado'] . ' - ' . $col['nombre_completo']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Tipo de Aportación</label>
                            <select name="tipo_aportacion_id" class="form-select" required id="tipo-select">
                                <option value="">Seleccione un tipo...</option>
                                <?php foreach ($tipos as $tipo): ?>
                                <option value="<?= $tipo['id'] ?>"
                                        data-monto="<?= $tipo['monto_base'] ?>"
                                        data-nombre="<?= e($tipo['nombre']) ?>">
                                    <?= e($tipo['nombre'] . ' - ' . formatMoney($tipo['monto_base'])) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12" id="colegiado-info" style="display: none;">
                            <div class="card bg-blue-lt">
                                <div class="card-body">
                                    <p class="mb-0">
                                        <strong>Colegiado:</strong> <span id="info-codigo"></span> - <span id="info-nombre"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required">Periodo</label>
                            <input type="month" name="periodo" class="form-control" value="<?= date('Y-m') ?>" required>
                            <small class="form-hint">Formato: YYYY-MM</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required">Monto</label>
                            <input type="number" name="monto" class="form-control" step="0.01" min="0" id="monto-input" required>
                            <small class="form-hint">Se llenará automáticamente al seleccionar el tipo</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required">Fecha de Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Mora Actual</label>
                            <input type="number" name="mora" class="form-control" step="0.01" min="0" value="0">
                            <small class="form-hint">Opcional, se calculará automáticamente si vence</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Estado</label>
                            <select name="estado" class="form-select" required>
                                <option value="PENDIENTE" selected>Pendiente</option>
                                <option value="VENCIDO">Vencido</option>
                                <option value="PAGADO">Pagado</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info">
                                <h4 class="alert-title"><i class="ti ti-info-circle"></i> Información</h4>
                                <ul class="mb-0">
                                    <li>Las cuotas mensuales normalmente se generan automáticamente</li>
                                    <li>Use esta opción solo para aportaciones extraordinarias o correcciones</li>
                                    <li>Verifique que no exista ya una aportación para el mismo periodo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="<?= url('aportaciones') ?>" class="btn btn-link">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy"></i> Crear Aportación
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('colegiado-select').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const infoDiv = document.getElementById('colegiado-info');

    if (this.value) {
        document.getElementById('info-codigo').textContent = option.dataset.codigo;
        document.getElementById('info-nombre').textContent = option.dataset.nombre;
        infoDiv.style.display = 'block';
    } else {
        infoDiv.style.display = 'none';
    }
});

document.getElementById('tipo-select').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const montoInput = document.getElementById('monto-input');

    if (this.value && option.dataset.monto) {
        montoInput.value = parseFloat(option.dataset.monto).toFixed(2);
    }
});
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Nueva Aportación';
include APP_PATH . '/views/layouts/main.php';
?>
