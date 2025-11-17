<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galactic Library - Perpustakaan Digital Modern</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <span>📚</span> Galactic Library
            </div>
            <nav>
                <ul>
                    <li><a href="#home">Beranda</a></li>
                    <li><a href="#catalog">Katalog</a></li>
                    <li><a href="#subscription">Langganan</a></li>
                    <?php if (is_logged_in()): ?>
                        <?php if (is_admin()): ?>
                            <li><a href="admin/index.php">Dashboard Admin</a></li>
                        <?php else: ?>
                            <li><a href="user/dashboard.php">Dashboard</a></li>
                        <?php endif; ?>
                        <li><a href="auth/logout.php" class="btn btn-danger">Logout</a></li>
                    <?php else: ?>
                        <li><a href="auth/login.php" class="btn btn-secondary">Login</a></li>
                        <li><a href="auth/register.php" class="btn btn-primary">Daftar</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <h1>Jelajahi Dunia Pengetahuan Tanpa Batas</h1>
            <p>Akses ribuan buku digital berkualitas tinggi. Baca kapan saja, di mana saja, dari perangkat apa saja.</p>
            <div class="hero-buttons">
                <a href="auth/register.php" class="btn btn-primary">Mulai Gratis</a>
                <a href="#catalog" class="btn btn-secondary">Lihat Katalog</a>
            </div>
        </div>
    </section>

    <!-- Featured Books -->
    <section class="section" id="catalog">
        <div class="container">
            <div class="section-title">
                <h2>Buku Pilihan</h2>
                <p>Koleksi terbaik untuk Anda</p>
            </div>
            
            <div class="cards-grid">
                <?php
                // Ambil 6 buku terbaru
                $query = "SELECT * FROM books ORDER BY created_at DESC LIMIT 6";
                $result = $conn->query($query);
                
                while ($book = $result->fetch_assoc()):
                ?>
                <div class="card book-card" onclick="window.location.href='user/book_detail.php?id=<?= $book['id'] ?>'">
                    <img src="<?= $book['cover_image'] ?>" alt="<?= $book['title'] ?>" class="book-cover">
                    <h3 class="book-title"><?= $book['title'] ?></h3>
                    <p class="book-author">oleh <?= $book['author'] ?></p>
                    <div class="book-price">
                        <?php if ($book['is_free']): ?>
                            <span class="badge badge-free">GRATIS</span>
                        <?php else: ?>
                            <span class="price-tag">Rp <?= number_format($book['price'], 0, ',', '.') ?></span>
                            <span class="badge badge-premium">Premium</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <div class="text-center mt-2">
                <a href="user/catalog.php" class="btn btn-primary">Lihat Semua Buku</a>
            </div>
        </div>
    </section>

    <!-- Subscription Plans -->
    <section class="section" id="subscription" style="background: rgba(20, 27, 60, 0.5);">
        <div class="container">
            <div class="section-title">
                <h2>Paket Langganan</h2>
                <p>Akses unlimited ke semua buku premium</p>
            </div>
            
            <div class="plans-grid">
                <div class="plan-card">
                    <div class="plan-name">🥈 Silver</div>
                    <div class="plan-price">Rp 29.000</div>
                    <p>per minggu</p>
                    <ul class="plan-features">
                        <li>Akses semua buku premium</li>
                        <li>Baca offline</li>
                        <li>Sinkronisasi antar perangkat</li>
                        <li>Berlaku 7 hari</li>
                    </ul>
                    <a href="auth/login.php" class="btn btn-primary">Pilih Silver</a>
                </div>
                
                <div class="plan-card">
                    <div class="plan-name">🥇 Gold</div>
                    <div class="plan-price">Rp 99.000</div>
                    <p>per bulan</p>
                    <ul class="plan-features">
                        <li>Semua fitur Silver</li>
                        <li>Akses early release</li>
                        <li>Download unlimited</li>
                        <li>Berlaku 30 hari</li>
                        <li>Prioritas customer service</li>
                    </ul>
                    <a href="auth/login.php" class="btn btn-primary">Pilih Gold</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Kenapa Memilih Kami?</h2>
            </div>
            
            <div class="cards-grid">
                <div class="card">
                    <h3>📱 Multi-Platform</h3>
                    <p>Baca dari smartphone, tablet, atau komputer dengan sinkronisasi otomatis</p>
                </div>
                <div class="card">
                    <h3>🔖 Bookmark & Notes</h3>
                    <p>Tandai halaman favorit dan buat catatan pribadi di setiap buku</p>
                </div>
                <div class="card">
                    <h3>🌙 Mode Gelap</h3>
                    <p>Tampilan nyaman untuk mata, sempurna untuk membaca di malam hari</p>
                </div>
                <div class="card">
                    <h3>💎 Koleksi Premium</h3>
                    <p>Ribuan buku berkualitas dari penerbit ternama</p>
                </div>
                <div class="card">
                    <h3>⚡ Akses Cepat</h3>
                    <p>Streaming buku tanpa perlu download, hemat ruang penyimpanan</p>
                </div>
                <div class="card">
                    <h3>🎯 Rekomendasi Cerdas</h3>
                    <p>Dapatkan saran buku sesuai minat dan riwayat bacaan Anda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: var(--card-bg); padding: 3rem 0; margin-top: 4rem; text-align: center;">
        <div class="container">
            <div class="logo" style="justify-content: center; margin-bottom: 1rem;">
                <span>📚</span> Galactic Library
            </div>
            <p style="color: var(--text-secondary);">© 2024 Galactic Library. All rights reserved.</p>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">
                <a href="#" style="color: var(--primary-color); text-decoration: none;">Privacy Policy</a> | 
                <a href="#" style="color: var(--primary-color); text-decoration: none;">Terms of Service</a>
            </p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>