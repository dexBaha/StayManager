<?php
require_once __DIR__ . '/../app/bootstrap.php';

$roomModel = new Room($db);
$rooms = $roomModel->available();

$pageTitle = 'Rooms';
require_once __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-widest text-brand-600">Hotel catalog</p>
            <h1 class="mt-2 text-4xl font-black tracking-tight">Available rooms</h1>
        </div>
        <p class="max-w-md text-slate-600">Browse available rooms and create a reservation from your user account.</p>
    </div>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($rooms as $room): ?>
            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-soft">
                <div class="h-36 bg-gradient-to-br from-brand-600 to-slate-900"></div>
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black"><?= e($room['hotel_name']) ?></h2>
                            <p class="mt-1 text-sm font-bold text-slate-500"><?= e($room['city']) ?> - Room <?= e($room['room_number']) ?></p>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">Available</span>
                    </div>
                    <p class="mt-5 text-sm text-slate-600">Type: <strong class="text-slate-900"><?= e($room['type']) ?></strong></p>
                    <p class="mt-3 text-3xl font-black text-brand-600">$<?= number_format((float) $room['price'], 2) ?> <span class="text-sm font-bold text-slate-500">/ night</span></p>
                    <a class="mt-6 inline-flex w-full justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-600" href="<?= e(url('/reserve.php?room_id=' . (int) $room['id'])) ?>">Reserve</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
