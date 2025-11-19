<?php
session_start();
require_once '../config/database.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../auth/login.php');
    exit();
}

// Revenue
$query = "SELECT SUM(amount) as total FROM purchases";
$book_revenue = $conn->query($query)->fetch_assoc()['total'] ?? 0;

$query = "SELECT SUM(amount) as total FROM subscriptions";
$sub_revenue = $conn->query($query)->fetch_assoc()['total'] ?? 0;

// Top Books
$query = "SELECT b.title, b.cover_image, COUNT(p.id) as sales, SUM(p.amount) as revenue
          FROM purchases p
          JOIN books b ON p.book_id = b.id
          GROUP BY p.book_id
          ORDER BY sales DESC
          LIMIT 5";
$top_books = $conn->query($query);

// Categories
$query = "SELECT category, COUNT(*) as total FROM books GROUP BY category";
$categories = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo" style="margin-bottom: 2rem;">
                <span>📚</span> Admin Panel
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php">📊 Dashboard</a></li>
                <li><a href="manage_books.php">📚 Kelola Buku</a></li>
                <li><a href="manage_users.php">👥 Kelola User</a></li>
                <li><a href="statistics.php" class="active">📈 Statistik</a></li>
                <li><a href="../index.php">🏠 Ke Beranda</a></li>
                <li><a href="../auth/logout.php" style="color: var(--danger-color);">🚪 Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1>Statistik & Laporan 📈</h1>

            <!-- Revenue Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" style="font-size: 1.5rem;">
                        Rp <?= number_format($book_revenue, 0, ',', '.') ?>
                    </div>
                    <div class="stat-label">Revenue Buku</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--secondary-color);">
                    <div class="stat-value" style="color: var(--secondary-color); font-size: 1.5rem;">
                        Rp <?= number_format($sub_revenue, 0, ',', '.') ?>
                    </div>
                    <div class="stat-label">Revenue Langganan</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--success-color);">
                    <div class="stat-value" style="color: var(--success-color); font-size: 1.5rem;">
                        Rp <?= number_format($book_revenue + $sub_revenue, 0, ',', '.') ?>
                    </div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>

            <!-- Top Books -->
            <div style="margin-top: 3rem;">
                <h2>Buku Terlaris 🏆</h2>
                <?php if ($top_books->num_rows > 0): ?>
                    <div class="table-container" style="margin-top: 1.5rem;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Buku</th>
                                    <th>Penjualan</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rank = 1;
                                while ($book = $top_books->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td>
                                            <span style="font-size: 1.5rem;">
                                                <?= $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : $rank)) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 1rem;">
                                                <img src="../<?= $book['cover_image'] ?>" 
                                                     style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;">
                                                <strong><?= $book['title'] ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: var(--accent-color); color: var(--dark-bg);">
                                                <?= $book['sales'] ?> terjual
                                            </span>
                                        </td>
                                        <td style="color: var(--success-color); font-weight: 700;">
                                            Rp <?= number_format($book['revenue'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php 
                                $rank++;
                                endwhile; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center" style="padding: 2rem;">Belum ada data</p>
                <?php endif; ?>
            </div>

            <!-- Categories -->
            <div style="margin-top: 3rem;">
                <h2>Kategori Buku 📂</h2>
                <div class="cards-grid" style="margin-top: 1.5rem;">
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <div class="card" style="text-align: center; padding: 2rem;">
                            <h3><?= $cat['category'] ?></h3>
                            <p style="color: var(--primary-color); font-size: 2rem; font-weight: 700;">
                                <?= $cat['total'] ?>
                            </p>
                            <p style="color: var(--text-secondary);">buku</p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>