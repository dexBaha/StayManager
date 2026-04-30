<?php
require_once __DIR__ . '/../../app/bootstrap.php';
Auth::requireAdmin();

$hotelModel = new Hotel($db);
$editHotel = null;

if (isset($_GET['edit'])) {
    $editHotel = $hotelModel->find((int) $_GET['edit']);
}

if (isPost()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $hotelModel->create($_POST['name'], $_POST['city'], $_POST['address'], $_POST['description']);
        Session::flash('success', 'Hotel created.');
    }

    if ($action === 'update') {
        $hotelModel->update((int) $_POST['id'], $_POST['name'], $_POST['city'], $_POST['address'], $_POST['description']);
        Session::flash('success', 'Hotel updated.');
    }

    if ($action === 'delete') {
        $hotelModel->delete((int) $_POST['id']);
        Session::flash('success', 'Hotel deleted.');
    }

    redirect('/admin/hotels.php');
}

$hotels = $hotelModel->all();
$pageTitle = 'Manage hotels';
require_once __DIR__ . '/../includes/header.php';
?>
<main class="container admin-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <section>
        <h1>Hotels</h1>
        <?php if ($message = Session::flash('success')): ?><div class="flash"><?= e($message) ?></div><?php endif; ?>
        <section class="card">
            <h2><?= $editHotel ? 'Edit hotel' : 'Add hotel' ?></h2>
            <form class="form" method="post">
                <input type="hidden" name="action" value="<?= $editHotel ? 'update' : 'create' ?>">
                <?php if ($editHotel): ?><input type="hidden" name="id" value="<?= (int) $editHotel['id'] ?>"><?php endif; ?>
                <div class="form-row">
                    <label>Name <input type="text" name="name" value="<?= e($editHotel['name'] ?? '') ?>" required></label>
                    <label>City <input type="text" name="city" value="<?= e($editHotel['city'] ?? '') ?>" required></label>
                </div>
                <label>Address <input type="text" name="address" value="<?= e($editHotel['address'] ?? '') ?>" required></label>
                <label>Description <textarea name="description" required><?= e($editHotel['description'] ?? '') ?></textarea></label>
                <button class="btn" type="submit"><?= $editHotel ? 'Update hotel' : 'Create hotel' ?></button>
            </form>
        </section>
        <section class="card" style="margin-top: 18px;">
            <h2>Hotel list</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Name</th><th>City</th><th>Address</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($hotels as $hotel): ?>
                            <tr>
                                <td><?= e($hotel['name']) ?></td>
                                <td><?= e($hotel['city']) ?></td>
                                <td><?= e($hotel['address']) ?></td>
                                <td>
                                    <a class="btn secondary" href="<?= e(url('/admin/hotels.php?edit=' . (int) $hotel['id'])) ?>">Edit</a>
                                    <form class="inline-form" method="post">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $hotel['id'] ?>">
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
