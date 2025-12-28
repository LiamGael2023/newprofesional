<?php
ob_start();
$headerActions = '';
if ($aportacion['estado'] == 'PENDIENTE' || $aportacion['estado'] == 'VENCIDO') {
    $headerActions = '<a href="' . url('caja/create?aportacion_id=' . $aportacion['id']) . '" class="btn btn-success"><i class="ti ti-cash"></i> Registrar Pago</a>';
}
?>

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detalle de Aportación</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Estado</label>
                    <div>
                        <?php
                        $colors = ['PENDIENTE' => 'warning', 'VENCIDO' => 'danger', 'PAGADO' => 'success', 'ANULADO' => 'secondary'];
                        $color = $colors[$aportacion['estado']] ?? 'info';
                        ?>
                        <span class="badge bg-<?= $color ?> badge-lg"><?= e($aportacion['estado']) ?></span>
                    </div>
                </div>
                <hr>
                <div class="row mb-2">
                    <div class="col-6">
                        <div class="text-muted">Tipo de Aportación</div>
                        <div><strong><?= e($aportacion['tipo_nombre']) ?></strong></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted">Periodo</div>
                        <div><strong><?= e($aportacion['periodo']) ?></strong></div>
                    </div>
                </div>
                <hr>
                <div class="mb-2">
                    <div class="text-muted">Monto Base</div>
                    <div class="h3 mb-0"><?= formatMoney($aportacion['monto']) ?></div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">Mora</div>
                    <div class="h3 mb-0 <?= $aportacion['mora'] > 0 ? 'text-red' : '' ?>">
                        <?= formatMoney($aportacion['mora']) ?>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">Monto Total</div>
                    <div class="h2 mb-0 text-primary"><?= formatMoney($aportacion['monto_total']) ?></div>
                </div>
                <hr>
                <div class="row mb-2">
                    <div class="col-6">
                        <div class="text-muted">Fecha de Vencimiento</div>
                        <div><?= formatDate($aportacion['fecha_vencimiento']) ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted">Fecha de Pago</div>
                        <div><?= $aportacion['fecha_pago'] ? formatDate($aportacion['fecha_pago']) : '-' ?></div>
                    </div>
                </div>

                <?php if ($aportacion['estado'] == 'VENCIDO'): ?>
                <div class="alert alert-danger mt-3">
                    <h4 class="alert-title"><i class="ti ti-alert-triangle"></i> Cuota Vencida</h4>
                    <p class="mb-0">
                        Esta cuota está vencida desde el <?= formatDate($aportacion['fecha_vencimiento']) ?>.
                        <?php
                        $dias_vencido = floor((time() - strtotime($aportacion['fecha_vencimiento'])) / 86400);
                        echo "Han pasado <strong>$dias_vencido días</strong>.";
                        ?>
                    </p>
                </div>
                <?php endif; ?>

                <?php if ($aportacion['estado'] == 'PAGADO'): ?>
                <div class="alert alert-success mt-3">
                    <h4 class="alert-title"><i class="ti ti-check"></i> Cuota Pagada</h4>
                    <p class="mb-0">
                        Esta cuota fue pagada el <?= formatDate($aportacion['fecha_pago']) ?>.
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información del Colegiado</h3>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="text-muted">Código</div>
                    <div>
                        <a href="<?= url('colegiados/show/' . $aportacion['colegiado_id']) ?>" class="h3 mb-0">
                            <?= e($aportacion['codigo_colegiado']) ?>
                        </a>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">Nombre Completo</div>
                    <div><strong><?= e($aportacion['nombre_completo']) ?></strong></div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">DNI</div>
                    <div><?= e($aportacion['dni']) ?></div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">Email</div>
                    <div><?= e($aportacion['email']) ?></div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">Celular</div>
                    <div><?= e($aportacion['celular']) ?></div>
                </div>
            </div>
        </div>

        <?php if (!empty($pagos)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Pagos Relacionados</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>Recibo</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagos as $pago): ?>
                            <tr>
                                <td><span class="badge bg-blue-lt"><?= e($pago['numero_recibo']) ?></span></td>
                                <td><?= formatDateTime($pago['fecha_pago']) ?></td>
                                <td><strong><?= formatMoney($pago['monto_pagado']) ?></strong></td>
                                <td><span class="badge bg-cyan-lt"><?= e($pago['metodo_pago']) ?></span></td>
                                <td>
                                    <a href="<?= url('caja/recibo/' . $pago['pago_id']) ?>" class="btn btn-sm btn-info" target="_blank">
                                        <i class="ti ti-printer"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Detalle de Aportación';
include APP_PATH . '/views/layouts/main.php';
?>
