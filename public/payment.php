<?php
require_once __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();

$reservationModel = new Reservation($db);
$paymentModel = new Payment($db);
$reservation = $reservationModel->findDetailed((int) ($_GET['reservation_id'] ?? 0));
$user = Auth::user();

if (!$reservation || (int) $reservation['user_id'] !== (int) $user['id']) {
    redirect('/dashboard.php');
}

if ($reservation['status'] !== 'confirmed') {
    Session::flash('success', 'Payment is available after admin confirmation.');
    redirect('/dashboard.php');
}

if ($paymentModel->findByReservation((int) $reservation['id'])) {
    Session::flash('success', 'This reservation is already paid.');
    redirect('/dashboard.php');
}

if (isPost()) {
    $cardNumber = preg_replace('/\D+/', '', $_POST['card_number'] ?? '');
    $cardLast4 = substr($cardNumber, -4);

    if (strlen($cardNumber) < 12 || strlen($cardLast4) !== 4) {
        Session::flash('error', 'Invalid card number.');
    } else {
        $paymentModel->create(
            (int) $reservation['id'],
            (float) $reservation['total_price'],
            $_POST['method'],
            trim($_POST['card_name']),
            $cardLast4
        );
        $reservation = $reservationModel->findDetailed((int) $reservation['id']);
        (new InvoiceService())->generate($reservation);
        Session::flash('success', 'Payment completed successfully. Your invoice is ready.');
        redirect('/dashboard.php');
    }
}

$pageTitle = 'Payment';
require_once __DIR__ . '/includes/header.php';
?>
<main class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
    <section class="animate-page-rise grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <aside class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-soft">
            <p class="text-sm font-black uppercase tracking-widest text-brand-100">Secure payment</p>
            <h1 class="mt-3 text-4xl font-black">Pay reservation</h1>
            <div class="mt-8 rounded-3xl bg-white/10 p-5 ring-1 ring-white/10">
                <p class="text-sm text-slate-300">Hotel</p>
                <p class="mt-1 text-xl font-black"><?= e($reservation['hotel_name']) ?></p>
                <p class="mt-4 text-sm text-slate-300">Room</p>
                <p class="mt-1 font-bold"><?= e($reservation['room_number']) ?> - <?= e($reservation['type']) ?></p>
                <p class="mt-4 text-sm text-slate-300">Dates</p>
                <p class="mt-1 font-bold"><?= e($reservation['check_in']) ?> to <?= e($reservation['check_out']) ?></p>
                <p class="mt-6 text-sm text-slate-300">Total amount</p>
                <p class="mt-1 text-5xl font-black text-brand-100"><?= money($reservation['total_price']) ?></p>
            </div>
        </aside>

        <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-soft">
            <h2 class="text-3xl font-black tracking-tight">Card details</h2>
            <?php if ($message = Session::flash('error')): ?>
                <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700"><?= e($message) ?></div>
            <?php endif; ?>
            <form class="mt-6 grid gap-5" method="post">
                <label class="text-sm font-black text-slate-600">Payment method
                    <select class="rounded-2xl border border-slate-200 px-4 py-3" name="method" required>
                        <option value="Visa">Visa</option>
                        <option value="Mastercard">Mastercard</option>
                        <option value="Hotel card">Hotel card</option>
                    </select>
                </label>
                <label class="text-sm font-black text-slate-600">Name on card
                    <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="card_name" autocomplete="cc-name" required>
                </label>
                <label class="text-sm font-black text-slate-600">Card number
                    <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="card_number" inputmode="numeric" autocomplete="cc-number" maxlength="19" placeholder="4242 4242 4242 4242" required>
                </label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-black text-slate-600">Expiry
                        <input class="rounded-2xl border border-slate-200 px-4 py-3" type="text" name="expiry" autocomplete="cc-exp" placeholder="12/28" required>
                    </label>
                    <label class="text-sm font-black text-slate-600">CVV
                        <input class="rounded-2xl border border-slate-200 px-4 py-3" type="password" name="cvv" inputmode="numeric" autocomplete="cc-csc" maxlength="4" required>
                    </label>
                </div>
                <button class="rounded-2xl bg-brand-600 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-900" type="submit">Pay now</button>
                <a class="text-center text-sm font-black text-slate-500 transition hover:text-brand-600" href="<?= e(url('/dashboard.php')) ?>">Back to dashboard</a>
            </form>
        </section>
    </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
