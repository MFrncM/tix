<?php

class User
{
    public static function findByEmail(PDO $pdo, string $email)
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function emailExists(PDO $pdo, string $email): bool
    {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    // Returns the verified user row on success, false on failure
    public static function login(PDO $pdo, string $email, string $password)
    {
        $user = self::findByEmail($pdo, $email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Returns the assigned role ('developer' | 'user')
    public static function register(PDO $pdo, string $name, string $email, string $password): string
    {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $role  = $count === 0 ? 'developer' : 'user';
        $hash  = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $hash, $role]);
        return $role;
    }
}
