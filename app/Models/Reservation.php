<?php

class Reservation
{
    private const PAYMENT_JOIN = 'LEFT JOIN payments ON payments.reservation_id = reservations.id';

    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $sql = 'SELECT reservations.*, users.name AS user_name, rooms.room_number, hotels.name AS hotel_name,
                       payments.status AS payment_status
                FROM reservations
                JOIN users ON users.id = reservations.user_id
                JOIN rooms ON rooms.id = reservations.room_id
                JOIN hotels ON hotels.id = rooms.hotel_id
                ' . self::PAYMENT_JOIN . '
                ORDER BY reservations.id DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT reservations.*, rooms.room_number, rooms.type, hotels.name AS hotel_name,
                    payments.status AS payment_status, payments.method AS payment_method, payments.paid_at
             FROM reservations
             JOIN rooms ON rooms.id = reservations.room_id
             JOIN hotels ON hotels.id = rooms.hotel_id
             ' . self::PAYMENT_JOIN . '
             WHERE reservations.user_id = ?
             ORDER BY reservations.id DESC'
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findDetailed(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT reservations.*, users.name AS user_name, users.email AS user_email,
                    rooms.room_number, rooms.type, rooms.price,
                    payments.status AS payment_status, payments.method AS payment_method, payments.card_last4, payments.paid_at,
                    hotels.name AS hotel_name, hotels.city, hotels.country
             FROM reservations
             JOIN users ON users.id = reservations.user_id
             JOIN rooms ON rooms.id = reservations.room_id
             JOIN hotels ON hotels.id = rooms.hotel_id
             ' . self::PAYMENT_JOIN . '
             WHERE reservations.id = ?'
        );
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function create(int $userId, int $roomId, string $checkIn, string $checkOut): bool
    {
        return $this->createAndReturnId($userId, $roomId, $checkIn, $checkOut) !== null;
    }

    public function createAndReturnId(int $userId, int $roomId, string $checkIn, string $checkOut): ?int
    {
        if (!isValidStayRange($checkIn, $checkOut)) {
            return null;
        }

        $room = $this->getRoom($roomId);

        if (!$room || !$this->roomCanBeReserved($roomId, $room)) {
            return null;
        }

        $nights = nightsBetween($checkIn, $checkOut);
        $total = $nights * (float) $room['price'];

        $stmt = $this->db->prepare(
            'INSERT INTO reservations (user_id, room_id, check_in, check_out, total_price, status)
             VALUES (?, ?, ?, ?, ?, "pending")'
        );

        if (!$stmt->execute([$userId, $roomId, $checkIn, $checkOut, $total])) {
            return null;
        }

        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE reservations SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id, ?int $userId = null): bool
    {
        $sql = 'DELETE FROM reservations WHERE id = ?';
        $params = [$id];

        if ($userId !== null) {
            $sql .= ' AND user_id = ?';
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    private function getRoom(int $roomId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM rooms WHERE id = ?');
        $stmt->execute([$roomId]);
        return $stmt->fetch() ?: null;
    }

    private function roomCanBeReserved(int $roomId, array $room): bool
    {
        if (($room['status'] ?? '') !== 'available') {
            return false;
        }

        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM reservations
             WHERE room_id = ?
             AND status IN ("pending", "confirmed")
             AND check_out >= CURDATE()'
        );
        $stmt->execute([$roomId]);

        return (int) $stmt->fetchColumn() === 0;
    }
}
