<?php
require_once __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();

$roomModel = new Room($db);
$reservationModel = new Reservation($db);
$room = $roomModel->find((int) ($_GET['room_id'] ?? 0));

if (!$room) {
    redirect('/rooms.php');
}

function reserveRoomGallery(string $type): array
{
    $type = strtolower($type);

    if (str_contains($type, 'suite') || str_contains($type, 'riad') || str_contains($type, 'executive')) {
        return [
            'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
        ];
    }

    if (str_contains($type, 'family')) {
        return [
            'https://images.unsplash.com/photo-1584132915807-fd1f5fbc078f?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1560448075-bb485b067938?auto=format&fit=crop&w=1200&q=80',
        ];
    }

    if (str_contains($type, 'double') || str_contains($type, 'deluxe')) {
        return [
            'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=80',
        ];
    }

    return [
        'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1560448204-603b3fc33ddc?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1618220179428-22790b461013?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1590490359683-658d3d23f972?auto=format&fit=crop&w=1200&q=80',
    ];
}

if (isPost()) {
    $created = $reservationModel->create(Auth::user()['id'], (int) $room['id'], $_POST['check_in'], $_POST['check_out']);
    Session::flash('success', $created ? 'Reservation created successfully.' : 'This room is not available or the dates are invalid.');
    redirect('/dashboard.php');
}

$gallery = reserveRoomGallery($room['type']);
$amenities = array_filter(array_map('trim', explode(',', $room['amenities'] ?? '')));

$pageTitle = 'Reserve room';
require_once __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <section class="animate-page-rise grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-soft">
            <img id="mainRoomPhoto" class="h-[420px] w-full object-cover" src="<?= e($gallery[0]) ?>" alt="<?= e($room['type']) ?> room">
            <div class="grid grid-cols-4 gap-3 p-4">
                <?php foreach ($gallery as $index => $photo): ?>
                    <button class="room-photo-thumb overflow-hidden rounded-2xl ring-2 <?= $index === 0 ? 'ring-brand-600' : 'ring-transparent' ?>" type="button" data-room-photo="<?= e($photo) ?>">
                        <img class="h-24 w-full object-cover transition duration-300 hover:scale-105" src="<?= e($photo) ?>" alt="<?= e($room['type']) ?> photo <?= $index + 1 ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <aside class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
            <p class="text-sm font-black uppercase tracking-widest text-brand-600"><?= e($room['hotel_name']) ?></p>
            <h1 class="mt-2 text-4xl font-black tracking-tight">Room <?= e($room['room_number']) ?></h1>
            <p class="mt-2 text-lg font-bold text-slate-500"><?= e($room['type']) ?> - <?= e($room['city']) ?>, <?= e($room['country']) ?></p>

            <p class="mt-5 rounded-3xl bg-slate-50 p-5 leading-7 text-slate-600">
                <?= e($room['description'] ?: 'Comfortable room prepared for a relaxing hotel stay.') ?>
            </p>

            <?php if ($amenities): ?>
                <div class="mt-5 flex flex-wrap gap-2">
                    <?php foreach ($amenities as $amenity): ?>
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-black text-brand-600"><?= e($amenity) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <p class="mt-6 text-4xl font-black text-brand-600">$<?= number_format((float) $room['price'], 2) ?> <span class="text-sm font-bold text-slate-500">/ night</span></p>

            <form class="mt-8 grid gap-5 rounded-3xl border border-slate-200 p-5" method="post">
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-black text-slate-600">Check-in
                        <input class="rounded-2xl border border-slate-200 px-4 py-3" type="date" name="check_in" required>
                    </label>
                    <label class="text-sm font-black text-slate-600">Check-out
                        <input class="rounded-2xl border border-slate-200 px-4 py-3" type="date" name="check_out" required>
                    </label>
                </div>
                <button class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-900" type="submit">Confirm reservation</button>
                <a class="text-center text-sm font-black text-slate-500 transition hover:text-brand-600" href="<?= e(url('/rooms.php')) ?>">Back to rooms</a>
            </form>
        </aside>
    </section>
</main>
<script>
document.querySelectorAll('[data-room-photo]').forEach((button) => {
    button.addEventListener('click', () => {
        document.getElementById('mainRoomPhoto').src = button.dataset.roomPhoto;
        document.querySelectorAll('.room-photo-thumb').forEach((thumb) => {
            thumb.classList.remove('ring-brand-600');
            thumb.classList.add('ring-transparent');
        });
        button.classList.add('ring-brand-600');
        button.classList.remove('ring-transparent');
    });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
