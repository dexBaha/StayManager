<?php
$currentUser = Auth::user();
$currentPath = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Hotel Management') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="topbar">
    <nav class="nav">
        <a href="/" class="brand">StayManager</a>
        <div class="nav-links">
            <a href="/" class="<?= $currentPath === '/index.php' ? 'active' : '' ?>">Home</a>
            <a href="/rooms.php" class="<?= $currentPath === '/rooms.php' ? 'active' : '' ?>">Rooms</a>
            <?php if ($currentUser): ?>
                <a href="/dashboard.php">My account</a>
                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                    <a href="/admin/index.php">Admin</a>
                <?php endif; ?>
                <a href="/logout.php">Logout</a>
            <?php else: ?>
                <a href="/login.php">Login</a>
                <a href="/register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

