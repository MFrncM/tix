<?php
require_once __DIR__ . '/config.php';

function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . '/my_tickets.php');
        exit;
    }
}

function requireDeveloper(): void {
    requireLogin();
    if (!in_array($_SESSION['role'], ['developer', 'admin'])) {
        header('Location: ' . BASE_URL . '/my_tickets.php');
        exit;
    }
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isDeveloper(): bool {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['developer', 'admin']);
}

function homeUrl(): string {
    if (!isLoggedIn()) return BASE_URL . '/index.php';
    if ($_SESSION['role'] === 'admin')     return BASE_URL . '/admin_dashboard.php';
    if ($_SESSION['role'] === 'developer') return BASE_URL . '/dashboard.php';
    return BASE_URL . '/my_tickets.php';
}
