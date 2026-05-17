<?php

class Payment
{
    public function __construct(private PDO $db)
    {
    }

    public function findByReservation(int $reservationId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE reservation_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$reservationId]);

        return $stmt->fetch() ?: null;
    }

    public function create(int $reservationId, float $amount, string $method, string $cardName, string $cardLast4): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payments (reservation_id, amount, method, card_name, card_last4, status)
             VALUES (?, ?, ?, ?, ?, "paid")'
        );

        return $stmt->execute([$reservationId, $amount, $method, $cardName, $cardLast4]);
    }
}

