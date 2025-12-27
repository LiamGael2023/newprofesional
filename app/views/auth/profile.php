<?php
ob_start();
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <form method="POST" action="<?= url('auth/profile') ?>">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del Perfil</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Rol</label>
                            <input type="text" class="form-control" value="<?= e($_SESSION['rol_nombre']) ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Cambiar Contraseña</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Contraseña Actual</label>
                            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="new_password" class="form-control" autocomplete="new-password">
                            <small class="form-hint">Mínimo 6 caracteres</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" name="confirm_password" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Guardar Cambios</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'Mi Perfil';
include APP_PATH . '/views/layouts/main.php';
?>
