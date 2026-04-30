<?php

class Dashboard
{
    public function __construct(private PDO $db)
    {
    }

    public function counts(): array
    {
        return [
            'users' => (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'hotels' => (int) $this->db->query('SELECT COUNT(*) FROM hotels')->fetchColumn(),
            'rooms' => (int) $this->db->query('SELECT COUNT(*) FROM rooms')->fetchColumn(),
            'reservations' => (int) $this->db->query('SELECT COUNT(*) FROM reservations')->fetchColumn(),
            'support' => (int) $this->db->query('SELECT COUNT(*) FROM support_tickets WHERE status = "open"')->fetchColumn(),
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
