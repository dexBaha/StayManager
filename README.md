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


## Setup

1. Create a MySQL database named `hotel_management`.
2. Import `database/schema.sql`.
3. Update database credentials in `config/config.php`.
4. Start PHP server:

```bash
php -S localhost:8000 -t public
```

5. Open `http://localhost:8000`.

## XAMPP Apache on Port 8080

If Apache is configured on port `8080`, place the project in:

```text
C:\xampp\htdocs\hotel-management
```

Then open:

```text
http://localhost:8080/hotel-management/public
```

MySQL still uses port `3306` by default. Start MySQL from the XAMPP control panel before opening the site.

## API


