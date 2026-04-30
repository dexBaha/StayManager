<?php
require_once __DIR__ . '/../app/bootstrap.php';

$auth = new Auth(new User($db));

if (isPost()) {
    $ok = $auth->register(trim($_POST['name']), trim($_POST['email']), $_POST['password']);

    if ($ok) {
        Session::flash('success', 'Account created. You can login now.');
        redirect('/login.php');
    }

    Session::flash('error', 'Email already exists.');
}

$pageTitle = 'Register';
require_once __DIR__ . '/includes/header.php';
?>
<main class="container">
    <section class="card">
        <h1>Create account</h1>
        <?php if ($message = Session::flash('error')): ?><div class="flash"><?= e($message) ?></div><?php endif; ?>
        <form class="form" method="post">
            <label>Name <input type="text" name="name" required></label>
            <label>Email <input type="email" name="email" required></label>
            <label>Password <input type="password" name="password" minlength="6" required></label>
            <button class="btn" type="submit">Register</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

