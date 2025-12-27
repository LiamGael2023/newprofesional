<?php
ob_start();
$headerActions = '
    <a href="' . url('aportaciones/generar') . '" class="btn btn-success"><i class="ti ti-refresh"></i> Generar Cuotas</a>
    <a href="' . url('aportaciones/create') . '" class="btn btn-primary"><i class="ti ti-plus"></i> Nueva Aportación</a>
';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filtros de Búsqueda</h3>
        <div class="card-actions">
            <button type="button" class="btn btn-warning btn-sm" onclick="calcularMoras()">
                <i class="ti ti-calculator"></i> Calcular Moras
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= url('aportaciones') ?>">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Código Colegiado</label>
                    <input type="text" name="codigo_colegiado" class="form-control" value="<?= e($filters['codigo_colegiado'] ?? '') ?>" placeholder="CP2025...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Periodo</label>
                    <input type="month" name="periodo" class="form-control" value="<?= e($filters['periodo'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="PENDIENTE" <?= ($filters['estado'] ?? '') == 'PENDIENTE' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="VENCIDO" <?= ($filters['estado'] ?? '') == 'VENCIDO' ? 'selected' : '' ?>>Vencido</option>
                        <option value="PAGADO" <?= ($filters['estado'] ?? '') == 'PAGADO' ? 'selected' : '' ?>>Pagado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-search"></i> Buscar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Colegiado</th>
                        <th>Tipo</th>
                        <th>Periodo</th>
                        <th>Monto</th>
                        <th>Mora</th>
                        <th>Total</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th class="w-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($aportaciones)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No se encontraron aportaciones</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($aportaciones as $apt): ?>
                        <tr>
                            <td>
                                <div><strong><?= e($apt['codigo_colegiado']) ?></strong></div>
                                <div class="text-muted small"><?= e($apt['nombre_completo']) ?></div>
                            </td>
                            <td><?= e($apt['tipo_nombre']) ?></td>
                            <td><?= e($apt['periodo']) ?></td>
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
                            <td>
                                <a href="<?= url('aportaciones/view/' . $apt['id']) ?>" class="btn btn-sm btn-info">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function calcularMoras() {
    if (confirm('¿Desea calcular las moras para todas las aportaciones vencidas?')) {
        fetch('<?= url('aportaciones/calcularMoras') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'}
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            location.reload();
        });
    }
}
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Aportaciones';
include APP_PATH . '/views/layouts/main.php';
?>
