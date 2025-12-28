<?php
ob_start();
$headerActions = '<button onclick="window.print()" class="btn btn-primary d-print-none"><i class="ti ti-printer"></i> Imprimir</button>';
?>

<div class="card mb-3 d-print-none">
    <div class="card-header">
        <h3 class="card-title">Filtros de Auditoría</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= url('reportes/auditoria') ?>">
            <div class="row g-2">
                <div class="col-md-2">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="<?= e($filters['fecha_desde'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="<?= e($filters['fecha_hasta'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tabla</label>
                    <select name="tabla" class="form-select">
                        <option value="">Todas</option>
                        <option value="colegiados" <?= ($filters['tabla'] ?? '') == 'colegiados' ? 'selected' : '' ?>>Colegiados</option>
                        <option value="aportaciones" <?= ($filters['tabla'] ?? '') == 'aportaciones' ? 'selected' : '' ?>>Aportaciones</option>
                        <option value="pagos" <?= ($filters['tabla'] ?? '') == 'pagos' ? 'selected' : '' ?>>Pagos</option>
                        <option value="personas" <?= ($filters['tabla'] ?? '') == 'personas' ? 'selected' : '' ?>>Personas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Operación</label>
                    <select name="operacion" class="form-select">
                        <option value="">Todas</option>
                        <option value="INSERT" <?= ($filters['operacion'] ?? '') == 'INSERT' ? 'selected' : '' ?>>INSERT</option>
                        <option value="UPDATE" <?= ($filters['operacion'] ?? '') == 'UPDATE' ? 'selected' : '' ?>>UPDATE</option>
                        <option value="DELETE" <?= ($filters['operacion'] ?? '') == 'DELETE' ? 'selected' : '' ?>>DELETE</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Usuario</label>
                    <select name="usuario_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($usuarios as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= ($filters['usuario_id'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                            <?= e($user['username']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registro de Auditoría</h3>
        <div class="card-subtitle">Historial de operaciones del sistema</div>
    </div>
    <div class="card-body">
        <?php if (empty($auditoria)): ?>
            <div class="empty">
                <div class="empty-icon">
                    <i class="ti ti-file-text" style="font-size: 3rem;"></i>
                </div>
                <p class="empty-title">No se encontraron registros de auditoría</p>
                <p class="empty-subtitle text-muted">Ajuste los filtros para ver más resultados</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-sm">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Tabla</th>
                            <th>Operación</th>
                            <th>Registro ID</th>
                            <th>Detalles</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($auditoria as $log): ?>
                        <tr>
                            <td>
                                <div><?= formatDate($log['fecha_operacion']) ?></div>
                                <div class="text-muted small"><?= date('H:i:s', strtotime($log['fecha_operacion'])) ?></div>
                            </td>
                            <td>
                                <div><?= e($log['usuario_nombre'] ?? 'Sistema') ?></div>
                            </td>
                            <td><span class="badge bg-blue-lt"><?= e($log['tabla_afectada']) ?></span></td>
                            <td>
                                <?php
                                $opColors = ['INSERT' => 'green', 'UPDATE' => 'blue', 'DELETE' => 'red'];
                                $color = $opColors[$log['operacion']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= e($log['operacion']) ?></span>
                            </td>
                            <td><?= e($log['registro_id']) ?></td>
                            <td>
                                <?php if (!empty($log['detalles'])): ?>
                                <button type="button" class="btn btn-sm btn-ghost-secondary"
                                        onclick="showDetails(this)"
                                        data-details="<?= htmlspecialchars($log['detalles']) ?>">
                                    <i class="ti ti-eye"></i> Ver
                                </button>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?= e($log['ip_address'] ?? '-') ?></small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-end">
                                <strong>Total de registros: <?= count($auditoria) ?></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-green-lt">
                            <div class="card-body p-3">
                                <div class="text-muted">Total INSERT</div>
                                <div class="h3 mb-0"><?= $stats['total_insert'] ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-blue-lt">
                            <div class="card-body p-3">
                                <div class="text-muted">Total UPDATE</div>
                                <div class="h3 mb-0"><?= $stats['total_update'] ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-red-lt">
                            <div class="card-body p-3">
                                <div class="text-muted">Total DELETE</div>
                                <div class="h3 mb-0"><?= $stats['total_delete'] ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para detalles -->
<div class="modal modal-blur fade" id="modal-details" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Operación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="details-content" style="background: #f5f5f5; padding: 15px; border-radius: 4px; max-height: 400px; overflow-y: auto;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
<script>
function showDetails(button) {
    const details = button.getAttribute('data-details');
    try {
        const formatted = JSON.stringify(JSON.parse(details), null, 2);
        document.getElementById('details-content').textContent = formatted;
    } catch (e) {
        document.getElementById('details-content').textContent = details;
    }

    const modal = new bootstrap.Modal(document.getElementById('modal-details'));
    modal.show();
}
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Auditoría del Sistema';
include APP_PATH . '/views/layouts/main.php';
?>
