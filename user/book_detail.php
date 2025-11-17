<?php
session_start();
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: catalog.php');
    exit();
}

$book_id = (int)$_GET['id'];

// Get book details
$query = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    header('Location: catalog.php');
    exit();
}

// Check if user can access
$can_access = false;
$purchase_required = false;

if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    $can_access = can_access_book($user_id, $book_id);
    $purchase_required = !$can_access && !$book['is_free'];
}

// Handle purchase
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['purchase']) && is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    
    // Check if already purchased
    $query = "SELECT * FROM purchases WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows == 0) {
        // Insert purchase
        $query = "INSERT INTO purchases (user_id, book_id, amount) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iid", $user_id, $book_id, $book['price']);
        
        if ($stmt->execute()) {
            header("Location: book_detail.php?id=$book_id&purchased=1");
            exit();
        }
    }
}

// Get similar books
$query = "SELECT * FROM books WHERE category = ? AND id != ? ORDER BY RAND() LIMIT 4";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $book['category'], $book_id);
$stmt->execute();
$similar_books = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $book['title'] ?> - Galactic Library</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <a href="../index.php" style="text-decoration: none; color: inherit;">
                    <span>📚</span> Galactic Library
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="catalog.php">Katalog</a></li>
                    <?php if (is_logged_in()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="../auth/login.php" class="btn btn-secondary">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </nav>

    <div class="container" style="padding: 3rem 2rem;">
        <?php if (isset($_GET['purchased'])): ?>
            <div class="alert" style="background: rgba(0, 245, 160, 0.1); color: var(--success-color); padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                ✅ Pembelian berhasil! Buku telah ditambahkan ke perpustakaan Anda.
            </div>
        <?php endif; ?>

        <!-- Book Detail -->
        <div style="display: grid; grid-template-columns: 350px 1fr; gap: 3rem; margin-bottom: 4rem;">
            <!-- Cover -->
            <div>
                <img src="../<?= $book['cover_image'] ?>" alt="<?= $book['title'] ?>" 
                     style="width: 100%; border-radius: 16px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);">
                
                <div class="card" style="margin-top: 2rem; padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <?php if ($book['is_free']): ?>
                            <span class="badge badge-free" style="font-size: 1rem; padding: 0.5rem 1rem;">GRATIS</span>
                        <?php else: ?>
                            <span style="font-size: 1.8rem; font-weight: 700; color: var(--accent-color);">
                                Rp <?= number_format($book['price'], 0, ',', '.') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!is_logged_in()): ?>
                        <a href="../auth/login.php" class="btn btn-primary" style="width: 100%;">
                            Login untuk Membaca
                        </a>
                    <?php elseif ($can_access): ?>
                        <a href="reader.php?id=<?= $book['id'] ?>" class="btn btn-primary" style="width: 100%;">
                            📖 Baca Sekarang
                        </a>
                    <?php elseif ($book['is_free']): ?>
                        <a href="reader.php?id=<?= $book['id'] ?>" class="btn btn-primary" style="width: 100%;">
                            📖 Baca Gratis
                        </a>
                    <?php else: ?>
                        <form method="POST">
                            <button type="submit" name="purchase" class="btn btn-primary" style="width: 100%;"
                                    onclick="return confirm('Konfirmasi pembelian buku ini seharga Rp <?= number_format($book['price'], 0, ',', '.') ?>?')">
                                💳 Beli Sekarang
                            </button>
                        </form>
                        <p class="text-center" style="margin-top: 1rem; font-size: 0.85rem; color: var(--text-secondary);">
                            Atau <a href="subscription.php" style="color: var(--primary-color);">berlangganan</a> untuk akses unlimited
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info -->
            <div>
                <div style="margin-bottom: 1rem;">
                    <span class="badge" style="background: var(--hover-bg); color: var(--text-secondary); padding: 0.5rem 1rem;">
                        📂 <?= $book['category'] ?>
                    </span>
                </div>
                
                <h1 style="margin-bottom: 1rem;"><?= $book['title'] ?></h1>
                <p style="font-size: 1.2rem; color: var(--text-secondary); margin-bottom: 2rem;">
                    oleh <span style="color: var(--primary-color); font-weight: 600;"><?= $book['author'] ?></span>
                </p>

                <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">Sinopsis</h3>
                    <p style="color: var(--text-secondary); line-height: 1.8;">
                        <?= nl2br($book['description']) ?>
                    </p>
                </div>

                <div class="card" style="padding: 2rem;">
                    <h3 style="margin-bottom: 1.5rem;">Detail Buku</h3>
                    <div style="display: grid; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.8rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <span style="color: var(--text-secondary);">Kategori</span>
                            <span style="font-weight: 600;"><?= $book['category'] ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.8rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <span style="color: var(--text-secondary);">Penulis</span>
                            <span style="font-weight: 600;"><?= $book['author'] ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.8rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <span style="color: var(--text-secondary);">Status</span>
                            <span style="font-weight: 600;">
                                <?= $book['is_free'] ? '🆓 Gratis' : '💎 Premium' ?>
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.8rem 0;">
                            <span style="color: var(--text-secondary);">Ditambahkan</span>
                            <span style="font-weight: 600;"><?= date('d M Y', strtotime($book['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Books -->
        <?php if ($similar_books->num_rows > 0): ?>
            <div>
                <h2 style="margin-bottom: 2rem;">Buku Serupa 📚</h2>
                <div class="cards-grid">
                    <?php while ($similar = $similar_books->fetch_assoc()): ?>
                        <div class="card book-card" onclick="window.location.href='book_detail.php?id=<?= $similar['id'] ?>'">
                            <img src="../<?= $similar['cover_image'] ?>" alt="<?= $similar['title'] ?>" class="book-cover">
                            <h3 class="book-title"><?= $similar['title'] ?></h3>
                            <p class="book-author">oleh <?= $similar['author'] ?></p>
                            <div class="book-price">
                                <?php if ($similar['is_free']): ?>
                                    <span class="badge badge-free">GRATIS</span>
                                <?php else: ?>
                                    <span class="price-tag">Rp <?= number_format($similar['price'], 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>