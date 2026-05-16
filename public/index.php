<?php
require_once __DIR__ . '/../app/bootstrap.php';
$pageTitle = 'Hotel Management';
require_once __DIR__ . '/includes/header.php';
?>
<section class="relative overflow-hidden animate-page-rise">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1600&q=80')] bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-brand-600/40"></div>
    <div class="relative mx-auto grid min-h-[540px] max-w-7xl items-center px-4 py-20 sm:px-6 lg:px-8">
        <div class="max-w-3xl text-white">
            <h1 class="text-5xl font-black leading-tight tracking-tight sm:text-7xl">StayManager</h1>
            <div class="mt-8 flex flex-wrap gap-3">
            <a class="rounded-full bg-brand-500 px-6 py-3 text-sm font-black text-white shadow-xl shadow-brand-900/30 transition hover:bg-brand-600" href="<?= e(url('/rooms.php')) ?>">View rooms</a>
            <?php if (!Auth::check()): ?>
                <a class="rounded-full bg-white px-6 py-3 text-sm font-black text-slate-950 transition hover:bg-slate-100" href="<?= e(url('/register.php')) ?>">Create account</a>
            <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
