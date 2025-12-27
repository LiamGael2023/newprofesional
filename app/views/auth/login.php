<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Iniciar Sesión - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>
</head>
<body class="d-flex flex-column bg-white">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <h1>🎓 Colegio Profesional</h1>
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Iniciar Sesión</h2>
                    <?php if (isset($errors['login'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <div class="d-flex">
                            <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
                            <div><?= e($errors['login'][0]) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <form method="POST" action="<?= url('auth/login') ?>" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="username" value="<?= e($username ?? '') ?>" placeholder="Ingresa tu usuario" autofocus required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center text-muted mt-3">
                Usuario por defecto: <strong>admin</strong> / Contraseña: <strong>admin123</strong>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>