<?php
ob_start();
?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <form method="POST" action="<?= url('caja/create') ?>" id="payment-form">
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Seleccionar Colegiado</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
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

                            <div id="aportaciones-pendientes" style="display: none;">
                                <h4>Aportaciones Pendientes</h4>
                                <div id="aportaciones-list"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Resumen de Pago</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-muted">Subtotal</div>
                                <div class="h2 mb-0" id="subtotal-display">S/ 0.00</div>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label required">Método de Pago</label>
                                <select name="metodo_pago" class="form-select" required>
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="TARJETA">Tarjeta de Crédito/Débito</option>
                                    <option value="TRANSFERENCIA">Transferencia Bancaria</option>
                                    <option value="YAPE">Yape/Plin</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Referencia/Comprobante</label>
                                <input type="text" name="referencia" class="form-control" placeholder="Número de operación (opcional)">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="2" placeholder="Opcional..."></textarea>
                            </div>

                            <hr>
                            <div class="mb-0">
                                <div class="text-muted">Total a Pagar</div>
                                <div class="h1 mb-0 text-primary" id="total-display">S/ 0.00</div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="<?= url('caja') ?>" class="btn btn-link">Cancelar</a>
                            <button type="submit" class="btn btn-success" id="btn-pagar" disabled>
                                <i class="ti ti-cash"></i> Procesar Pago
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let selectedAportaciones = [];
let total = 0;

document.getElementById('colegiado-select').addEventListener('change', function() {
    const colegiadoId = this.value;

    if (!colegiadoId) {
        document.getElementById('aportaciones-pendientes').style.display = 'none';
        return;
    }

    // Fetch aportaciones pendientes
    fetch('<?= url('aportaciones/pendientes') ?>?colegiado_id=' + colegiadoId)
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('aportaciones-list');
            container.innerHTML = '';
            selectedAportaciones = [];
            total = 0;
            updateTotal();

            if (data.length === 0) {
                container.innerHTML = '<div class="alert alert-success">No hay aportaciones pendientes</div>';
                document.getElementById('aportaciones-pendientes').style.display = 'block';
                return;
            }

            data.forEach(apt => {
                const div = document.createElement('div');
                div.className = 'card mb-2';
                div.innerHTML = `
                    <div class="card-body p-2">
                        <div class="form-check">
                            <input class="form-check-input aportacion-check" type="checkbox"
                                   value="${apt.id}" id="apt-${apt.id}"
                                   data-monto="${apt.monto_total}">
                            <label class="form-check-label" for="apt-${apt.id}">
                                <strong>${apt.tipo_nombre}</strong> - ${apt.periodo}
                                <br>
                                <small class="text-muted">
                                    Vence: ${apt.fecha_vencimiento} |
                                    Monto: S/ ${parseFloat(apt.monto).toFixed(2)} |
                                    Mora: S/ ${parseFloat(apt.mora).toFixed(2)}
                                </small>
                                <br>
                                <strong class="text-primary">Total: S/ ${parseFloat(apt.monto_total).toFixed(2)}</strong>
                            </label>
                        </div>
                    </div>
                `;
                container.appendChild(div);
            });

            document.getElementById('aportaciones-pendientes').style.display = 'block';

            // Add event listeners to checkboxes
            document.querySelectorAll('.aportacion-check').forEach(cb => {
                cb.addEventListener('change', function() {
                    if (this.checked) {
                        selectedAportaciones.push({
                            id: parseInt(this.value),
                            monto: parseFloat(this.dataset.monto)
                        });
                    } else {
                        selectedAportaciones = selectedAportaciones.filter(a => a.id !== parseInt(this.value));
                    }
                    updateTotal();
                });
            });
        });
});

function updateTotal() {
    total = selectedAportaciones.reduce((sum, apt) => sum + apt.monto, 0);
    document.getElementById('subtotal-display').textContent = 'S/ ' + total.toFixed(2);
    document.getElementById('total-display').textContent = 'S/ ' + total.toFixed(2);
    document.getElementById('btn-pagar').disabled = total === 0;
}

document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();

    if (selectedAportaciones.length === 0) {
        alert('Debe seleccionar al menos una aportación');
        return;
    }

    // Add selected aportaciones to form
    const form = this;
    selectedAportaciones.forEach(apt => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'aportaciones[]';
        input.value = apt.id;
        form.appendChild(input);
    });

    form.submit();
});
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Nuevo Pago';
include APP_PATH . '/views/layouts/main.php';
?>
