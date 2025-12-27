<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? APP_NAME ?></title>
    <link rel="stylesheet" href="<?= url('public/css/style.css') ?>">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3><?= APP_NAME ?></h3>
                <small>v<?= APP_VERSION ?></small>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="<?= url('dashboard') ?>">
                        <i>📊</i> Dashboard
                    </a>
                </li>

                <?php if (hasPermission('personas') || hasPermission('all')): ?>
                <li>
                    <a href="<?= url('personas') ?>">
                        <i>👤</i> Personas
                    </a>
                </li>
                <?php endif; ?>

                <?php if (hasPermission('colegiados') || hasPermission('all')): ?>
                <li>
                    <a href="<?= url('colegiados') ?>">
                        <i>🎓</i> Colegiados
                    </a>
                </li>
                <?php endif; ?>

                <?php if (hasPermission('aportaciones') || hasPermission('all')): ?>
                <li>
                    <a href="<?= url('aportaciones') ?>">
                        <i>💰</i> Aportaciones
                    </a>
                </li>
                <?php endif; ?>

                <?php if (hasPermission('caja') || hasPermission('all')): ?>
                <li>
                    <a href="<?= url('caja') ?>">
                        <i>🏦</i> Caja
                    </a>
                </li>
                <?php endif; ?>

                <?php if (hasPermission('reportes') || hasPermission('all')): ?>
                <li>
                    <a href="<?= url('reportes') ?>">
                        <i>📈</i> Reportes
                    </a>
                </li>
                <?php endif; ?>

                <li>
                    <a href="<?= url('auth/profile') ?>">
                        <i>⚙️</i> Mi Perfil
                    </a>
                </li>

                <li>
                    <a href="<?= url('auth/logout') ?>">
                        <i>🚪</i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="main-header">
                <h1><?= $pageTitle ?? 'Panel de Control' ?></h1>

                <div class="user-info">
                    <span>👤 <?= currentUsername() ?></span>
                    <span>|</span>
                    <span><?= $_SESSION['rol_nombre'] ?? 'Usuario' ?></span>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <?php
                // Mostrar mensajes flash
                $flash = null;
                if (isset($_SESSION['flash'])) {
                    $flash = $_SESSION['flash'];
                    unset($_SESSION['flash']);
                }

                if ($flash):
                ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <?= e($flash['message']) ?>
                </div>
                <?php endif; ?>

                <!-- Contenido de la página -->
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>

    <script src="<?= url('public/js/main.js') ?>"></script>
</body>
</html>
