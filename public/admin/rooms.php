<?php
require_once __DIR__ . '/../../app/bootstrap.php';
Auth::requireAdmin();

$roomModel = new Room($db);
$hotelModel = new Hotel($db);
$editRoom = null;

if (isset($_GET['edit'])) {
    $editRoom = $roomModel->find((int) $_GET['edit']);
}

if (isPost()) {
    $action = $_POST['action'] ?? '';
    $hotelId = (int) ($_POST['hotel_id'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $unavailableUntil = $_POST['unavailable_until'] ?? null;
    $description = trim($_POST['description'] ?? '');
    $amenities = trim($_POST['amenities'] ?? '');

    if ($action === 'create') {
        $roomModel->create($hotelId, $_POST['room_number'], $_POST['type'], $price, $_POST['status'], $unavailableUntil, $description, $amenities);
        Session::flash('success', 'Room created.');
    }

    if ($action === 'update') {
        $roomModel->update((int) $_POST['id'], $hotelId, $_POST['room_number'], $_POST['type'], $price, $_POST['status'], $unavailableUntil, $description, $amenities);
        Session::flash('success', 'Room updated.');
    }

    if ($action === 'delete') {
        $roomModel->delete((int) $_POST['id']);
        Session::flash('success', 'Room deleted.');
    }

    redirect('/admin/rooms.php');
}

$rooms = $roomModel->all();
$hotels = $hotelModel->all();
$pageTitle = 'Manage rooms';
require_once __DIR__ . '/../includes/header.php';
?>
<main class="container admin-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <section>
        <h1>Rooms</h1>
        <?php if ($message = Session::flash('success')): ?><div class="flash"><?= e($message) ?></div><?php endif; ?>
        <section class="card">
            <h2><?= $editRoom ? 'Edit room' : 'Add room' ?></h2>
            <form class="form" method="post">
                <input type="hidden" name="action" value="<?= $editRoom ? 'update' : 'create' ?>">
                <?php if ($editRoom): ?><input type="hidden" name="id" value="<?= (int) $editRoom['id'] ?>"><?php endif; ?>
                <div class="form-row">
                    <label>Hotel
                        <select name="hotel_id" required>
                            <?php foreach ($hotels as $hotel): ?>
                                <option value="<?= (int) $hotel['id'] ?>" <?= (int) ($editRoom['hotel_id'] ?? 0) === (int) $hotel['id'] ? 'selected' : '' ?>>
                                    <?= e($hotel['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Room number <input type="text" name="room_number" value="<?= e($editRoom['room_number'] ?? '') ?>" required></label>
                </div>
                <div class="form-row">
                    <label>Type <input type="text" name="type" value="<?= e($editRoom['type'] ?? '') ?>" required></label>
                    <label>Price <input type="number" step="0.01" min="0" name="price" value="<?= e((string) ($editRoom['price'] ?? '')) ?>" required></label>
                    <label>Status
                        <select name="status">
                            <?php foreach (['available', 'reserved', 'maintenance'] as $status): ?>
                                <option value="<?= $status ?>" <?= ($editRoom['status'] ?? '') === $status ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Unavailable until <input type="date" name="unavailable_until" value="<?= e($editRoom['unavailable_until'] ?? '') ?>"></label>
                </div>
                <label>Description <textarea name="description" placeholder="Example: Calm room with city view and comfortable workspace."><?= e($editRoom['description'] ?? '') ?></textarea></label>
                <label>Amenities <input type="text" name="amenities" value="<?= e($editRoom['amenities'] ?? '') ?>" placeholder="WiFi, breakfast included, AC, TV, minibar"></label>
                <button class="btn" type="submit"><?= $editRoom ? 'Update room' : 'Create room' ?></button>
            </form>
        </section>
        <section class="card" style="margin-top: 18px;">
            <h2>Room list</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Hotel</th><th>Room</th><th>Type</th><th>Price</th><th>Status</th><th>Amenities</th><th>Unavailable until</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td><?= e($room['hotel_name']) ?></td>
                                <td><?= e($room['room_number']) ?></td>
                                <td><?= e($room['type']) ?></td>
                                <td><?= money($room['price']) ?></td>
                                <td><span class="status <?= e($room['status']) ?>"><?= e($room['status']) ?></span></td>
                                <td><?= e($room['amenities'] ?? '-') ?></td>
                                <td><?= e($room['reserved_until'] ?? $room['unavailable_until'] ?? '-') ?></td>
                                <td>
                                    <a class="btn secondary" href="<?= e(url('/admin/rooms.php?edit=' . (int) $room['id'])) ?>">Edit</a>
                                    <form class="inline-form" method="post">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $room['id'] ?>">
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
