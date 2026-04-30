<?php

class Room
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        $sql = 'SELECT rooms.*, hotels.name AS hotel_name, hotels.city, hotels.country, hotels.stars, hotels.photo_url, hotels.description AS hotel_description
                FROM rooms
                JOIN hotels ON hotels.id = rooms.hotel_id
                ORDER BY rooms.id DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function available(): array
    {
        $sql = 'SELECT rooms.*, hotels.name AS hotel_name, hotels.city, hotels.country, hotels.stars, hotels.photo_url, hotels.description AS hotel_description
                FROM rooms
                JOIN hotels ON hotels.id = rooms.hotel_id
                WHERE rooms.status = "available"
                ORDER BY hotels.country ASC, hotels.name ASC, rooms.price ASC';

        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT rooms.*, hotels.name AS hotel_name, hotels.city, hotels.country, hotels.stars, hotels.photo_url
             FROM rooms
             JOIN hotels ON hotels.id = rooms.hotel_id
             WHERE rooms.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(int $hotelId, string $number, string $type, float $price, string $status): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO rooms (hotel_id, room_number, type, price, status) VALUES (?, ?, ?, ?, ?)'
        );

        return $stmt->execute([$hotelId, $number, $type, $price, $status]);
    }

    public function update(int $id, int $hotelId, string $number, string $type, float $price, string $status): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE rooms SET hotel_id = ?, room_number = ?, type = ?, price = ?, status = ? WHERE id = ?'
        );

        return $stmt->execute([$hotelId, $number, $type, $price, $status, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM rooms WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
