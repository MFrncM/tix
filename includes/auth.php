<?php
require_once __DIR__ . '/config.php';

function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function requireDeveloper(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'developer') {
        header('Location: ' . BASE_URL . '/my_tickets.php');
        exit;
    }
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isDeveloper(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'developer';
}

