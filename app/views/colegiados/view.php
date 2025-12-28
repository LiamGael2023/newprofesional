<?php
ob_start();
$headerActions = '<a href="' . url('colegiados/edit/' . $colegiado['id']) . '" class="btn btn-primary"><i class="ti ti-edit"></i> Editar</a>';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información Personal</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Código Colegiado</label>
                    <div class="h3 mb-0"><?= e($colegiado['codigo_colegiado']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Estado</label>
                    <div>
                        <?php
                        $colors = ['ACTIVO' => 'success', 'INACTIVO' => 'secondary', 'SUSPENDIDO' => 'warning'];
                        $color = $colors[$colegiado['estado']] ?? 'info';
                        ?>
                        <span class="badge bg-<?= $color ?> badge-lg"><?= e($colegiado['estado']) ?></span>
                    </div>
                </div>
                <hr>
                <div class="mb-2">
                    <div class="text-muted">Nombre Completo</div>
                    <div><strong><?= e($colegiado['nombre_completo']) ?></strong></div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">DNI</div>
                    <div><?= e($colegiado['dni']) ?></div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">Email</div>
                    <div><?= e($colegiado['email']) ?></div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">Celular</div>
                    <div><?= e($colegiado['celular']) ?></div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">Fecha de Colegiación</div>
                    <div><?= formatDate($colegiado['fecha_colegiacion']) ?></div>
                </div>
                <?php if (!empty($colegiado['observaciones'])): ?>
                <div class="mt-3">
                    <div class="text-muted">Observaciones</div>
                    <div><?= nl2br(e($colegiado['observaciones'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aportaciones Recientes</h3>
            </div>
            <div class="card-body">
                <?php if (empty($aportaciones)): ?>
                    <div class="empty">
                        <p class="empty-title">No hay aportaciones registradas</p>
                        <p class="empty-subtitle text-muted">Las aportaciones se generan automáticamente cada mes</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Periodo</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th>Mora</th>
                                    <th>Total</th>
                                    <th>Vencimiento</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($aportaciones as $apt): ?>
                                <tr>
                                    <td><?= e($apt['periodo']) ?></td>
                                    <td><?= e($apt['tipo_nombre']) ?></td>
                                    <td><?= formatMoney($apt['monto']) ?></td>
                                    <td><?= $apt['mora'] > 0 ? '<span class="text-red">'.formatMoney($apt['mora']).'</span>' : '-' ?></td>
                                    <td><strong><?= formatMoney($apt['monto_total']) ?></strong></td>
                                    <td><?= formatDate($apt['fecha_vencimiento']) ?></td>
                                    <td>
                                        <?php
                                        $colors = ['PENDIENTE' => 'warning', 'VENCIDO' => 'danger', 'PAGADO' => 'success', 'ANULADO' => 'secondary'];
                                        $color = $colors[$apt['estado']] ?? 'info';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= e($apt['estado']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Historial de Pagos</h3>
            </div>
            <div class="card-body">
                <?php if (empty($pagos)): ?>
                    <div class="empty">
                        <p class="empty-title">No hay pagos registrados</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Recibo</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pagos as $pago): ?>
                                <tr>
                                    <td><span class="badge bg-blue-lt"><?= e($pago['numero_recibo']) ?></span></td>
                                    <td><?= formatDateTime($pago['fecha_pago']) ?></td>
                                    <td><strong><?= formatMoney($pago['monto_total']) ?></strong></td>
                                    <td><span class="badge bg-cyan-lt"><?= e($pago['metodo_pago']) ?></span></td>
                                    <td>
                                        <span class="badge bg-<?= $pago['estado'] == 'PROCESADO' ? 'success' : 'danger' ?>">
                                            <?= e($pago['estado']) ?>
                                        </span>
                                    </td>
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

<?php
$content = ob_get_clean();
$pageTitle = 'Detalle Colegiado: ' . $colegiado['codigo_colegiado'];
include APP_PATH . '/views/layouts/main.php';
?>
