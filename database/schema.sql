CREATE DATABASE IF NOT EXISTS hotel_management
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE hotel_management;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS support_tickets;
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
    country VARCHAR(100) NOT NULL DEFAULT 'Tunisia',
    stars TINYINT NOT NULL DEFAULT 4,
    photo_url VARCHAR(500) NULL,
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
    unavailable_until DATE NULL,
    description TEXT NULL,
    amenities VARCHAR(255) NULL,
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

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    method VARCHAR(50) NOT NULL,
    card_name VARCHAR(120) NOT NULL,
    card_last4 VARCHAR(4) NOT NULL,
    status ENUM('paid', 'refunded') NOT NULL DEFAULT 'paid',
    paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_reservation FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE
);

CREATE TABLE support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    admin_reply TEXT NULL,
    status ENUM('open', 'answered') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    answered_at TIMESTAMP NULL,
    CONSTRAINT fk_support_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role) VALUES
('Admin Hotel', 'admin@hotel.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin'),
('User Demo', 'user@hotel.com', 'e606e38b0d8c19b24cf0ee3808183162ea7cd63ff7912dbb22b5e803286b4446', 'user');

INSERT INTO hotels (name, city, country, stars, photo_url, address, description) VALUES
('Royal Tunis Hotel', 'Tunis', 'Tunisia', 5, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'Avenue Habib Bourguiba, Tunis', 'A comfortable city hotel for business and leisure stays.'),
('Marina Sousse Resort', 'Sousse', 'Tunisia', 4, 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1200&q=80', 'Port El Kantaoui, Sousse', 'A seaside hotel close to restaurants, beaches, and family activities.'),
('Desert Palm Hotel', 'Tozeur', 'Tunisia', 4, 'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80', 'Route Touristique, Tozeur', 'A calm hotel near desert excursions and oasis tours.'),
('Atlas Sky Palace', 'Marrakech', 'Morocco', 5, 'https://images.unsplash.com/photo-1590073242678-70ee3fc28f8e?auto=format&fit=crop&w=1200&q=80', 'Medina District, Marrakech', 'A refined stay with rooftop views, spa services, and warm Moroccan design.'),
('Paris Lumiere Hotel', 'Paris', 'France', 5, 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80', 'Rue de Rivoli, Paris', 'An elegant city hotel close to museums, shopping streets, and historic landmarks.'),
('The Plaza Hotel', 'New York', 'United States', 5, 'https://commons.wikimedia.org/wiki/Special:FilePath/The%20Plaza%20Hotel.JPG', 'Fifth Avenue at Central Park South, New York', 'A landmark luxury hotel beside Central Park with classic New York service and grand public spaces.'),
('Burj Al Arab Jumeirah', 'Dubai', 'United Arab Emirates', 5, 'https://commons.wikimedia.org/wiki/Special:FilePath/Burj%20Al%20Arab%20during%20sunset.jpg', 'Jumeirah Street, Umm Suqeim 3, Dubai', 'An iconic sail-shaped luxury hotel on its own island with panoramic Arabian Gulf views.'),
('Marina Bay Sands', 'Singapore', 'Singapore', 5, 'https://commons.wikimedia.org/wiki/Special:FilePath/Singapore%20%28SG%29%2C%20Marina%20Bay%20Sands%20Hotel%20--%202019%20--%204458.jpg', '10 Bayfront Avenue, Singapore', 'A large integrated resort with three hotel towers, skyline views, shopping, dining, and entertainment.'),
('Ritz Paris', 'Paris', 'France', 5, 'https://commons.wikimedia.org/wiki/Special:FilePath/Ritz-Paris-Hotel-Exterior.jpg', '15 Place Vendome, Paris', 'A historic palace hotel on Place Vendome known for refined suites, dining, and classic Parisian luxury.');

INSERT INTO rooms (hotel_id, room_number, type, price, status, unavailable_until, description, amenities) VALUES
(1, '101', 'Single', 85.00, 'available', NULL, 'Compact room for one guest with a comfortable bed and practical workspace.', 'WiFi, breakfast included, AC, TV'),
(1, '205', 'Double', 130.00, 'available', NULL, 'Bright double room for couples or friends with city view and daily cleaning.', 'WiFi, breakfast included, AC, minibar, TV'),
(2, '302', 'Suite', 220.00, 'available', NULL, 'Spacious suite with lounge area, premium bathroom, and balcony-style view.', 'WiFi, breakfast included, AC, minibar, sea view, room service'),
(2, '114', 'Family', 180.00, 'maintenance', DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'Large family room with extra beds and a calm layout for longer stays.', 'WiFi, breakfast included, AC, kids beds, TV'),
(3, '22', 'Double', 115.00, 'available', NULL, 'Warm double room close to the oasis tours with quiet interior design.', 'WiFi, breakfast included, AC, parking'),
(4, 'A12', 'Riad Suite', 260.00, 'available', NULL, 'Moroccan-style suite with premium decor, seating area, and relaxing atmosphere.', 'WiFi, breakfast included, AC, spa access, room service'),
(4, 'B07', 'Deluxe Double', 175.00, 'available', NULL, 'Deluxe double room with refined design and comfortable bedding.', 'WiFi, breakfast included, AC, minibar, TV'),
(5, '701', 'Executive Suite', 310.00, 'available', NULL, 'Executive suite designed for luxury stays with elegant view and lounge space.', 'WiFi, breakfast included, AC, minibar, city view, room service'),
(5, '412', 'Classic Double', 190.00, 'available', NULL, 'Classic Parisian double room with soft lighting and practical amenities.', 'WiFi, breakfast included, AC, TV, coffee machine'),
(6, '501', 'Deluxe King', 420.00, 'available', NULL, 'Elegant king room with classic decor, premium bedding, and city views near Central Park.', 'WiFi, breakfast available, AC, minibar, city view, marble bathroom'),
(6, '1201', 'Terrace Suite', 890.00, 'available', NULL, 'Spacious suite with separate living area, refined furnishings, and a private terrace-style setting.', 'WiFi, AC, minibar, lounge area, premium bath, concierge'),
(7, '2501', 'Deluxe Marina Suite', 980.00, 'available', NULL, 'Duplex-style suite with Arabian Gulf views, private reception area, and refined luxury finishes.', 'WiFi, breakfast included, AC, sea view, butler service, jacuzzi'),
(7, '2707', 'Panoramic Suite', 1450.00, 'available', NULL, 'High-floor panoramic suite with sweeping Dubai views and generous living space.', 'WiFi, AC, panoramic view, butler service, lounge, luxury bath'),
(8, '1808', 'Premier Garden View', 520.00, 'available', NULL, 'Modern room with garden-facing views, warm interiors, and easy access to resort amenities.', 'WiFi, AC, garden view, smart TV, minibar, coffee machine'),
(8, '3510', 'Sands Premier Suite', 820.00, 'available', NULL, 'Contemporary suite with separate lounge, skyline views, and premium resort comfort.', 'WiFi, AC, skyline view, lounge area, minibar, bathtub'),
(9, '214', 'Superior Room', 760.00, 'available', NULL, 'Refined Parisian room with elegant finishes, quiet comfort, and Place Vendome atmosphere.', 'WiFi, AC, minibar, marble bathroom, luxury linens'),
(9, '701', 'Suite Vendome', 1320.00, 'available', NULL, 'Prestige suite with classic French design, generous lounge space, and elevated city views.', 'WiFi, AC, lounge area, city view, luxury bath, concierge');

INSERT INTO reservations (user_id, room_id, check_in, check_out, total_price, status) VALUES
(2, 1, '2026-05-10', '2026-05-12', 170.00, 'confirmed'),
(2, 3, '2026-06-01', '2026-06-04', 660.00, 'pending');
