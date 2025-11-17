<?php
session_start();
require_once '../config/database.php';

if (!is_logged_in() || is_admin()) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil informasi langganan aktif
$query = "SELECT * FROM subscriptions 
          WHERE user_id = ? AND status = 'active' AND end_date > NOW() 
          ORDER BY end_date DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$subscription = $stmt->get_result()->fetch_assoc();

// Ambil buku yang sudah dibeli
$query = "SELECT b.*, p.purchase_date 
          FROM books b
          JOIN purchases p ON b.id = p.book_id
          WHERE p.user_id = ?
          ORDER BY p.purchase_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$purchased_books = $stmt->get_result();

// Ambil riwayat pembelian
$query = "SELECT b.title, b.cover_image, p.purchase_date, p.amount 
          FROM purchases p
          JOIN books b ON p.book_id = b.id
          WHERE p.user_id = ?
          ORDER BY p.purchase_date DESC
          LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$purchase_history = $stmt->get_result();

// Hitung statistik
$query = "SELECT COUNT(*) as total FROM purchases WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_purchased = $stmt->get_result()->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Galactic Library</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo" style="margin-bottom: 2rem;">
                <span>📚</span> Galactic Library
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">🏠 Dashboard</a></li>
                <li><a href="catalog.php">📖 Katalog Buku</a></li>
                <li><a href="subscription.php">💎 Langganan</a></li>
                <li><a href="purchase_history.php">📜 Riwayat Pembelian</a></li>
                <li><a href="../auth/logout.php" style="color: var(--danger-color);">🚪 Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h1>Selamat Datang, <?= $_SESSION['full_name'] ?>! 👋</h1>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                Mari lanjutkan petualangan membaca Anda hari ini
            </p>

            <!-- Status Langganan -->
            <?php if ($subscription): ?>
                <div class="card" style="background: var(--gradient-2); padding: 2rem; margin-bottom: 2rem;">
                    <div class="flex justify-between align-center">
                        <div>
                            <h3>Status Langganan: <?= strtoupper($subscription['plan_type']) ?> ✨</h3>
                            <p style="margin-top: 0.5rem;">
                                Aktif hingga: <strong><?= date('d M Y', strtotime($subscription['end_date'])) ?></strong>
                            </p>
                        </div>
                        <div class="badge" style="background: var(--success-color); color: var(--dark-bg); padding: 0.5rem 1rem;">
                            AKTIF
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card" style="border: 2px dashed var(--primary-color); padding: 2rem; margin-bottom: 2rem; text-align: center;">
                    <h3>Belum Ada Langganan</h3>
                    <p style="margin: 1rem 0;">Berlangganan sekarang dan dapatkan akses unlimited ke semua buku premium!</p>
                    <a href="subscription.php" class="btn btn-primary">Lihat Paket</a>
                </div>
            <?php endif; ?>

            <!-- Statistik -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= $total_purchased ?></div>
                    <div class="stat-label">Buku Dibeli</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--secondary-color);">
                    <div class="stat-value" style="color: var(--secondary-color);">
                        <?= $subscription ? '∞' : '0' ?>
                    </div>
                    <div class="stat-label">Akses Premium</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--accent-color);">
                    <div class="stat-value" style="color: var(--accent-color);">
                        <?= $purchased_books->num_rows ?>
                    </div>
                    <div class="stat-label">Perpustakaan Saya</div>
                </div>
            </div>

            <!-- Buku Saya -->
            <div style="margin-top: 3rem;">
                <h2>Perpustakaan Saya 📚</h2>
                <?php if ($purchased_books->num_rows > 0): ?>
                    <div class="cards-grid" style="margin-top: 1.5rem;">
                        <?php while ($book = $purchased_books->fetch_assoc()): ?>
                            <div class="card book-card">
                                <img src="../<?= $book['cover_image'] ?>" alt="<?= $book['title'] ?>" class="book-cover">
                                <h3 class="book-title"><?= $book['title'] ?></h3>
                                <p class="book-author">oleh <?= $book['author'] ?></p>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.5rem;">
                                    Dibeli: <?= date('d M Y', strtotime($book['purchase_date'])) ?>
                                </p>
                                <a href="reader.php?id=<?= $book['id'] ?>" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                                    📖 Baca Sekarang
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="card" style="text-align: center; padding: 3rem; margin-top: 1.5rem;">
                        <p style="font-size: 3rem; margin-bottom: 1rem;">📚</p>
                        <h3>Belum Ada Buku</h3>
                        <p style="margin: 1rem 0;">Mulai jelajahi katalog dan temukan buku favorit Anda!</p>
                        <a href="catalog.php" class="btn btn-primary">Jelajahi Katalog</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Riwayat Pembelian Terakhir -->
            <?php if ($purchase_history->num_rows > 0): ?>
                <div style="margin-top: 3rem;">
                    <h2>Riwayat Pembelian Terakhir 🛒</h2>
                    <div class="table-container" style="margin-top: 1.5rem;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Buku</th>
                                    <th>Tanggal</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($purchase = $purchase_history->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 1rem;">
                                                <img src="../<?= $purchase['cover_image'] ?>" 
                                                     alt="<?= $purchase['title'] ?>" 
                                                     style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;">
                                                <span><?= $purchase['title'] ?></span>
                                            </div>
                                        </td>
                                        <td><?= date('d M Y', strtotime($purchase['purchase_date'])) ?></td>
                                        <td style="color: var(--accent-color); font-weight: 600;">
                                            Rp <?= number_format($purchase['amount'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-2">
                        <a href="purchase_history.php" class="btn btn-secondary">Lihat Semua</a>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>