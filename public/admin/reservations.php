<?php
require_once __DIR__ . '/../../app/bootstrap.php';
Auth::requireAdmin();

$reservationModel = new Reservation($db);

if (isPost()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'status') {
        $reservationModel->updateStatus((int) $_POST['id'], $_POST['status']);
        Session::flash('success', 'Reservation status updated.');
    }

    if ($action === 'delete') {
        $reservationModel->delete((int) $_POST['id']);
        Session::flash('success', 'Reservation deleted.');
    }

    redirect('/admin/reservations.php');
}

$reservations = $reservationModel->all();
$pageTitle = 'Manage reservations';
require_once __DIR__ . '/../includes/header.php';
?>
<main class="container admin-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <section>
        <h1>Reservations</h1>
        <?php if ($message = Session::flash('success')): ?><div class="flash"><?= e($message) ?></div><?php endif; ?>
        <section class="card">
            <div class="table-wrap">
                <table>
                    <thead><tr><th>User</th><th>Hotel</th><th>Room</th><th>Dates</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= e($reservation['user_name']) ?></td>
                                <td><?= e($reservation['hotel_name']) ?></td>
                                <td><?= e($reservation['room_number']) ?></td>
                                <td><?= e($reservation['check_in']) ?> to <?= e($reservation['check_out']) ?></td>
                                <td>$<?= number_format((float) $reservation['total_price'], 2) ?></td>
                                <td>
                                    <form class="form" method="post">
                                        <input type="hidden" name="action" value="status">
                                        <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                                        <select name="status">
                                            <?php foreach (['pending', 'confirmed', 'cancelled'] as $status): ?>
                                                <option value="<?= $status ?>" <?= $reservation['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn secondary" type="submit">Save</button>
                                    </form>
                                </td>
                                <td>
                                    <form class="inline-form" method="post">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                                        <button class="btn danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

