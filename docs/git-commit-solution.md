# Git Commit Solution For 4 Students

The cahier des charges says each person must use Git commits. The clean solution is: each student makes at least one real commit with their own name and email.

## Before Starting

Each student should configure Git on the PC before making their commit:

```bash
git config user.name "Student Name"
git config user.email "student.email@example.com"
```

To check the current identity:

```bash
git config user.name
git config user.email
```

## Suggested Commit Split

### Student 1 - Authentication And Users

Files to change or review:
- `app/Models/User.php`
- `app/Services/Auth.php`
- `app/Core/Session.php`
- `public/login.php`
- `public/register.php`
- `public/logout.php`
- `public/api/auth.php`

Commit command example:

```bash
git add app/Models/User.php app/Services/Auth.php app/Core/Session.php public/login.php public/register.php public/logout.php public/api/auth.php
git commit -m "Implement authentication and user session flow"
```

### Student 2 - Hotels, Rooms, Photos, And Upload

Files to change or review:
- `app/Models/Hotel.php`
- `app/Models/Room.php`
- `app/Core/helpers.php`
- `public/rooms.php`
- `public/reserve.php`
- `public/admin/hotels.php`
- `public/admin/rooms.php`
- `public/assets/js/rooms.js`
- `public/assets/js/reserve.js`
- `public/uploads/hotels/.gitkeep`

Commit command example:

```bash
git add app/Models/Hotel.php app/Models/Room.php app/Core/helpers.php public/rooms.php public/reserve.php public/admin/hotels.php public/admin/rooms.php public/assets/js/rooms.js public/assets/js/reserve.js public/uploads/hotels/.gitkeep
git commit -m "Implement hotel and room management with photo upload"
```

### Student 3 - Reservations, Dashboard, And Support

Files to change or review:
- `app/Models/Reservation.php`
- `app/Models/SupportTicket.php`
- `public/dashboard.php`
- `public/api/reservations.php`
- `public/admin/reservations.php`
- `public/admin/customer-service.php`

Commit command example:

```bash
git add app/Models/Reservation.php app/Models/SupportTicket.php public/dashboard.php public/api/reservations.php public/admin/reservations.php public/admin/customer-service.php
git commit -m "Implement reservations dashboard and support tickets"
```

### Student 4 - Admin, Payment, Invoice, Database, And Docs

Files to change or review:
- `app/Models/Dashboard.php`
- `app/Models/Payment.php`
- `app/Services/InvoiceService.php`
- `public/admin/index.php`
- `public/payment.php`
- `public/invoice.php`
- `public/assets/js/charts.js`
- `database/schema.sql`
- `docs/student-task-code-guide.md`
- `docs/student-task-code-guide.pdf`

Commit command example:

```bash
git add app/Models/Dashboard.php app/Models/Payment.php app/Services/InvoiceService.php public/admin/index.php public/payment.php public/invoice.php public/assets/js/charts.js database/schema.sql docs/student-task-code-guide.md docs/student-task-code-guide.pdf
git commit -m "Implement admin stats payment invoices and documentation"
```

## Check The Result

After all students commit, run:

```bash
git shortlog -sne --all
git log --oneline --decorate --max-count=10
```

The teacher should see multiple names in `git shortlog`.

## Important

Do not use fake author names for work that students did not do. The safest answer is that each student opens the project, configures their own Git identity, makes a small real improvement or review change in their section, and commits it.
