<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= url('public/css/style.css') ?>">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2>🎓 Colegio Profesional</h2>

            <?php if (isset($errors['login'])): ?>
                <div class="alert alert-danger">
                    <?= e($errors['login'][0]) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('auth/login') ?>">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" class="form-control"
                           value="<?= e($username ?? '') ?>" required autofocus>
                    <?php if (isset($errors['username'])): ?>
                        <small style="color: red;"><?= e($errors['username'][0]) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <?php if (isset($errors['password'])): ?>
                        <small style="color: red;"><?= e($errors['password'][0]) ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Iniciar Sesión
                </button>
            </form>

            <div style="margin-top: 20px; text-align: center; color: #7f8c8d; font-size: 12px;">
                <p>Usuario por defecto: <strong>admin</strong></p>
                <p>Contraseña: <strong>admin123</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
