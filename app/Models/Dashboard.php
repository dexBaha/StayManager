<?php

class Dashboard
{
    public function __construct(private PDO $db)
    {
    }

    public function counts(): array
    {
        $counts = $this->db->query(
            'SELECT
                (SELECT COUNT(*) FROM users) AS users,
                (SELECT COUNT(*) FROM hotels) AS hotels,
                (SELECT COUNT(*) FROM rooms) AS rooms,
                (SELECT COUNT(*) FROM reservations) AS reservations,
                (SELECT COUNT(*) FROM support_tickets WHERE status = "open") AS support'
        )->fetch();

        return [
            'users' => (int) $counts['users'],
            'hotels' => (int) $counts['hotels'],
            'rooms' => (int) $counts['rooms'],
            'reservations' => (int) $counts['reservations'],
            'support' => (int) $counts['support'],
        ];
    }

    public function reservationsByStatus(): array
    {
        $stmt = $this->db->query('SELECT status, COUNT(*) AS total FROM reservations GROUP BY status');
        return $stmt->fetchAll();
    }

    public function revenue(): float
    {
        return (float) $this->db->query('SELECT COALESCE(SUM(total_price), 0) FROM reservations WHERE status = "confirmed"')->fetchColumn();
    }
}
