<?php
session_start();
require_once '../config/database.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../auth/login.php');
    exit();
}

// Statistik
$query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$total_users = $conn->query($query)->fetch_assoc()['total'];

$query = "SELECT COUNT(*) as total FROM books";
$total_books = $conn->query($query)->fetch_assoc()['total'];

$query = "SELECT COUNT(*) as total FROM subscriptions WHERE status = 'active' AND end_date > NOW()";
$active_subscriptions = $conn->query($query)->fetch_assoc()['total'];

$query = "SELECT SUM(amount) as total FROM purchases";
$total_revenue = $conn->query($query)->fetch_assoc()['total'] ?? 0;

$query = "SELECT SUM(amount) as total FROM subscriptions WHERE status = 'active'";
$subscription_revenue = $conn->query($query)->fetch_assoc()['total'] ?? 0;

// Pembelian terbaru
$query = "SELECT u.full_name, b.title, p.amount, p.purchase_date 
          FROM purchases p
          JOIN users u ON p.user_id = u.id
          JOIN books b ON p.book_id = b.id
          ORDER BY p.purchase_date DESC
          LIMIT 5";
$recent_purchases = $conn->query($query);

// Langganan terbaru
$query = "SELECT u.full_name, s.plan_type, s.amount, s.start_date, s.end_date 
          FROM subscriptions s
          JOIN users u ON s.user_id = u.id
          ORDER BY s.start_date DESC
          LIMIT 5";
$recent_subscriptions = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Galactic Library</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo" style="margin-bottom: 2rem;">
                <span>📚</span> Admin Panel
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="active">📊 Dashboard</a></li>
                <li><a href="manage_books.php">📚 Kelola Buku</a></li>
                <li><a href="manage_users.php">👥 Kelola User</a></li>
                <li><a href="statistics.php">📈 Statistik</a></li>
                <li><a href="../index.php">🏠 Ke Beranda</a></li>
                <li><a href="../auth/logout.php" style="color: var(--danger-color);">🚪 Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h1>Dashboard Admin 👨‍💼</h1>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                Selamat datang, <?= $_SESSION['full_name'] ?>
            </p>

            <!-- Statistik Utama -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= $total_users ?></div>
                    <div class="stat-label">Total User</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--secondary-color);">
                    <div class="stat-value" style="color: var(--secondary-color);"><?= $total_books ?></div>
                    <div class="stat-label">Total Buku</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--accent-color);">
                    <div class="stat-value" style="color: var(--accent-color);"><?= $active_subscriptions ?></div>
                    <div class="stat-label">Langganan Aktif</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--success-color);">
                    <div class="stat-value" style="color: var(--success-color); font-size: 1.5rem;">
                        Rp <?= number_format($total_revenue + $subscription_revenue, 0, ',', '.') ?>
                    </div>
                    <div class="stat-label">Total Pendapatan</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="margin-top: 3rem;">
                <h2>Quick Actions ⚡</h2>
                <div class="cards-grid" style="margin-top: 1.5rem; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                    <a href="manage_books.php?action=add" class="card" style="text-decoration: none; text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">➕</div>
                        <h3>Tambah Buku</h3>
                    </a>
                    <a href="manage_users.php" class="card" style="text-decoration: none; text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                        <h3>Kelola User</h3>
                    </a>
                    <a href="statistics.php" class="card" style="text-decoration: none; text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📊</div>
                        <h3>Lihat Laporan</h3>
                    </a>
                </div>
            </div>

            <!-- Pembelian Terbaru -->
            <div style="margin-top: 3rem;">
                <h2>Pembelian Terbaru 🛒</h2>
                <div class="table-container" style="margin-top: 1.5rem;">
                    <?php if ($recent_purchases->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Buku</th>
                                    <th>Harga</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($purchase = $recent_purchases->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $purchase['full_name'] ?></td>
                                        <td><?= $purchase['title'] ?></td>
                                        <td style="color: var(--success-color); font-weight: 600;">
                                            Rp <?= number_format($purchase['amount'], 0, ',', '.') ?>
                                        </td>
                                        <td><?= date('d M Y H:i', strtotime($purchase['purchase_date'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center" style="padding: 2rem;">Belum ada pembelian</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Langganan Terbaru -->
            <div style="margin-top: 3rem;">
                <h2>Langganan Terbaru 💎</h2>
                <div class="table-container" style="margin-top: 1.5rem;">
                    <?php if ($recent_subscriptions->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Paket</th>
                                    <th>Harga</th>
                                    <th>Mulai</th>
                                    <th>Berakhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($sub = $recent_subscriptions->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $sub['full_name'] ?></td>
                                        <td>
                                            <span class="badge" style="background: <?= $sub['plan_type'] == 'gold' ? 'var(--warning-color)' : 'var(--text-secondary)' ?>; color: var(--dark-bg);">
                                                <?= strtoupper($sub['plan_type']) ?>
                                            </span>
                                        </td>
                                        <td style="color: var(--success-color); font-weight: 600;">
                                            Rp <?= number_format($sub['amount'], 0, ',', '.') ?>
                                        </td>
                                        <td><?= date('d M Y', strtotime($sub['start_date'])) ?></td>
                                        <td><?= date('d M Y', strtotime($sub['end_date'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center" style="padding: 2rem;">Belum ada langganan</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>