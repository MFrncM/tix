<?php

class User
{
    public static function findByEmail(PDO $pdo, string $email)
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function findById(PDO $pdo, int $id)
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function emailExists(PDO $pdo, string $email, int $excludeId = 0): bool
    {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$email, $excludeId]);
        return (bool) $stmt->fetch();
    }

    public static function login(PDO $pdo, string $email, string $password)
    {
        $user = self::findByEmail($pdo, $email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Returns the assigned role ('admin' | 'developer' | 'user')
    public static function register(PDO $pdo, string $name, string $email, string $password): string
    {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $role  = $count === 0 ? 'admin' : 'user';
        $hash  = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $hash, $role]);
        return $role;
    }

    public static function getAll(PDO $pdo): array
    {
        $stmt = $pdo->query(
            'SELECT u.*, COUNT(t.id) AS ticket_count
             FROM users u
             LEFT JOIN tickets t ON t.submitted_by = u.id
             GROUP BY u.id
             ORDER BY u.created_at ASC'
        );
        return $stmt->fetchAll();
    }

    public static function updateRole(PDO $pdo, int $id, string $role): void
    {
        $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->execute([$role, $id]);
    }

    public static function updateProfile(PDO $pdo, int $id, string $name, string $email): void
    {
        $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        $stmt->execute([$name, $email, $id]);
    }

    public static function delete(PDO $pdo, int $id): void
    {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function countByRole(PDO $pdo, string $role): int
    {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
        $stmt->execute([$role]);
        return (int) $stmt->fetchColumn();
    }

    public static function ROLES(): array
    {
        return ['user', 'developer', 'admin'];
    }
}
