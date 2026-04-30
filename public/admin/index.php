<?php
require_once __DIR__ . '/../../app/bootstrap.php';
Auth::requireAdmin();

$dashboard = new Dashboard($db);
$counts = $dashboard->counts();
$statuses = $dashboard->reservationsByStatus();
$revenue = $dashboard->revenue();

$labels = array_column($statuses, 'status');
$values = array_map('intval', array_column($statuses, 'total'));

$pageTitle = 'Admin dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<main class="container admin-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <section>
        <h1>Admin dashboard</h1>
        <div class="grid stats">
            <article class="card"><p class="muted">Users</p><div class="stat-number"><?= $counts['users'] ?></div></article>
            <article class="card"><p class="muted">Hotels</p><div class="stat-number"><?= $counts['hotels'] ?></div></article>
            <article class="card"><p class="muted">Rooms</p><div class="stat-number"><?= $counts['rooms'] ?></div></article>
            <article class="card"><p class="muted">Reservations</p><div class="stat-number"><?= $counts['reservations'] ?></div></article>
        </div>
        <div class="grid rooms" style="margin-top: 18px;">
            <article class="card">
                <h2>Revenue</h2>
                <p class="price">$<?= number_format($revenue, 2) ?></p>
                <p class="muted">Confirmed reservations only.</p>
            </article>
            <article class="card">
                <canvas id="reservationChart" width="520" height="260"></canvas>
            </article>
        </div>
    </section>
</main>
<script src="<?= e(url('/assets/js/charts.js')) ?>"></script>
<script>
drawReservationChart('reservationChart', <?= json_encode($labels) ?>, <?= json_encode($values) ?>);
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
