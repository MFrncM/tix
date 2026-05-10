<?php

class SystemProject
{
    // Returns all systems with their ticket counts
    public static function getAll(PDO $pdo): array
    {
        return $pdo->query("
            SELECT s.*, COUNT(t.id) AS ticket_count
            FROM systems s
            LEFT JOIN tickets t ON t.system_id = s.id
            GROUP BY s.id
            ORDER BY s.name
        ")->fetchAll();
    }

    public static function getActive(PDO $pdo): array
    {
        return $pdo->query('SELECT * FROM systems WHERE is_active = 1 ORDER BY name')->fetchAll();
    }

    public static function add(PDO $pdo, string $name, ?string $description): void
    {
        $stmt = $pdo->prepare('INSERT INTO systems (name, description) VALUES (?, ?)');
        $stmt->execute([$name, $description ?: null]);
    }

    public static function toggle(PDO $pdo, int $id): void
    {
        $stmt = $pdo->prepare('UPDATE systems SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function nameExists(PDO $pdo, string $name): bool
    {
        $stmt = $pdo->prepare('SELECT id FROM systems WHERE name = ?');
        $stmt->execute([$name]);
        return (bool) $stmt->fetch();
    }

    public static function isActiveAndValid(PDO $pdo, int $id): bool
    {
        $stmt = $pdo->prepare('SELECT id FROM systems WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        return (bool) $stmt->fetch();
    }
}
