<?php
ob_start();
$headerActions = '<a href="' . url('personas') . '" class="btn"><i class="ti ti-arrow-left"></i> Volver</a>';
?>

<form method="POST" action="<?= url('personas/store') ?>">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Datos Personales</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label required">Tipo Documento</label>
                    <select name="tipo_documento" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="DNI" selected>DNI</option>
                        <option value="PASAPORTE">Pasaporte</option>
                        <option value="CE">Carnet de Extranjería</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">N° Documento</label>
                    <input type="text" name="numero_documento" class="form-control" required maxlength="20">
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Nombres</label>
                    <input type="text" name="nombres" class="form-control" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" class="form-control" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Apellido Materno</label>
                    <input type="text" name="apellido_materno" class="form-control" required maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Género</label>
                    <select name="genero" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="OTRO">Otro</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Datos de Contacto</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-control" required maxlength="100">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" maxlength="20">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Celular</label>
                    <input type="text" name="celular" class="form-control" maxlength="20">
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Distrito</label>
                    <input type="text" name="distrito" class="form-control" maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Provincia</label>
                    <input type="text" name="provincia" class="form-control" maxlength="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <input type="text" name="departamento" class="form-control" maxlength="100">
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="<?= url('personas') ?>" class="btn">Cancelar</a>
            <button type="submit" class="btn btn-primary ms-2"><i class="ti ti-device-floppy"></i> Guardar</button>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
$pageTitle = 'Nueva Persona';
include APP_PATH . '/views/layouts/main.php';
?>