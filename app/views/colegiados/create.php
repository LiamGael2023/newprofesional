<?php
ob_start();
?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <form method="POST" action="<?= url('colegiados/create') ?>">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nueva Colegiación</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required">Seleccionar Persona</label>
                            <select name="persona_id" class="form-select" required id="persona-select">
                                <option value="">Seleccione una persona...</option>
                                <?php foreach ($personas as $persona): ?>
                                <option value="<?= $persona['id'] ?>"
                                        data-dni="<?= e($persona['dni']) ?>"
                                        data-nombre="<?= e($persona['nombres'] . ' ' . $persona['apellido_paterno'] . ' ' . $persona['apellido_materno']) ?>"
                                        data-email="<?= e($persona['email']) ?>"
                                        data-celular="<?= e($persona['celular']) ?>">
                                    <?= e($persona['dni'] . ' - ' . $persona['nombres'] . ' ' . $persona['apellido_paterno'] . ' ' . $persona['apellido_materno']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-hint">Solo se muestran personas que no están colegiadas</small>
                        </div>

                        <div class="col-12" id="persona-info" style="display: none;">
                            <div class="card bg-blue-lt">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>DNI:</strong> <span id="info-dni"></span></p>
                                            <p class="mb-1"><strong>Nombre:</strong> <span id="info-nombre"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Email:</strong> <span id="info-email"></span></p>
                                            <p class="mb-1"><strong>Celular:</strong> <span id="info-celular"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Fecha de Colegiación</label>
                            <input type="date" name="fecha_colegiacion" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Estado</label>
                            <select name="estado" class="form-select" required>
                                <option value="ACTIVO" selected>Activo</option>
                                <option value="INACTIVO">Inactivo</option>
                                <option value="SUSPENDIDO">Suspendido</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="3" placeholder="Opcional..."></textarea>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info">
                                <h4 class="alert-title"><i class="ti ti-info-circle"></i> Información</h4>
                                <p class="mb-0">El código de colegiado se generará automáticamente en el formato: CP<?= date('Y') ?>####</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="<?= url('colegiados') ?>" class="btn btn-link">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy"></i> Colegiar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('persona-select').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const infoDiv = document.getElementById('persona-info');

    if (this.value) {
        document.getElementById('info-dni').textContent = option.dataset.dni;
        document.getElementById('info-nombre').textContent = option.dataset.nombre;
        document.getElementById('info-email').textContent = option.dataset.email;
        document.getElementById('info-celular').textContent = option.dataset.celular;
        infoDiv.style.display = 'block';
    } else {
        infoDiv.style.display = 'none';
    }
});
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Nueva Colegiación';
include APP_PATH . '/views/layouts/main.php';
?>
