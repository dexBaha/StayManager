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
    $stars = max(1, min(5, (int) ($_POST['stars'] ?? 3)));
    $photoUrl = trim($_POST['photo_url'] ?? '');

    try {
        $uploadedPhoto = uploadImage('photo_file', 'uploads/hotels');

        if ($uploadedPhoto !== null) {
            $photoUrl = $uploadedPhoto;
        }
    } catch (RuntimeException $exception) {
        Session::flash('error', $exception->getMessage());
        redirect('/admin/hotels.php' . ($action === 'update' && !empty($_POST['id']) ? '?edit=' . (int) $_POST['id'] : ''));
    }

    if ($action === 'create') {
        $hotelModel->create($_POST['name'], $_POST['city'], $_POST['country'], $stars, $photoUrl, $_POST['address'], $_POST['description']);
        Session::flash('success', 'Hotel created.');
    }

    if ($action === 'update') {
        $hotelModel->update((int) $_POST['id'], $_POST['name'], $_POST['city'], $_POST['country'], $stars, $photoUrl, $_POST['address'], $_POST['description']);
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
        <?php if ($message = Session::flash('error')): ?><div class="flash" style="border-color:#fecaca;background:#fef2f2;color:#b91c1c;"><?= e($message) ?></div><?php endif; ?>
        <section class="card">
            <h2><?= $editHotel ? 'Edit hotel' : 'Add hotel' ?></h2>
            <form class="form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $editHotel ? 'update' : 'create' ?>">
                <?php if ($editHotel): ?><input type="hidden" name="id" value="<?= (int) $editHotel['id'] ?>"><?php endif; ?>
                <div class="form-row">
                    <label>Name <input type="text" name="name" value="<?= e($editHotel['name'] ?? '') ?>" required></label>
                    <label>City <input type="text" name="city" value="<?= e($editHotel['city'] ?? '') ?>" required></label>
                    <label>Country <input type="text" name="country" value="<?= e($editHotel['country'] ?? '') ?>" required></label>
                    <label>Stars <input type="number" name="stars" min="1" max="5" value="<?= e((string) ($editHotel['stars'] ?? 4)) ?>" required></label>
                </div>
                <label>Photo URL <input type="url" name="photo_url" value="<?= e($editHotel['photo_url'] ?? '') ?>" placeholder="https://example.com/hotel.jpg"></label>
                <label>Upload hotel photo <input type="file" name="photo_file" accept="image/jpeg,image/png,image/webp,image/gif"></label>
                <p class="muted">Upload accepts JPG, PNG, WEBP, or GIF up to 2 MB. If you upload a file, it replaces the photo URL.</p>
                <?php if (!empty($editHotel['photo_url'])): ?>
                    <img src="<?= e(mediaUrl($editHotel['photo_url'])) ?>" alt="<?= e($editHotel['name']) ?> preview" style="max-width: 220px; border-radius: 16px; margin: 8px 0;">
                <?php endif; ?>
                <label>Address <input type="text" name="address" value="<?= e($editHotel['address'] ?? '') ?>" required></label>
                <label>Description <textarea name="description" required><?= e($editHotel['description'] ?? '') ?></textarea></label>
                <button class="btn" type="submit"><?= $editHotel ? 'Update hotel' : 'Create hotel' ?></button>
            </form>
        </section>
        <section class="card" style="margin-top: 18px;">
            <h2>Hotel list</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Photo</th><th>Name</th><th>Location</th><th>Stars</th><th>Address</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($hotels as $hotel): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($hotel['photo_url'])): ?>
                                        <img src="<?= e(mediaUrl($hotel['photo_url'])) ?>" alt="<?= e($hotel['name']) ?>" style="height:54px;width:86px;object-fit:cover;border-radius:10px;">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= e($hotel['name']) ?></td>
                                <td><?= e($hotel['city']) ?>, <?= e($hotel['country']) ?></td>
                                <td><?= str_repeat('★', (int) ($hotel['stars'] ?? 0)) ?></td>
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
