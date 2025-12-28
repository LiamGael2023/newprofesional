<?php
ob_start();
?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <form method="POST" action="<?= url('colegiados/edit/' . $colegiado['id']) ?>">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Colegiado: <?= e($colegiado['codigo_colegiado']) ?></h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card bg-blue-lt">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Código:</strong> <?= e($colegiado['codigo_colegiado']) ?></p>
                                            <p class="mb-1"><strong>DNI:</strong> <?= e($colegiado['dni']) ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Nombre:</strong> <?= e($colegiado['nombre_completo']) ?></p>
                                            <p class="mb-1"><strong>Email:</strong> <?= e($colegiado['email']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Fecha de Colegiación</label>
                            <input type="date" name="fecha_colegiacion" class="form-control" value="<?= e($colegiado['fecha_colegiacion']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Estado</label>
                            <select name="estado" class="form-select" required>
                                <option value="ACTIVO" <?= $colegiado['estado'] == 'ACTIVO' ? 'selected' : '' ?>>Activo</option>
                                <option value="INACTIVO" <?= $colegiado['estado'] == 'INACTIVO' ? 'selected' : '' ?>>Inactivo</option>
                                <option value="SUSPENDIDO" <?= $colegiado['estado'] == 'SUSPENDIDO' ? 'selected' : '' ?>>Suspendido</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="3"><?= e($colegiado['observaciones'] ?? '') ?></textarea>
                        </div>

                        <?php if ($colegiado['estado'] != 'ACTIVO'): ?>
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <h4 class="alert-title"><i class="ti ti-alert-triangle"></i> Advertencia</h4>
                                <p class="mb-0">
                                    Este colegiado está en estado <strong><?= e($colegiado['estado']) ?></strong>.
                                    <?php if ($colegiado['estado'] == 'SUSPENDIDO'): ?>
                                        No se generarán cuotas mientras esté suspendido.
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="<?= url('colegiados') ?>" class="btn btn-link">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Editar Colegiado';
include APP_PATH . '/views/layouts/main.php';
?>
