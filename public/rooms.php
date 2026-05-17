<?php
require_once __DIR__ . '/../app/bootstrap.php';

$roomModel = new Room($db);
$rooms = $roomModel->available();
$countries = [];

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
                                <p class="text-3xl font-black text-brand-600"><?= money(min(array_column($hotel['rooms'], 'price'))) ?></p>
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
                                                <?php $availability = roomAvailability($room); ?>
                                                <span class="rounded-full <?= $availability['status'] === 'available' ? 'bg-emerald-50 text-emerald-700' : ($availability['status'] === 'reserved' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') ?> px-3 py-1 text-xs font-black"><?= e($availability['label']) ?></span>
                                            </div>
                                            <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($room['description'] ?: 'Comfortable room prepared for a relaxing hotel stay.') ?></p>
                                            <?php $amenities = array_filter(array_map('trim', explode(',', $room['amenities'] ?? ''))); ?>
                                            <?php if ($amenities): ?>
                                                <div class="mt-4 flex flex-wrap gap-2">
                                                    <?php foreach ($amenities as $amenity): ?>
                                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600"><?= e($amenity) ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                            <p class="mt-4 text-3xl font-black text-slate-950"><?= money($room['price']) ?> <span class="text-sm font-bold text-slate-500">/ night</span></p>
                                            <p class="mt-3 rounded-2xl <?= $availability['can_book'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' ?> px-4 py-3 text-sm font-bold"><?= e($availability['message']) ?></p>
                                            <?php if ($availability['can_book']): ?>
                                                <a class="mt-5 inline-flex w-full justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-900" href="<?= e(url('/reserve.php?room_id=' . (int) $room['id'])) ?>">Reserve this room</a>
                                            <?php else: ?>
                                                <button class="mt-5 w-full cursor-not-allowed rounded-2xl bg-slate-200 px-5 py-3 text-sm font-black text-slate-500" type="button" disabled>Not available</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </article>

                    <div class="hotel-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-3 backdrop-blur-md sm:p-6" id="hotel-modal-<?= (int) $hotel['id'] ?>" data-hotel-modal>
                        <div class="hotel-modal-panel relative grid max-h-[92vh] w-full max-w-6xl overflow-hidden rounded-[2rem] bg-white shadow-2xl lg:grid-cols-[1.08fr_0.92fr]">
                            <button class="absolute right-4 top-4 z-10 grid h-11 w-11 place-items-center rounded-full bg-white/95 text-xl font-black text-slate-950 shadow-lg transition hover:bg-slate-950 hover:text-white" type="button" data-hotel-modal-close aria-label="Close hotel preview">&times;</button>

                            <section class="bg-slate-950 p-3">
                                <img class="h-72 w-full rounded-[1.5rem] object-cover sm:h-[430px]" src="<?= e($gallery[0]) ?>" alt="<?= e($hotel['name']) ?>">
                                <div class="mt-3 grid grid-cols-2 gap-3">
                                    <img class="h-28 w-full rounded-2xl object-cover sm:h-36" src="<?= e($gallery[1]) ?>" alt="<?= e($hotel['name']) ?> interior">
                                    <img class="h-28 w-full rounded-2xl object-cover sm:h-36" src="<?= e($gallery[2]) ?>" alt="<?= e($hotel['name']) ?> exterior">
                                </div>
                            </section>

                            <section class="overflow-y-auto p-6 sm:p-8">
                                <div class="flex h-full flex-col">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-black text-brand-600"><?= e($hotel['city']) ?>, <?= e($hotel['country']) ?></span>
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700"><?= str_repeat('&#9733;', max(1, min(5, $hotel['stars']))) ?></span>
                                    </div>
                                    <h3 class="mt-4 text-4xl font-black tracking-tight text-slate-950"><?= e($hotel['name']) ?></h3>
                                    <p class="mt-4 leading-7 text-slate-600"><?= e($hotel['description']) ?></p>

                                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                            <p class="text-xs font-black uppercase tracking-widest text-slate-400">Location</p>
                                            <p class="mt-2 text-lg font-black text-slate-950"><?= e($hotel['city']) ?></p>
                                            <p class="text-sm font-bold text-slate-500"><?= e($hotel['country']) ?></p>
                                        </div>
                                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                            <p class="text-xs font-black uppercase tracking-widest text-slate-400">Available rooms</p>
                                            <p class="mt-2 text-4xl font-black text-slate-950"><?= count($hotel['rooms']) ?></p>
                                        </div>
                                    </div>

                                    <div class="mt-6 rounded-3xl bg-slate-950 p-6 text-white">
                                        <p class="text-sm font-bold text-slate-300">Starting from</p>
                                        <p class="mt-1 text-4xl font-black text-brand-100"><?= money(min(array_column($hotel['rooms'], 'price'))) ?></p>
                                        <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                                            <a class="inline-flex flex-1 justify-center rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-950 transition hover:bg-brand-100" href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($hotel['name'] . ' ' . $hotel['city'] . ' ' . $hotel['country']) ?>" target="_blank" rel="noreferrer">Open location</a>
                                            <button class="inline-flex flex-1 justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-500" type="button" data-hotel-modal-close>See rooms below</button>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</main>
<script src="<?= e(url('/assets/js/rooms.js')) ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
