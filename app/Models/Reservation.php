<?php

class Reservation
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $sql = 'SELECT reservations.*, users.name AS user_name, rooms.room_number, hotels.name AS hotel_name
                FROM reservations
                JOIN users ON users.id = reservations.user_id
                JOIN rooms ON rooms.id = reservations.room_id
                JOIN hotels ON hotels.id = rooms.hotel_id
                ORDER BY reservations.id DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT reservations.*, rooms.room_number, rooms.type, hotels.name AS hotel_name
             FROM reservations
             JOIN rooms ON rooms.id = reservations.room_id
             JOIN hotels ON hotels.id = rooms.hotel_id
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

    public function create(int $userId, int $roomId, string $checkIn, string $checkOut): bool
    {
        if (!$this->validDateRange($checkIn, $checkOut)) {
            return false;
        }

        $room = $this->getRoom($roomId);

        if (!$room || !$this->roomCanBeReserved($roomId, $room)) {
            return false;
        }

        $nights = max(1, (new DateTime($checkIn))->diff(new DateTime($checkOut))->days);
        $total = $nights * (float) $room['price'];

        $stmt = $this->db->prepare(
            'INSERT INTO reservations (user_id, room_id, check_in, check_out, total_price, status)
             VALUES (?, ?, ?, ?, ?, "pending")'
        );

        return $stmt->execute([$userId, $roomId, $checkIn, $checkOut, $total]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE reservations SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function updateDates(int $id, int $userId, string $checkIn, string $checkOut): bool
    {
        if (!$this->validDateRange($checkIn, $checkOut)) {
            return false;
        }

        $reservation = $this->find($id);

        if (!$reservation || (int) $reservation['user_id'] !== $userId) {
            return false;
        }

        $room = $this->getRoom((int) $reservation['room_id']);
        $nights = max(1, (new DateTime($checkIn))->diff(new DateTime($checkOut))->days);
        $total = $nights * (float) $room['price'];

        $stmt = $this->db->prepare(
            'UPDATE reservations SET check_in = ?, check_out = ?, total_price = ?, status = "pending" WHERE id = ?'
        );

        return $stmt->execute([$checkIn, $checkOut, $total, $id]);
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

    private function validDateRange(string $checkIn, string $checkOut): bool
    {
        return new DateTimeImmutable($checkOut) > new DateTimeImmutable($checkIn);
    }
}
