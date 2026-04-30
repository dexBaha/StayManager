CREATE DATABASE IF NOT EXISTS hotel_management
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE hotel_management;

DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS hotels;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id INT NOT NULL,
    room_number VARCHAR(30) NOT NULL,
    type VARCHAR(80) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    status ENUM('available', 'reserved', 'maintenance') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rooms_hotel FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_reservations_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role) VALUES
('Admin Hotel', 'admin@hotel.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin'),
('User Demo', 'user@hotel.com', 'e606e38b0d8c19b24cf0ee3808183162ea7cd63ff7912dbb22b5e803286b4446', 'user');

INSERT INTO hotels (name, city, address, description) VALUES
('Royal Tunis Hotel', 'Tunis', 'Avenue Habib Bourguiba, Tunis', 'A comfortable city hotel for business and leisure stays.'),
('Marina Sousse Resort', 'Sousse', 'Port El Kantaoui, Sousse', 'A seaside hotel close to restaurants, beaches, and family activities.'),
('Desert Palm Hotel', 'Tozeur', 'Route Touristique, Tozeur', 'A calm hotel near desert excursions and oasis tours.');

INSERT INTO rooms (hotel_id, room_number, type, price, status) VALUES
(1, '101', 'Single', 85.00, 'available'),
(1, '205', 'Double', 130.00, 'available'),
(2, '302', 'Suite', 220.00, 'available'),
(2, '114', 'Family', 180.00, 'maintenance'),
(3, '22', 'Double', 115.00, 'available');

INSERT INTO reservations (user_id, room_id, check_in, check_out, total_price, status) VALUES
(2, 1, '2026-05-10', '2026-05-12', 170.00, 'confirmed'),
(2, 3, '2026-06-01', '2026-06-04', 660.00, 'pending');
