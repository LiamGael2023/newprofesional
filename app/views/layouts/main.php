<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?= $title ?? APP_NAME ?></title>
    <!-- CSS files -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler-flags.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler-payments.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler-vendors.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Sidebar -->
        <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="<?= url('dashboard') ?>">
                        <span class="text-white">🎓 Colegio Profesional</span>
                    </a>
                </h1>
                <div class="navbar-nav flex-row d-lg-none">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm">
                                <i class="ti ti-user"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="<?= url('auth/profile') ?>" class="dropdown-item">Mi Perfil</a>
                            <a href="<?= url('auth/logout') ?>" class="dropdown-item">Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard') ?>">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-dashboard"></i>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        <?php if (hasPermission('personas') || hasPermission('all')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('personas') ?>">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-user"></i>
                                </span>
                                <span class="nav-link-title">Personas</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (hasPermission('colegiados') || hasPermission('all')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('colegiados') ?>">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-school"></i>
                                </span>
                                <span class="nav-link-title">Colegiados</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (hasPermission('aportaciones') || hasPermission('all')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('aportaciones') ?>">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-coin"></i>
                                </span>
                                <span class="nav-link-title">Aportaciones</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (hasPermission('caja') || hasPermission('all')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('caja') ?>">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-cash"></i>
                                </span>
                                <span class="nav-link-title">Caja</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (hasPermission('reportes') || hasPermission('all')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" role="button">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-chart-bar"></i>
                                </span>
                                <span class="nav-link-title">Reportes</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= url('reportes/colegiados') ?>">
                                    Colegiados
                                </a>
                                <a class="dropdown-item" href="<?= url('reportes/aportaciones') ?>">
                                    Aportaciones
                                </a>
                                <a class="dropdown-item" href="<?= url('reportes/pagos') ?>">
                                    Pagos/Recaudación
                                </a>
                                <a class="dropdown-item" href="<?= url('reportes/morosidad') ?>">
                                    Morosidad
                                </a>
                                <a class="dropdown-item" href="<?= url('reportes/estadisticas') ?>">
                                    Estadísticas
                                </a>
                                <a class="dropdown-item" href="<?= url('reportes/auditoria') ?>">
                                    Auditoría
                                </a>
                            </div>
                        </li>
                        <?php endif; ?>

                        <li class="nav-item mt-auto">
                            <a class="nav-link" href="<?= url('auth/profile') ?>">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-settings"></i>
                                </span>
                                <span class="nav-link-title">Mi Perfil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('auth/logout') ?>">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-logout"></i>
                                </span>
                                <span class="nav-link-title">Cerrar Sesión</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Header -->
        <header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm bg-blue-lt">
                                <i class="ti ti-user"></i>
                            </span>
                            <div class="d-none d-xl-block ps-2">
                                <div><?= currentUsername() ?></div>
                                <div class="mt-1 small text-muted"><?= $_SESSION['rol_nombre'] ?? 'Usuario' ?></div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="<?= url('auth/profile') ?>" class="dropdown-item">Mi Perfil</a>
                            <div class="dropdown-divider"></div>
                            <a href="<?= url('auth/logout') ?>" class="dropdown-item">Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-wrapper">
            <!-- Page header -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">
                                <?= $pageTitle ?? 'Panel de Control' ?>
                            </h2>
                        </div>
                        <?php if (isset($headerActions)): ?>
                        <div class="col-auto ms-auto d-print-none">
                            <?= $headerActions ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <?php
                    // Mostrar mensajes flash
                    if (isset($_SESSION['flash'])) {
                        $flash = $_SESSION['flash'];
                        unset($_SESSION['flash']);
                        $alertType = $flash['type'] === 'error' ? 'danger' : $flash['type'];
                    ?>
                    <div class="alert alert-<?= $alertType ?> alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <?php if ($flash['type'] === 'success'): ?>
                                <i class="ti ti-check icon alert-icon"></i>
                                <?php elseif ($flash['type'] === 'error' || $flash['type'] === 'danger'): ?>
                                <i class="ti ti-alert-circle icon alert-icon"></i>
                                <?php elseif ($flash['type'] === 'warning'): ?>
                                <i class="ti ti-alert-triangle icon alert-icon"></i>
                                <?php else: ?>
                                <i class="ti ti-info-circle icon alert-icon"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?= e($flash['message']) ?>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    <?php } ?>

                    <?= $content ?? '' ?>
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    <a href="<?= url('') ?>" class="link-secondary">v<?= APP_VERSION ?></a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    &copy; <?= date('Y') ?> Sistema de Gestión de Colegio Profesional
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Libs JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script src="<?= url('public/js/main.js') ?>"></script>
</body>
</html>
