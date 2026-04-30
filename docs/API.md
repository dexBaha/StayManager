# Hotel Management API

The project includes a small JSON API built with PHP, OOP models, sessions, and PDO.

Base URL:

```text
http://localhost:8000/api
```

## Auth

### Login

```http
POST /api/auth.php?action=login
Content-Type: application/json
```

```json
{
  "email": "admin@hotel.com",
  "password": "admin123"
}
```

### Register

```http
POST /api/auth.php?action=register
Content-Type: application/json
```

```json
{
  "name": "New User",
  "email": "new@example.com",
  "password": "user123"
}
```

### Logout

```http
POST /api/auth.php?action=logout
```

## Rooms

### List Available Rooms

```http
GET /api/rooms.php
```

### Get One Room

```http
GET /api/rooms.php?id=1
```

## Reservations

Reservation routes require a logged-in session.

### List Reservations

```http
GET /api/reservations.php
```

Admins receive all reservations. Users receive only their own reservations.

### Create Reservation

```http
POST /api/reservations.php
Content-Type: application/json
```

```json
{
  "room_id": 1,
  "check_in": "2026-05-10",
  "check_out": "2026-05-12"
}
```

### Delete Reservation

```http
DELETE /api/reservations.php?id=1
```

## Admin Stats

Stats require an admin session.

```http
GET /api/stats.php
```

Returns user, hotel, room, reservation counts, confirmed revenue, and reservation status totals.

