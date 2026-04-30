<aside class="sidebar">
    <a href="<?= e(url('/admin/index.php')) ?>" class="<?= basename($_SERVER['SCRIPT_NAME']) === 'index.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="<?= e(url('/admin/hotels.php')) ?>" class="<?= basename($_SERVER['SCRIPT_NAME']) === 'hotels.php' ? 'active' : '' ?>">Hotels</a>
    <a href="<?= e(url('/admin/rooms.php')) ?>" class="<?= basename($_SERVER['SCRIPT_NAME']) === 'rooms.php' ? 'active' : '' ?>">Rooms</a>
    <a href="<?= e(url('/admin/reservations.php')) ?>" class="<?= basename($_SERVER['SCRIPT_NAME']) === 'reservations.php' ? 'active' : '' ?>">Reservations</a>
    <a href="<?= e(url('/admin/users.php')) ?>" class="<?= basename($_SERVER['SCRIPT_NAME']) === 'users.php' ? 'active' : '' ?>">Users</a>
</aside>
