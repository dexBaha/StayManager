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
<main class="container">
    <h1>Welcome, <?= e($user['name']) ?></h1>
    <?php if ($message = Session::flash('success')): ?><div class="flash"><?= e($message) ?></div><?php endif; ?>
    <section class="card">
        <h2>My reservations</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Hotel</th>
                        <th>Room</th>
                        <th>Dates</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?= e($reservation['hotel_name']) ?></td>
                            <td><?= e($reservation['room_number']) ?> (<?= e($reservation['type']) ?>)</td>
                            <td>
                                <form class="form" method="post">
                                    <input type="hidden" name="action" value="update_dates">
                                    <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                                    <input type="date" name="check_in" value="<?= e($reservation['check_in']) ?>" required>
                                    <input type="date" name="check_out" value="<?= e($reservation['check_out']) ?>" required>
                                    <button class="btn secondary" type="submit">Update</button>
                                </form>
                            </td>
                            <td>$<?= number_format((float) $reservation['total_price'], 2) ?></td>
                            <td><span class="status <?= e($reservation['status']) ?>"><?= e($reservation['status']) ?></span></td>
                            <td>
                                <form class="inline-form" method="post">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                                    <button class="btn danger" type="submit">Cancel</button>
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
