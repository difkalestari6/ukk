<?php
session_start();
require_once '../config/database.php';

if (!is_logged_in() || is_admin()) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil riwayat pembelian buku
$query = "SELECT b.*, p.purchase_date, p.amount 
          FROM purchases p
          JOIN books b ON p.book_id = b.id
          WHERE p.user_id = ?
          ORDER BY p.purchase_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$purchases = $stmt->get_result();

// Ambil riwayat langganan
$query = "SELECT * FROM subscriptions 
          WHERE user_id = ?
          ORDER BY start_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$subscriptions = $stmt->get_result();

// Hitung total pengeluaran
$query = "SELECT SUM(amount) as total FROM purchases WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_purchases = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

$query = "SELECT SUM(amount) as total FROM subscriptions WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_subscriptions = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian - Galactic Library</title>
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
                <li><a href="dashboard.php">🏠 Dashboard</a></li>
                <li><a href="catalog.php">📖 Katalog Buku</a></li>
                <li><a href="subscription.php">💎 Langganan</a></li>
                <li><a href="purchase_history.php" class="active">📜 Riwayat Pembelian</a></li>
                <li><a href="../auth/logout.php" style="color: var(--danger-color);">🚪 Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h1>Riwayat Pembelian 📜</h1>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                Lihat semua transaksi pembelian dan langganan Anda
            </p>

            <!-- Summary Cards -->
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 3rem;">
                <div class="stat-card">
                    <div class="stat-value" style="font-size: 1.8rem;">
                        Rp <?= number_format($total_purchases, 0, ',', '.') ?>
                    </div>
                    <div class="stat-label">Total Pembelian Buku</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--secondary-color);">
                    <div class="stat-value" style="color: var(--secondary-color); font-size: 1.8rem;">
                        Rp <?= number_format($total_subscriptions, 0, ',', '.') ?>
                    </div>
                    <div class="stat-label">Total Langganan</div>
                </div>
                <div class="stat-card" style="border-left-color: var(--accent-color);">
                    <div class="stat-value" style="color: var(--accent-color); font-size: 1.8rem;">
                        Rp <?= number_format($total_purchases + $total_subscriptions, 0, ',', '.') ?>
                    </div>
                    <div class="stat-label">Total Pengeluaran</div>
                </div>
            </div>

            <!-- Pembelian Buku -->
            <div style="margin-bottom: 3rem;">
                <h2>Pembelian Buku 📚</h2>
                <?php if ($purchases->num_rows > 0): ?>
                    <div class="table-container" style="margin-top: 1.5rem;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Buku</th>
                                    <th>Tanggal Pembelian</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($purchase = $purchases->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 1rem;">
                                                <img src="../<?= $purchase['cover_image'] ?>" 
                                                     alt="<?= $purchase['title'] ?>" 
                                                     style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;">
                                                <div>
                                                    <strong><?= $purchase['title'] ?></strong>
                                                    <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.2rem;">
                                                        oleh <?= $purchase['author'] ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= date('d M Y, H:i', strtotime($purchase['purchase_date'])) ?></td>
                                        <td style="color: var(--accent-color); font-weight: 600;">
                                            Rp <?= number_format($purchase['amount'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <a href="reader.php?id=<?= $purchase['id'] ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                                                📖 Baca
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="card" style="text-align: center; padding: 3rem; margin-top: 1.5rem;">
                        <p style="font-size: 3rem; margin-bottom: 1rem;">📚</p>
                        <h3>Belum Ada Pembelian</h3>
                        <p style="margin: 1rem 0;">Mulai jelajahi katalog dan beli buku favorit Anda!</p>
                        <a href="catalog.php" class="btn btn-primary">Jelajahi Katalog</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Riwayat Langganan -->
            <div>
                <h2>Riwayat Langganan 💎</h2>
                <?php if ($subscriptions->num_rows > 0): ?>
                    <div class="table-container" style="margin-top: 1.5rem;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Paket</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Berakhir</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($sub = $subscriptions->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <span class="badge" style="background: <?= $sub['plan_type'] == 'gold' ? 'var(--warning-color)' : 'var(--text-secondary)' ?>; color: var(--dark-bg); padding: 0.5rem 1rem;">
                                                <?= $sub['plan_type'] == 'gold' ? '🥇' : '🥈' ?> <?= strtoupper($sub['plan_type']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($sub['start_date'])) ?></td>
                                        <td><?= date('d M Y', strtotime($sub['end_date'])) ?></td>
                                        <td style="color: var(--accent-color); font-weight: 600;">
                                            Rp <?= number_format($sub['amount'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <?php if ($sub['status'] == 'active' && strtotime($sub['end_date']) > time()): ?>
                                                <span class="badge badge-free">AKTIF</span>
                                            <?php else: ?>
                                                <span class="badge" style="background: var(--text-secondary); color: var(--dark-bg);">EXPIRED</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="card" style="text-align: center; padding: 3rem; margin-top: 1.5rem;">
                        <p style="font-size: 3rem; margin-bottom: 1rem;">💎</p>
                        <h3>Belum Ada Langganan</h3>
                        <p style="margin: 1rem 0;">Berlangganan untuk akses unlimited ke semua buku premium!</p>
                        <a href="subscription.php" class="btn btn-primary">Lihat Paket</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>