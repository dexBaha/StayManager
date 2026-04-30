<?php
require_once __DIR__ . '/../../app/bootstrap.php';
Auth::requireAdmin();

$userModel = new User($db);
$editUser = null;

if (isset($_GET['edit'])) {
    $editUser = $userModel->find((int) $_GET['edit']);
}

if (isPost()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $userModel->create($_POST['name'], $_POST['email'], $_POST['password'], $_POST['role']);
        Session::flash('success', 'User created.');
    }

    if ($action === 'update') {
        $userModel->update((int) $_POST['id'], $_POST['name'], $_POST['email'], $_POST['role']);
        if (!empty($_POST['password'])) {
            $userModel->updatePassword((int) $_POST['id'], $_POST['password']);
        }
        Session::flash('success', 'User updated.');
    }

    if ($action === 'delete') {
        $userModel->delete((int) $_POST['id']);
        Session::flash('success', 'User deleted.');
    }

    redirect('/admin/users.php');
}

$users = $userModel->all();
$pageTitle = 'Manage users';
require_once __DIR__ . '/../includes/header.php';
?>
<main class="container admin-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    <section>
        <h1>Users</h1>
        <?php if ($message = Session::flash('success')): ?><div class="flash"><?= e($message) ?></div><?php endif; ?>
        <section class="card">
            <h2><?= $editUser ? 'Edit user' : 'Add user' ?></h2>
            <form class="form" method="post">
                <input type="hidden" name="action" value="<?= $editUser ? 'update' : 'create' ?>">
                <?php if ($editUser): ?><input type="hidden" name="id" value="<?= (int) $editUser['id'] ?>"><?php endif; ?>
                <div class="form-row">
                    <label>Name <input type="text" name="name" value="<?= e($editUser['name'] ?? '') ?>" required></label>
                    <label>Email <input type="email" name="email" value="<?= e($editUser['email'] ?? '') ?>" required></label>
                </div>
                <div class="form-row">
                    <label>Password <input type="password" name="password" <?= $editUser ? '' : 'required' ?>></label>
                    <label>Role
                        <select name="role">
                            <option value="user" <?= ($editUser['role'] ?? '') === 'user' ? 'selected' : '' ?>>user</option>
                            <option value="admin" <?= ($editUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>admin</option>
                        </select>
                    </label>
                </div>
                <button class="btn" type="submit"><?= $editUser ? 'Update user' : 'Create user' ?></button>
            </form>
        </section>
        <section class="card" style="margin-top: 18px;">
            <h2>User list</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= e($user['name']) ?></td>
                                <td><?= e($user['email']) ?></td>
                                <td><?= e($user['role']) ?></td>
                                <td>
                                    <a class="btn secondary" href="/admin/users.php?edit=<?= (int) $user['id'] ?>">Edit</a>
                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <form class="inline-form" method="post">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                            <button class="btn danger" type="submit">Delete</button>
                                        </form>
                                    <?php endif; ?>
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

