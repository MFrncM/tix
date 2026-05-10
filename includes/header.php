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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/resource/css/index.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/<?= isDeveloper() ? 'dashboard.php' : 'my_tickets.php' ?>">
            <i class="bi bi-ticket-perforated-fill me-1"></i><?= APP_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <?php if (isLoggedIn()): ?>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (isDeveloper()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/dashboard.php">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'manage_systems.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/manage_systems.php">
                        <i class="bi bi-grid me-1"></i>Systems
                    </a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'my_tickets.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/my_tickets.php">
                        <i class="bi bi-ticket me-1"></i>My Tickets
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'submit_ticket.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/submit_ticket.php">
                        <i class="bi bi-plus-circle me-1"></i>Submit Ticket
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= e($_SESSION['name']) ?>
                        <?php if (isDeveloper()): ?>
                        <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">Dev</span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container-fluid px-4 py-4">
