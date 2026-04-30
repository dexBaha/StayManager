<?php
require_once __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();

$reservationModel = new Reservation($db);
$ticketModel = new SupportTicket($db);
$user = Auth::user();

if (isPost()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $reservationModel->delete((int) $_POST['id'], $user['id']);
        Session::flash('success', 'Reservation cancelled.');
    }

    if ($action === 'update_dates') {
        $updated = $reservationModel->updateDates((int) $_POST['id'], $user['id'], $_POST['check_in'], $_POST['check_out']);
        Session::flash('success', $updated ? 'Reservation updated.' : 'Check-out date must be after check-in date.');
    }

    if ($action === 'support_question') {
        $created = $ticketModel->create($user['id'], trim($_POST['subject']), trim($_POST['message']));
        Session::flash('success', $created ? 'Your question was sent to customer service.' : 'Could not send your question.');
    }

    redirect('/dashboard.php');
}

function dashboardRoomPhoto(string $type): string
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

    return 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80';
}

$reservations = $reservationModel->forUser($user['id']);
$tickets = $ticketModel->forUser($user['id']);
$totalSpent = array_sum(array_map(fn ($reservation) => (float) $reservation['total_price'], $reservations));
$pendingCount = count(array_filter($reservations, fn ($reservation) => $reservation['status'] === 'pending'));
$confirmedCount = count(array_filter($reservations, fn ($reservation) => $reservation['status'] === 'confirmed'));
$nextReservation = $reservations[0] ?? null;

$pageTitle = 'My account';
require_once __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <section class="animate-page-rise overflow-hidden rounded-[2rem] bg-slate-950 text-white shadow-soft">
        <div class="grid gap-8 p-8 lg:grid-cols-[1fr_360px] lg:p-10">
            <div>
                <p class="text-sm font-black uppercase tracking-widest text-brand-100">My stay dashboard</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight sm:text-6xl">Welcome, <?= e($user['name']) ?></h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-300">Track your hotel stays, update dates, and manage every reservation from one animated travel board.</p>
                <a class="mt-8 inline-flex rounded-full bg-white px-6 py-3 text-sm font-black text-slate-950 transition hover:bg-brand-100" href="<?= e(url('/rooms.php')) ?>">Explore more hotels</a>
            </div>
            <div class="animate-soft-float rounded-3xl bg-white/10 p-6 ring-1 ring-white/10">
                <p class="text-sm font-bold text-slate-300">Reservations</p>
                <p class="mt-2 text-6xl font-black"><?= count($reservations) ?></p>
                <p class="mt-4 text-sm text-slate-300">Total value</p>
                <p class="mt-1 text-3xl font-black text-brand-100">$<?= number_format($totalSpent, 2) ?></p>
            </div>
        </div>
    </section>

    <?php if ($message = Session::flash('success')): ?>
        <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700"><?= e($message) ?></div>
    <?php endif; ?>

    <section class="mt-8 grid gap-4 md:grid-cols-3">
        <article class="animate-card-in rounded-3xl border border-slate-200 bg-white p-6 shadow-soft" style="animation-delay: 40ms">
            <p class="text-sm font-black uppercase tracking-widest text-slate-400">Confirmed</p>
            <p class="mt-3 text-4xl font-black text-emerald-600"><?= $confirmedCount ?></p>
        </article>
        <article class="animate-card-in rounded-3xl border border-slate-200 bg-white p-6 shadow-soft" style="animation-delay: 120ms">
            <p class="text-sm font-black uppercase tracking-widest text-slate-400">Pending</p>
            <p class="mt-3 text-4xl font-black text-amber-600"><?= $pendingCount ?></p>
        </article>
        <article class="animate-card-in rounded-3xl border border-slate-200 bg-white p-6 shadow-soft" style="animation-delay: 200ms">
            <p class="text-sm font-black uppercase tracking-widest text-slate-400">Next stay</p>
            <p class="mt-3 text-xl font-black text-slate-950"><?= $nextReservation ? e($nextReservation['check_in']) : 'No booking yet' ?></p>
        </article>
    </section>

    <section class="mt-10">
        <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-black uppercase tracking-widest text-brand-600">Reservations</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight">Your booking cards</h2>
            </div>
            <p class="max-w-md text-sm font-bold text-slate-500">Edit dates directly from each card or cancel a stay you no longer need.</p>
        </div>

        <?php if (!$reservations): ?>
            <div class="animate-card-in rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center shadow-soft">
                <h3 class="text-2xl font-black">No reservations yet</h3>
                <p class="mt-3 text-slate-600">Choose a hotel and create your first booking.</p>
                <a class="mt-6 inline-flex rounded-full bg-brand-600 px-6 py-3 text-sm font-black text-white transition hover:bg-brand-900" href="<?= e(url('/rooms.php')) ?>">Browse rooms</a>
            </div>
        <?php endif; ?>

        <div class="grid gap-6 lg:grid-cols-2">
            <?php foreach ($reservations as $index => $reservation): ?>
                <article class="animate-card-in overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-soft transition duration-300 hover:-translate-y-1 hover:shadow-2xl" style="animation-delay: <?= $index * 80 ?>ms">
                    <img class="h-52 w-full object-cover" src="<?= e(dashboardRoomPhoto($reservation['type'])) ?>" alt="<?= e($reservation['type']) ?> room">
                    <div class="p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-black uppercase tracking-widest text-brand-600"><?= e($reservation['type']) ?></p>
                                <h3 class="mt-1 text-2xl font-black"><?= e($reservation['hotel_name']) ?></h3>
                                <p class="mt-1 text-sm font-bold text-slate-500">Room <?= e($reservation['room_number']) ?></p>
                            </div>
                            <span class="status <?= e($reservation['status']) ?>"><?= e($reservation['status']) ?></span>
                        </div>

                        <div class="mt-6 grid gap-3 rounded-3xl bg-slate-50 p-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-slate-400">Check-in</p>
                                <p class="mt-1 font-black"><?= e($reservation['check_in']) ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-slate-400">Check-out</p>
                                <p class="mt-1 font-black"><?= e($reservation['check_out']) ?></p>
                            </div>
                        </div>

                        <p class="mt-5 text-3xl font-black text-brand-600">$<?= number_format((float) $reservation['total_price'], 2) ?></p>

                        <form class="mt-6 grid gap-3 rounded-3xl border border-slate-200 p-4" method="post">
                            <input type="hidden" name="action" value="update_dates">
                            <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="text-sm font-black text-slate-600">New check-in <input class="rounded-2xl border border-slate-200 px-3 py-2 text-sm" type="date" name="check_in" value="<?= e($reservation['check_in']) ?>" required></label>
                                <label class="text-sm font-black text-slate-600">New check-out <input class="rounded-2xl border border-slate-200 px-3 py-2 text-sm" type="date" name="check_out" value="<?= e($reservation['check_out']) ?>" required></label>
                            </div>
                            <button class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-600" type="submit">Update dates</button>
                        </form>

                        <form class="mt-3" method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                            <button class="w-full rounded-2xl bg-red-50 px-5 py-3 text-sm font-black text-red-700 transition hover:bg-red-600 hover:text-white" type="submit">Cancel reservation</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mt-12 grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
        <article class="animate-card-in rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
            <p class="text-sm font-black uppercase tracking-widest text-brand-600">Customer service</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight">Ask a question</h2>
            <p class="mt-3 text-slate-600">Send a message to the hotel team. The admin response will appear here in your dashboard.</p>
            <form class="mt-6 grid gap-4" method="post">
                <input type="hidden" name="action" value="support_question">
                <label class="text-sm font-black text-slate-600">Subject
                    <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="subject" placeholder="Example: Airport transfer" required>
                </label>
                <label class="text-sm font-black text-slate-600">Question
                    <textarea class="min-h-32 rounded-2xl border border-slate-200 px-4 py-3" name="message" placeholder="Write your question here..." required></textarea>
                </label>
                <button class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-900" type="submit">Send question</button>
            </form>
        </article>

        <article class="animate-card-in rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft" style="animation-delay: 120ms">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-black uppercase tracking-widest text-brand-600">Messages</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight">Admin replies</h2>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600"><?= count($tickets) ?> question<?= count($tickets) === 1 ? '' : 's' ?></span>
            </div>

            <div class="mt-6 grid gap-4">
                <?php if (!$tickets): ?>
                    <div class="rounded-3xl border border-dashed border-slate-300 p-8 text-center">
                        <p class="font-black text-slate-900">No questions yet</p>
                        <p class="mt-2 text-sm text-slate-500">Ask customer service anything about your stay.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($tickets as $ticket): ?>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h3 class="font-black text-slate-950"><?= e($ticket['subject']) ?></h3>
                            <span class="rounded-full <?= $ticket['status'] === 'answered' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?> px-3 py-1 text-xs font-black"><?= e($ticket['status']) ?></span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($ticket['message']) ?></p>
                        <?php if (!empty($ticket['admin_reply'])): ?>
                            <div class="mt-4 rounded-2xl bg-white p-4 shadow-sm">
                                <p class="text-xs font-black uppercase tracking-widest text-brand-600">Admin response</p>
                                <p class="mt-2 text-sm leading-6 text-slate-700"><?= e($ticket['admin_reply']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
