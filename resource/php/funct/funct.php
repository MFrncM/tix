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
        'Low'      => 'badge-low',
        'Medium'   => 'badge-medium',
        'High'     => 'badge-high',
        'Critical' => 'badge-critical',
    ];
    $class = $map[$priority] ?? 'badge-closed';
    return "<span class=\"badge {$class}\">" . e($priority) . "</span>";
}

function statusBadge(string $status): string {
    $map = [
        'Open'        => 'badge-open',
        'In Progress' => 'badge-progress',
        'Resolved'    => 'badge-resolved',
        'Closed'      => 'badge-closed',
    ];
    $class = $map[$status] ?? 'badge-closed';
    return "<span class=\"badge {$class}\">" . e($status) . "</span>";
}
