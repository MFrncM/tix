<?php
// ─────────────────────────────────────────────
//  HTML helpers
// ─────────────────────────────────────────────

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// ─────────────────────────────────────────────
//  Badge renderers
// ─────────────────────────────────────────────

function priorityBadge(string $priority): string {
    $map = [
        'Low'      => 'bg-success',
        'Medium'   => 'bg-primary',
        'High'     => 'bg-warning text-dark',
        'Critical' => 'bg-danger',
    ];
    $class = $map[$priority] ?? 'bg-secondary';
    return "<span class=\"badge {$class}\">" . e($priority) . "</span>";
}

function statusBadge(string $status): string {
    $map = [
        'Open'        => 'bg-primary',
        'In Progress' => 'bg-warning text-dark',
        'Resolved'    => 'bg-success',
        'Closed'      => 'bg-secondary',
    ];
    $class = $map[$status] ?? 'bg-secondary';
    return "<span class=\"badge {$class}\">" . e($status) . "</span>";
}
