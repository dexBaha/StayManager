<?php
require_once __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();

$roomModel = new Room($db);
$reservationModel = new Reservation($db);
$room = $roomModel->find((int) ($_GET['room_id'] ?? 0));

if (!$room) {
    redirect('/rooms.php');
}

if (isPost()) {
    $created = $reservationModel->create(Auth::user()['id'], (int) $room['id'], $_POST['check_in'], $_POST['check_out']);
    Session::flash('success', $created ? 'Reservation created successfully.' : 'Check-out date must be after check-in date.');
    redirect('/dashboard.php');
}

$pageTitle = 'Reserve room';
require_once __DIR__ . '/includes/header.php';
?>
<main class="container">
    <section class="card">
        <h1>Reserve room <?= e($room['room_number']) ?></h1>
        <p class="muted"><?= e($room['hotel_name']) ?> - <?= e($room['city']) ?></p>
        <p class="price">$<?= number_format((float) $room['price'], 2) ?> / night</p>
        <form class="form" method="post">
            <div class="form-row">
                <label>Check-in <input type="date" name="check_in" required></label>
                <label>Check-out <input type="date" name="check_out" required></label>
            </div>
            <button class="btn" type="submit">Confirm reservation</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
