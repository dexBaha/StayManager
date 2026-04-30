<?php
require_once __DIR__ . '/../app/bootstrap.php';
$pageTitle = 'Hotel Management';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="hero-inner">
        <h1>StayManager</h1>
        <p>Book rooms, manage reservations, and administer hotels from one PHP OOP application.</p>
        <div class="actions">
            <a class="btn" href="<?= e(url('/rooms.php')) ?>">View rooms</a>
            <?php if (!Auth::check()): ?>
                <a class="btn secondary" href="<?= e(url('/register.php')) ?>">Create account</a>
            <?php endif; ?>
        </div>
    </div>
</section>
<main class="container">
    <div class="grid rooms">
        <article class="card">
            <h2>User area</h2>
            <p class="muted">Users can register, log in, browse rooms, create reservations, update reservation dates, and cancel reservations.</p>
        </article>
        <article class="card">
            <h2>Admin area</h2>
            <p class="muted">Admins can manage users, hotels, rooms, reservations, and view dashboard statistics with JavaScript charts.</p>
        </article>
        <article class="card">
            <h2>PDO + OOP</h2>
            <p class="muted">Database access uses PDO prepared statements and organized model classes.</p>
        </article>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
