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
            <p class="mb-4 inline-flex rounded-full bg-white/15 px-4 py-2 text-sm font-bold ring-1 ring-white/20 backdrop-blur animate-soft-float">PHP OOP + PDO hotel platform</p>
            <h1 class="text-5xl font-black leading-tight tracking-tight sm:text-7xl">StayManager</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-100">Book rooms, manage reservations, and administer hotels from one polished PHP OOP application with Tailwind interfaces.</p>
            <div class="mt-8 flex flex-wrap gap-3">
            <a class="rounded-full bg-brand-500 px-6 py-3 text-sm font-black text-white shadow-xl shadow-brand-900/30 transition hover:bg-brand-600" href="<?= e(url('/rooms.php')) ?>">View rooms</a>
            <?php if (!Auth::check()): ?>
                <a class="rounded-full bg-white px-6 py-3 text-sm font-black text-slate-950 transition hover:bg-slate-100" href="<?= e(url('/register.php')) ?>">Create account</a>
            <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="grid gap-6 md:grid-cols-3">
        <article class="animate-card-in rounded-2xl border border-slate-200 bg-white p-6 shadow-soft" style="animation-delay: 60ms">
            <div class="mb-5 grid h-12 w-12 place-items-center rounded-xl bg-brand-50 text-xl font-black text-brand-600">1</div>
            <h2 class="text-xl font-black">User area</h2>
            <p class="mt-3 text-slate-600">Users can register, log in, browse rooms, create reservations, update reservation dates, and cancel reservations.</p>
        </article>
        <article class="animate-card-in rounded-2xl border border-slate-200 bg-white p-6 shadow-soft" style="animation-delay: 140ms">
            <div class="mb-5 grid h-12 w-12 place-items-center rounded-xl bg-amber-50 text-xl font-black text-amber-600">2</div>
            <h2 class="text-xl font-black">Admin area</h2>
            <p class="mt-3 text-slate-600">Admins can manage users, hotels, rooms, reservations, and view dashboard statistics with JavaScript charts.</p>
        </article>
        <article class="animate-card-in rounded-2xl border border-slate-200 bg-white p-6 shadow-soft" style="animation-delay: 220ms">
            <div class="mb-5 grid h-12 w-12 place-items-center rounded-xl bg-slate-100 text-xl font-black text-slate-700">3</div>
            <h2 class="text-xl font-black">PDO + OOP</h2>
            <p class="mt-3 text-slate-600">Database access uses PDO prepared statements, reusable classes, Git, and JSON API endpoints.</p>
        </article>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
