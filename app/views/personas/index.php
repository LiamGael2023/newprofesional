<?php
ob_start();
?>

<div class="card">
    <div class="card-header">
        <h3>Gestión de Personas</h3>
        <a href="<?= url('personas/create') ?>" class="btn btn-success">+ Nueva Persona</a>
    </div>

    <div class="card-body">
        <!-- Filtros -->
        <form method="GET" action="<?= url('personas') ?>" class="mb-20">
            <div class="form-row">
                <div class="form-group">
                    <label>N° Documento</label>
                    <input type="text" name="numero_documento" class="form-control"
                           value="<?= e($filters['numero_documento'] ?? '') ?>"
                           placeholder="Buscar por documento">
                </div>

                <div class="form-group">
                    <label>Nombres/Apellidos</label>
                    <input type="text" name="nombres" class="form-control"
                           value="<?= e($filters['nombres'] ?? '') ?>"
                           placeholder="Buscar por nombre">
                </div>

                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="ACTIVO" <?= ($filters['estado'] ?? '') == 'ACTIVO' ? 'selected' : '' ?>>Activo</option>
                        <option value="INACTIVO" <?= ($filters['estado'] ?? '') == 'INACTIVO' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Buscar</button>
                </div>
            </div>
        </form>

        <!-- Tabla de resultados -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Nombres y Apellidos</th>
                        <th>Email</th>
                        <th>Celular</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($personas)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">No se encontraron personas</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($personas as $persona): ?>
                        <tr>
                            <td>
                                <?= e($persona['tipo_documento']) ?><br>
                                <strong><?= e($persona['numero_documento']) ?></strong>
                            </td>
                            <td>
                                <?= e($persona['nombres']) ?><br>
                                <strong><?= e($persona['apellido_paterno'] . ' ' . $persona['apellido_materno']) ?></strong>
                            </td>
                            <td><?= e($persona['email']) ?></td>
                            <td><?= e($persona['celular']) ?></td>
                            <td>
                                <span class="badge badge-<?= $persona['estado'] == 'ACTIVO' ? 'success' : 'secondary' ?>">
                                    <?= e($persona['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= url('personas/view/' . $persona['id']) ?>" class="btn btn-info btn-sm">Ver</a>
                                <a href="<?= url('personas/edit/' . $persona['id']) ?>" class="btn btn-warning btn-sm">Editar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Personas';
include APP_PATH . '/views/layouts/main.php';
?>
