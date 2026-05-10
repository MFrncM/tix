<?php

class Ticket
{
    // ── Constants ───────────────────────────────

    public static function PRIORITIES(): array
    {
        return ['Low', 'Medium', 'High', 'Critical'];
    }

    public static function STATUSES(): array
    {
        return ['Open', 'In Progress', 'Resolved', 'Closed'];
    }

    public static function TYPES(): array
    {
        return ['Bug Fix', 'Feature Request', 'Update', 'Support', 'Other'];
    }

    // ── Queries ─────────────────────────────────

    public static function getStats(PDO $pdo): array
    {
        return $pdo->query("
            SELECT
                SUM(status = 'Open')        AS open_count,
                SUM(status = 'In Progress') AS inprogress_count,
                SUM(status = 'Resolved')    AS resolved_count,
                SUM(status = 'Closed')      AS closed_count,
                COUNT(*)                    AS total
            FROM tickets
        ")->fetch();
    }

    public static function getAll(PDO $pdo, array $filters = []): array
    {
        $where  = '1=1';
        $params = [];

        if (!empty($filters['system_id'])) {
            $where   .= ' AND t.system_id = ?';
            $params[] = $filters['system_id'];
        }
        if (!empty($filters['status'])) {
            $where   .= ' AND t.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $where   .= ' AND t.priority = ?';
            $params[] = $filters['priority'];
        }
        if (!empty($filters['submitted_by'])) {
            $where   .= ' AND t.submitted_by = ?';
            $params[] = $filters['submitted_by'];
        }

        $stmt = $pdo->prepare("
            SELECT t.*, s.name AS system_name, u.name AS submitter_name
            FROM tickets t
            JOIN systems s ON t.system_id = s.id
            JOIN users   u ON t.submitted_by = u.id
            WHERE {$where}
            ORDER BY FIELD(t.priority, 'Critical', 'High', 'Medium', 'Low'), t.created_at DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getById(PDO $pdo, int $id)
    {
        $stmt = $pdo->prepare("
            SELECT t.*, s.name AS system_name, u.name AS submitter_name
            FROM tickets t
            JOIN systems s ON t.system_id = s.id
            JOIN users   u ON t.submitted_by = u.id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ── Mutations ───────────────────────────────

    public static function create(PDO $pdo, array $data): int
    {
        $stmt = $pdo->prepare("
            INSERT INTO tickets (title, description, system_id, ticket_type, priority, submitted_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['system_id'],
            $data['ticket_type'],
            $data['priority'],
            $data['submitted_by'],
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(PDO $pdo, int $id, string $status, string $priority): void
    {
        $stmt = $pdo->prepare('UPDATE tickets SET status = ?, priority = ? WHERE id = ?');
        $stmt->execute([$status, $priority, $id]);
    }

    // ── Validation ──────────────────────────────

    public static function isValidPriority(string $priority): bool
    {
        return in_array($priority, self::PRIORITIES(), true);
    }

    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::STATUSES(), true);
    }

    public static function isValidType(string $type): bool
    {
        return in_array($type, self::TYPES(), true);
    }
}
