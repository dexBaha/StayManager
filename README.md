# Hotel Management OOP PHP Project

Simple hotel management website built with HTML, CSS, JavaScript, PHP OOP, and PDO.

## Features

- User and admin authentication
- User CRUD for reservations
- Admin CRUD for hotels, rooms, reservations, and users
- PDO database connection
- OOP classes for User, Auth, Hotel, Room, Reservation, and Dashboard stats
- JavaScript chart on the admin dashboard
- JSON API endpoints for auth, rooms, reservations, and admin stats
- Git-ready structure with `.gitignore`

## Student Responsibilities

- Student 1: `User` class and `Auth` system
- Student 2: `Hotel` and `Room` classes
- Student 3: `Reservation` system
- Student 4: Admin dashboard and JavaScript stats chart

## Setup

1. Create a MySQL database named `hotel_management`.
2. Import `database/schema.sql`.
3. Update database credentials in `config/config.php`.
4. Start PHP server:

```bash
php -S localhost:8000 -t public
```

5. Open `http://localhost:8000`.

## API

API documentation is available in `docs/API.md`.

Examples:

- `GET /api/rooms.php`
- `POST /api/auth.php?action=login`
- `GET /api/reservations.php`
- `GET /api/stats.php`

## Default Accounts

Admin:

- Email: `admin@hotel.com`
- Password: `admin123`

User:

- Email: `user@hotel.com`
- Password: `user123`
