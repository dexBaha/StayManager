<?php
require_once __DIR__ . '/../app/bootstrap.php';

$roomModel = new Room($db);
$rooms = $roomModel->available();
$countries = [];

function hotelGallery(string $mainPhoto): array
{
    return [
        $mainPhoto,
        'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=1200&q=80',
    ];
}

function roomTypePhoto(string $type): string
{
    $type = strtolower($type);

    if (str_contains($type, 'suite') || str_contains($type, 'riad')) {
        return 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=900&q=80';
    }

    if (str_contains($type, 'family')) {
        return 'https://images.unsplash.com/photo-1584132915807-fd1f5fbc078f?auto=format&fit=crop&w=900&q=80';
    }

    if (str_contains($type, 'double') || str_contains($type, 'deluxe')) {
        return 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=900&q=80';
    }

    if (str_contains($type, 'executive')) {
        return 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=900&q=80';
    }

    return 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80';
}

foreach ($rooms as $room) {
    $hotelId = (int) $room['hotel_id'];
    $country = $room['country'] ?: 'Other';

    if (!isset($countries[$country][$hotelId])) {
        $countries[$country][$hotelId] = [
            'id' => $hotelId,
            'name' => $room['hotel_name'],
            'city' => $room['city'],
            'country' => $country,
            'stars' => (int) ($room['stars'] ?? 4),
            'photo_url' => $room['photo_url'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
            'description' => $room['hotel_description'] ?? '',
            'rooms' => [],
        ];
    }

    $countries[$country][$hotelId]['rooms'][] = $room;
}

$pageTitle = 'Rooms';
require_once __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <section class="animate-page-rise mb-10 overflow-hidden rounded-[2rem] bg-slate-950 text-white shadow-soft">
        <div class="grid gap-8 p-8 lg:grid-cols-[1.2fr_0.8fr] lg:p-10">
            <div>
                <p class="text-sm font-black uppercase tracking-widest text-brand-100">Hotel catalog</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight sm:text-6xl">Choose a hotel, then pick your room.</h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-300">Click a hotel to preview photos and location, then open room types with smooth animations.</p>
            </div>
            <div class="rounded-3xl bg-white/10 p-6 ring-1 ring-white/10">
                <p class="text-sm font-bold text-slate-300">Available destinations</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <?php foreach (array_keys($countries) as $countryName): ?>
                        <a class="rounded-full bg-white px-4 py-2 text-sm font-black text-slate-950 transition hover:bg-brand-100" href="#country-<?= e(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $countryName))) ?>"><?= e($countryName) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php foreach ($countries as $countryName => $hotels): ?>
        <section id="country-<?= e(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $countryName))) ?>" class="mb-12 animate-page-rise">
            <div class="mb-5 flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-black uppercase tracking-widest text-brand-600">Country</p>
                    <h2 class="text-3xl font-black tracking-tight"><?= e($countryName) ?></h2>
                </div>
                <p class="text-sm font-bold text-slate-500"><?= count($hotels) ?> hotel<?= count($hotels) > 1 ? 's' : '' ?></p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <?php foreach ($hotels as $hotel): ?>
                    <?php $gallery = hotelGallery($hotel['photo_url']); ?>
                    <article class="hotel-card group overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-soft transition duration-300 hover:-translate-y-1 hover:shadow-2xl" data-hotel-card>
                        <button class="block w-full text-left" type="button" data-hotel-modal-open="hotel-modal-<?= (int) $hotel['id'] ?>">
                            <div class="relative h-64 overflow-hidden">
                                <div class="grid h-full grid-cols-3 gap-1 bg-slate-900">
                                    <img class="col-span-2 h-full w-full object-cover transition duration-700 group-hover:scale-105" src="<?= e($gallery[0]) ?>" alt="<?= e($hotel['name']) ?>">
                                    <div class="grid gap-1">
                                        <img class="h-full w-full object-cover transition duration-700 group-hover:scale-105" src="<?= e($gallery[1]) ?>" alt="<?= e($hotel['name']) ?> lobby">
                                        <img class="h-full w-full object-cover transition duration-700 group-hover:scale-105" src="<?= e($gallery[2]) ?>" alt="<?= e($hotel['name']) ?> view">
                                    </div>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                                    <div class="mb-3 flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-black backdrop-blur"><?= e($hotel['city']) ?>, <?= e($hotel['country']) ?></span>
                                        <span class="rounded-full bg-amber-400 px-3 py-1 text-xs font-black text-slate-950"><?= str_repeat('&#9733;', max(1, min(5, $hotel['stars']))) ?></span>
                                    </div>
                                    <h3 class="text-2xl font-black tracking-tight"><?= e($hotel['name']) ?></h3>
                                    <p class="mt-2 line-clamp-2 text-sm text-slate-200"><?= e($hotel['description']) ?></p>
                                </div>
                            </div>
                        </button>
                        <div class="flex items-center justify-between gap-4 p-6">
                            <div>
                                <p class="text-sm font-bold text-slate-500">From</p>
                                <p class="text-3xl font-black text-brand-600">$<?= number_format((float) min(array_column($hotel['rooms'], 'price')), 2) ?></p>
                            </div>
                            <button class="rounded-full bg-slate-900 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-600" type="button" data-hotel-toggle aria-expanded="false" data-toggle-label>Show rooms</button>
                        </div>

                        <div class="hotel-rooms max-h-0 overflow-hidden border-t border-slate-100 bg-slate-50 transition-all duration-500 ease-out" data-hotel-rooms>
                            <div class="grid gap-4 p-6 sm:grid-cols-2">
                                <?php foreach ($hotel['rooms'] as $index => $room): ?>
                                    <div class="room-reveal overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" style="--delay: <?= $index * 80 ?>ms">
                                        <img class="h-36 w-full object-cover" src="<?= e(roomTypePhoto($room['type'])) ?>" alt="<?= e($room['type']) ?> room">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-xs font-black uppercase tracking-widest text-brand-600"><?= e($room['type']) ?></p>
                                                    <h4 class="mt-1 text-lg font-black">Room <?= e($room['room_number']) ?></h4>
                                                </div>
                                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">Available</span>
                                            </div>
                                            <p class="mt-4 text-3xl font-black text-slate-950">$<?= number_format((float) $room['price'], 2) ?> <span class="text-sm font-bold text-slate-500">/ night</span></p>
                                            <a class="mt-5 inline-flex w-full justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-900" href="<?= e(url('/reserve.php?room_id=' . (int) $room['id'])) ?>">Reserve this room</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </article>

                    <div class="hotel-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/75 p-4 backdrop-blur-sm" id="hotel-modal-<?= (int) $hotel['id'] ?>" data-hotel-modal>
                        <div class="hotel-modal-panel max-h-[92vh] w-full max-w-5xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl">
                            <div class="grid gap-1 bg-slate-900 p-1 sm:grid-cols-3">
                                <img class="h-72 w-full rounded-tl-[1.7rem] object-cover sm:col-span-2" src="<?= e($gallery[0]) ?>" alt="<?= e($hotel['name']) ?>">
                                <div class="grid gap-1">
                                    <img class="h-36 w-full rounded-tr-[1.7rem] object-cover" src="<?= e($gallery[1]) ?>" alt="<?= e($hotel['name']) ?> interior">
                                    <img class="h-36 w-full object-cover" src="<?= e($gallery[2]) ?>" alt="<?= e($hotel['name']) ?> exterior">
                                </div>
                            </div>
                            <div class="grid gap-6 p-6 lg:grid-cols-[1fr_320px]">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-black text-brand-600"><?= e($hotel['city']) ?>, <?= e($hotel['country']) ?></span>
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700"><?= str_repeat('&#9733;', max(1, min(5, $hotel['stars']))) ?></span>
                                    </div>
                                    <h3 class="mt-3 text-3xl font-black tracking-tight"><?= e($hotel['name']) ?></h3>
                                    <p class="mt-4 leading-7 text-slate-600"><?= e($hotel['description']) ?></p>
                                    <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                        <p class="text-sm font-black uppercase tracking-widest text-slate-400">Location</p>
                                        <p class="mt-2 text-lg font-black text-slate-950"><?= e($hotel['city']) ?>, <?= e($hotel['country']) ?></p>
                                        <a class="mt-4 inline-flex rounded-full bg-slate-900 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-600" href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($hotel['name'] . ' ' . $hotel['city'] . ' ' . $hotel['country']) ?>" target="_blank" rel="noreferrer">Open location</a>
                                    </div>
                                </div>
                                <div class="rounded-3xl bg-slate-950 p-6 text-white">
                                    <p class="text-sm font-bold text-slate-300">Available rooms</p>
                                    <p class="mt-2 text-4xl font-black"><?= count($hotel['rooms']) ?></p>
                                    <p class="mt-4 text-sm text-slate-300">Starting from</p>
                                    <p class="mt-1 text-3xl font-black text-brand-100">$<?= number_format((float) min(array_column($hotel['rooms'], 'price')), 2) ?></p>
                                    <button class="mt-6 w-full rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-950 transition hover:bg-brand-100" type="button" data-hotel-modal-close>Close preview</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</main>
<script src="<?= e(url('/assets/js/rooms.js')) ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
