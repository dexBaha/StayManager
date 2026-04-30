<?php

class SupportTicket
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $sql = 'SELECT support_tickets.*, users.name AS user_name, users.email AS user_email
                FROM support_tickets
                JOIN users ON users.id = support_tickets.user_id
                ORDER BY support_tickets.created_at DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function create(int $userId, string $subject, string $message): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO support_tickets (user_id, subject, message, status) VALUES (?, ?, ?, "open")'
        );

        return $stmt->execute([$userId, $subject, $message]);
    }

    public function reply(int $id, string $adminReply): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE support_tickets SET admin_reply = ?, status = "answered", answered_at = NOW() WHERE id = ?'
        );

        return $stmt->execute([$adminReply, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM support_tickets WHERE id = ?');
        return $stmt->execute([$id]);
    }
}

