<?php
require_once __DIR__ . '/../app/bootstrap.php';

$auth = new Auth(new User($db));

if (isPost()) {
    if ($auth->login(trim($_POST['email']), $_POST['password'])) {
        redirect(Auth::isAdmin() ? '/admin/index.php' : '/dashboard.php');
    }

    Session::flash('error', 'Invalid email or password.');
}

$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>
<main class="container">
    <section class="card">
        <h1>Login</h1>
        <?php if ($message = Session::flash('success')): ?><div class="flash"><?= e($message) ?></div><?php endif; ?>
        <?php if ($message = Session::flash('error')): ?><div class="flash"><?= e($message) ?></div><?php endif; ?>
        <form class="form" method="post">
            <label>Email <input type="email" name="email" required></label>
            <label>Password <input type="password" name="password" required></label>
            <button class="btn" type="submit">Login</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

