<?php

class Room
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $sql = 'SELECT rooms.*, hotels.name AS hotel_name, hotels.city, hotels.country, hotels.stars, hotels.photo_url, hotels.description AS hotel_description,
                       MAX(CASE WHEN reservations.status IN ("pending", "confirmed") AND reservations.check_out >= CURDATE() THEN reservations.check_out END) AS reserved_until
                FROM rooms
                JOIN hotels ON hotels.id = rooms.hotel_id
                LEFT JOIN reservations ON reservations.room_id = rooms.id
                GROUP BY rooms.id
                ORDER BY rooms.id DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function available(): array
    {
        $sql = 'SELECT rooms.*, hotels.name AS hotel_name, hotels.city, hotels.country, hotels.stars, hotels.photo_url, hotels.description AS hotel_description,
                       MAX(CASE WHEN reservations.status IN ("pending", "confirmed") AND reservations.check_out >= CURDATE() THEN reservations.check_out END) AS reserved_until
                FROM rooms
                JOIN hotels ON hotels.id = rooms.hotel_id
                LEFT JOIN reservations ON reservations.room_id = rooms.id
                GROUP BY rooms.id
                ORDER BY hotels.country ASC, hotels.name ASC, rooms.price ASC';

        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT rooms.*, hotels.name AS hotel_name, hotels.city, hotels.country, hotels.stars, hotels.photo_url,
                    MAX(CASE WHEN reservations.status IN ("pending", "confirmed") AND reservations.check_out >= CURDATE() THEN reservations.check_out END) AS reserved_until
             FROM rooms
             JOIN hotels ON hotels.id = rooms.hotel_id
             LEFT JOIN reservations ON reservations.room_id = rooms.id
             WHERE rooms.id = ?
             GROUP BY rooms.id'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(
        int $hotelId,
        string $number,
        string $type,
        float $price,
        string $status,
        ?string $unavailableUntil = null,
        string $description = '',
        string $amenities = ''
    ): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO rooms (hotel_id, room_number, type, price, status, unavailable_until, description, amenities)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([$hotelId, $number, $type, $price, $status, $unavailableUntil ?: null, $description, $amenities]);
    }

    public function update(
        int $id,
        int $hotelId,
        string $number,
        string $type,
        float $price,
        string $status,
        ?string $unavailableUntil = null,
        string $description = '',
        string $amenities = ''
    ): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE rooms
             SET hotel_id = ?, room_number = ?, type = ?, price = ?, status = ?, unavailable_until = ?, description = ?, amenities = ?
             WHERE id = ?'
        );

        return $stmt->execute([$hotelId, $number, $type, $price, $status, $unavailableUntil ?: null, $description, $amenities, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM rooms WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
