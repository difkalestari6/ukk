<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Perpustakaan Digital Modern - Akses ribuan buku digital berkualitas">
    <meta name="keywords" content="perpustakaan digital, ebook, buku online, membaca">
    <meta name="author" content="Galactic Library">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Galactic Library</title>
    <link rel="stylesheet" href="<?= isset($base_url) ? $base_url : '' ?>/assets/css/style.css">
    <link rel="icon" href="<?= isset($base_url) ? $base_url : '' ?>/assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <a href="<?= isset($base_url) ? $base_url : '' ?>/index.php" style="text-decoration: none; color: inherit;">
                    <span>📚</span> Galactic Library
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="<?= isset($base_url) ? $base_url : '' ?>/index.php">Beranda</a></li>
                    <li><a href="<?= isset($base_url) ? $base_url : '' ?>/user/catalog.php">Katalog</a></li>
                    <?php if (is_logged_in()): ?>
                        <?php if (is_admin()): ?>
                            <li><a href="<?= isset($base_url) ? $base_url : '' ?>/admin/index.php">Dashboard Admin</a></li>
                        <?php else: ?>
                            <li><a href="<?= isset($base_url) ? $base_url : '' ?>/user/dashboard.php">Dashboard</a></li>
                            <li><a href="<?= isset($base_url) ? $base_url : '' ?>/user/subscription.php">Langganan</a></li>
                        <?php endif; ?>
                        <li><a href="<?= isset($base_url) ? $base_url : '' ?>/auth/logout.php" class="btn btn-danger">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?= isset($base_url) ? $base_url : '' ?>/auth/login.php" class="btn btn-secondary">Login</a></li>
                        <li><a href="<?= isset($base_url) ? $base_url : '' ?>/auth/register.php" class="btn btn-primary">Daftar</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </nav>