<?php

class TicketComment
{
    public static function getByTicket(PDO $pdo, int $ticketId): array
    {
        $stmt = $pdo->prepare("
            SELECT c.*, u.name AS author_name, u.role AS author_role
            FROM ticket_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.ticket_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }

    public static function add(PDO $pdo, int $ticketId, int $userId, string $comment): void
    {
        $stmt = $pdo->prepare('INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (?, ?, ?)');
        $stmt->execute([$ticketId, $userId, $comment]);
    }
}
