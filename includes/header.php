<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../resource/php/funct/funct.php';
require_once __DIR__ . '/../resource/php/class/Ticket.php';
require_once __DIR__ . '/../resource/php/class/TicketComment.php';
require_once __DIR__ . '/../resource/php/class/User.php';
require_once __DIR__ . '/../resource/php/class/SystemProject.php';
require_once __DIR__ . '/auth.php';

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?></title>
    <!-- Prevent flash of wrong theme -->
    <script>(function(){var t=localStorage.getItem('tix-theme')||'light';document.documentElement.setAttribute('data-theme',t);document.documentElement.setAttribute('data-bs-theme',t);})();</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/resource/css/theme.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="<?= homeUrl() ?>">
            <span class="brand-mark"><i class="bi bi-ticket-perforated-fill"></i></span>
            <?= APP_NAME ?>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <?php if (isLoggedIn()): ?>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">

                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin_dashboard.php">
                        <i class="bi bi-shield-lock"></i>Admin
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/dashboard.php">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'manage_systems.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/manage_systems.php">
                        <i class="bi bi-grid"></i>Systems
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'manage_users.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/manage_users.php">
                        <i class="bi bi-people"></i>Users
                    </a>
                </li>

                <?php elseif (isDeveloper()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/dashboard.php">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'manage_systems.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/manage_systems.php">
                        <i class="bi bi-grid"></i>Systems
                    </a>
                </li>

                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'my_tickets.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/my_tickets.php">
                        <i class="bi bi-ticket"></i>My Tickets
                    </a>
                </li>
                <?php endif; ?>

                <?php if (!isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'submit_ticket.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/submit_ticket.php">
                        <i class="bi bi-plus-circle"></i>Submit Ticket
                    </a>
                </li>
                <?php endif; ?>

            </ul>
            <ul class="navbar-nav align-items-center gap-2">
                <li class="nav-item">
                    <button class="theme-toggle" onclick="tixToggleTheme()" aria-label="Toggle theme" title="Toggle dark/light mode">
                        <i class="bi bi-sun t-icon t-sun"></i>
                        <i class="bi bi-moon t-icon t-moon"></i>
                    </button>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i><?= e($_SESSION['name']) ?>
                        <?php if (isAdmin()): ?>
                        <span class="badge badge-admin ms-1">Admin</span>
                        <?php elseif (isDeveloper()): ?>
                        <span class="badge badge-developer ms-1">Dev</span>
                        <?php else: ?>
                        <span class="badge badge-user ms-1">User</span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout.php">
                            <i class="bi bi-box-arrow-right"></i>Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <?php else: ?>
        <div class="ms-auto d-flex align-items-center">
            <button class="theme-toggle" onclick="tixToggleTheme()" aria-label="Toggle theme">
                <i class="bi bi-sun t-icon t-sun"></i>
                <i class="bi bi-moon t-icon t-moon"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container-fluid px-4 py-4">
