<?php
require_once __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();

$reservationModel = new Reservation($db);
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

    redirect('/dashboard.php');
}

$reservations = $reservationModel->forUser($user['id']);
$pageTitle = 'My account';
require_once __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="animate-page-rise mb-8">
        <p class="text-sm font-black uppercase tracking-widest text-brand-600">User dashboard</p>
        <h1 class="mt-2 text-4xl font-black tracking-tight">Welcome, <?= e($user['name']) ?></h1>
    </div>
    <?php if ($message = Session::flash('success')): ?><div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700"><?= e($message) ?></div><?php endif; ?>
    <section class="animate-card-in rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
        <h2 class="text-2xl font-black">My reservations</h2>
        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">Hotel</th>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">Room</th>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">Dates</th>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td class="px-4 py-4 font-bold"><?= e($reservation['hotel_name']) ?></td>
                            <td class="px-4 py-4 text-slate-600"><?= e($reservation['room_number']) ?> (<?= e($reservation['type']) ?>)</td>
                            <td class="px-4 py-4">
                                <form class="grid gap-2" method="post">
                                    <input type="hidden" name="action" value="update_dates">
                                    <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                                    <input class="rounded-xl border border-slate-200 px-3 py-2 text-sm" type="date" name="check_in" value="<?= e($reservation['check_in']) ?>" required>
                                    <input class="rounded-xl border border-slate-200 px-3 py-2 text-sm" type="date" name="check_out" value="<?= e($reservation['check_out']) ?>" required>
                                    <button class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-black text-slate-700 transition hover:bg-slate-200" type="submit">Update</button>
                                </form>
                            </td>
                            <td class="px-4 py-4 font-black text-brand-600">$<?= number_format((float) $reservation['total_price'], 2) ?></td>
                            <td class="px-4 py-4"><span class="status <?= e($reservation['status']) ?>"><?= e($reservation['status']) ?></span></td>
                            <td class="px-4 py-4">
                                <form method="post">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                                    <button class="rounded-xl bg-red-600 px-3 py-2 text-sm font-black text-white transition hover:bg-red-700" type="submit">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
