<?php
require_once __DIR__ . '/../app/bootstrap.php';

$roomModel = new Room($db);
$rooms = $roomModel->available();

$pageTitle = 'Rooms';
require_once __DIR__ . '/includes/header.php';
?>
<main class="container">
    <h1>Available rooms</h1>
    <div class="grid rooms">
        <?php foreach ($rooms as $room): ?>
            <article class="card">
                <h2><?= e($room['hotel_name']) ?></h2>
                <p class="muted"><?= e($room['city']) ?> - Room <?= e($room['room_number']) ?></p>
                <p>Type: <strong><?= e($room['type']) ?></strong></p>
                <p class="price">$<?= number_format((float) $room['price'], 2) ?> / night</p>
                <a class="btn" href="/reserve.php?room_id=<?= (int) $room['id'] ?>">Reserve</a>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

