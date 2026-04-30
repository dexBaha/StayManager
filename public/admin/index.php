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
<main class="mx-auto grid max-w-7xl gap-6 px-4 py-12 sm:px-6 lg:grid-cols-[260px_1fr] lg:px-8">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <section>
        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-black uppercase tracking-widest text-brand-600">Administration</p>
                <h1 class="mt-2 text-4xl font-black tracking-tight">Admin dashboard</h1>
            </div>
            <p class="max-w-md text-slate-600">Track users, hotels, rooms, reservations, and confirmed revenue from one interface.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft"><p class="text-sm font-black uppercase text-slate-400">Users</p><div class="mt-3 text-4xl font-black"><?= $counts['users'] ?></div></article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft"><p class="text-sm font-black uppercase text-slate-400">Hotels</p><div class="mt-3 text-4xl font-black"><?= $counts['hotels'] ?></div></article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft"><p class="text-sm font-black uppercase text-slate-400">Rooms</p><div class="mt-3 text-4xl font-black"><?= $counts['rooms'] ?></div></article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft"><p class="text-sm font-black uppercase text-slate-400">Reservations</p><div class="mt-3 text-4xl font-black"><?= $counts['reservations'] ?></div></article>
        </div>
        <div class="mt-6 grid gap-6 xl:grid-cols-[320px_1fr]">
            <article class="rounded-3xl border border-slate-200 bg-slate-900 p-6 text-white shadow-soft">
                <p class="text-sm font-black uppercase tracking-widest text-brand-100">Revenue</p>
                <p class="mt-4 text-4xl font-black">$<?= number_format($revenue, 2) ?></p>
                <p class="mt-2 text-sm text-slate-300">Confirmed reservations only.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
                <canvas class="w-full" id="reservationChart" width="680" height="300"></canvas>
            </article>
        </div>
    </section>
</main>
<script src="<?= e(url('/assets/js/charts.js')) ?>"></script>
<script>
drawReservationChart('reservationChart', <?= json_encode($labels) ?>, <?= json_encode($values) ?>);
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
