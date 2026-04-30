<?php

class Hotel
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM hotels ORDER BY id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM hotels WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(
        string $name,
        string $city,
        string $country,
        int $stars,
        string $photoUrl,
        string $address,
        string $description
    ): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO hotels (name, city, country, stars, photo_url, address, description)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([$name, $city, $country, $stars, $photoUrl, $address, $description]);
    }

    public function update(
        int $id,
        string $name,
        string $city,
        string $country,
        int $stars,
        string $photoUrl,
        string $address,
        string $description
    ): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE hotels SET name = ?, city = ?, country = ?, stars = ?, photo_url = ?, address = ?, description = ? WHERE id = ?'
        );

        return $stmt->execute([$name, $city, $country, $stars, $photoUrl, $address, $description, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM hotels WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
