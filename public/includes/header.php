<?php
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Hotel Management') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf8',
                            100: '#d1faee',
                            500: '#0f766e',
                            600: '#0f5f59',
                            900: '#123735'
                        }
                    },
                    boxShadow: {
                        soft: '0 20px 60px rgba(15, 23, 42, 0.10)'
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="<?= e(url('/assets/css/style.css')) ?>">
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
<header class="sticky top-0 z-40 border-b border-white/70 bg-white/90 shadow-sm backdrop-blur">
    <nav class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <a href="<?= e(url('/')) ?>" class="flex items-center gap-3 text-2xl font-black tracking-tight text-brand-600">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-600 text-white shadow-lg shadow-brand-600/20">S</span>
            StayManager
        </a>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= e(url('/')) ?>" class="rounded-full px-4 py-2 text-sm font-bold transition <?= isCurrentPath('/index.php') || isCurrentPath('/public/') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' ?>">Home</a>
            <a href="<?= e(url('/rooms.php')) ?>" class="rounded-full px-4 py-2 text-sm font-bold transition <?= isCurrentPath('/rooms.php') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' ?>">Rooms</a>
            <?php if ($currentUser): ?>
                <a href="<?= e(url('/dashboard.php')) ?>" class="rounded-full px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">My account</a>
                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                    <a href="<?= e(url('/admin/index.php')) ?>" class="rounded-full px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">Admin</a>
                <?php endif; ?>
                <a href="<?= e(url('/logout.php')) ?>" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-700">Logout</a>
            <?php else: ?>
                <a href="<?= e(url('/login.php')) ?>" class="rounded-full px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950">Login</a>
                <a href="<?= e(url('/register.php')) ?>" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-slate-900/10 transition hover:bg-slate-700">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
