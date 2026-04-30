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
<main class="mx-auto grid min-h-[calc(100vh-170px)] max-w-7xl items-center px-4 py-12 sm:px-6 lg:px-8">
    <section class="mx-auto w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-soft">
        <p class="text-sm font-black uppercase tracking-widest text-brand-600">Welcome back</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight">Login</h1>
        <?php if ($message = Session::flash('success')): ?><div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700"><?= e($message) ?></div><?php endif; ?>
        <?php if ($message = Session::flash('error')): ?><div class="mt-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700"><?= e($message) ?></div><?php endif; ?>
        <form class="mt-6 grid gap-5" method="post">
            <label class="grid gap-2 text-sm font-bold text-slate-700">Email <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100" type="email" name="email" required></label>
            <label class="grid gap-2 text-sm font-bold text-slate-700">Password <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100" type="password" name="password" required></label>
            <button class="rounded-2xl bg-brand-600 px-5 py-3 font-black text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-900" type="submit">Login</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
