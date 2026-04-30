<?php
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Hotel Management') ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/style.css')) ?>">
</head>
<body>
<header class="topbar">
    <nav class="nav">
        <a href="<?= e(url('/')) ?>" class="brand">StayManager</a>
        <div class="nav-links">
            <a href="<?= e(url('/')) ?>" class="<?= isCurrentPath('/index.php') || isCurrentPath('/public/') ? 'active' : '' ?>">Home</a>
            <a href="<?= e(url('/rooms.php')) ?>" class="<?= isCurrentPath('/rooms.php') ? 'active' : '' ?>">Rooms</a>
            <?php if ($currentUser): ?>
                <a href="<?= e(url('/dashboard.php')) ?>">My account</a>
                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                    <a href="<?= e(url('/admin/index.php')) ?>">Admin</a>
                <?php endif; ?>
                <a href="<?= e(url('/logout.php')) ?>">Logout</a>
            <?php else: ?>
                <a href="<?= e(url('/login.php')) ?>">Login</a>
                <a href="<?= e(url('/register.php')) ?>">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
