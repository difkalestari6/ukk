<?php
session_start();
require_once '../config/database.php';

// Get filter parameters
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$category = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$filter = isset($_GET['filter']) ? clean_input($_GET['filter']) : 'all';

// Build query
$query = "SELECT * FROM books WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (title LIKE '%$search%' OR author LIKE '%$search%')";
}

if (!empty($category)) {
    $query .= " AND category = '$category'";
}

if ($filter == 'free') {
    $query .= " AND is_free = 1";
} elseif ($filter == 'premium') {
    $query .= " AND is_free = 0";
}

$query .= " ORDER BY created_at DESC";
$books = $conn->query($query);

// Get categories
$categories_query = "SELECT DISTINCT category FROM books ORDER BY category";
$categories = $conn->query($categories_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - Galactic Library</title>
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
                    <?php if (is_logged_in()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="../auth/logout.php" class="btn btn-danger">Logout</a></li>
                    <?php else: ?>
                        <li><a href="../auth/login.php" class="btn btn-secondary">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </nav>

    <div class="container" style="padding: 3rem 2rem;">
        <h1 class="text-center">Jelajahi Katalog Buku 📚</h1>
        <p class="text-center" style="color: var(--text-secondary); margin-bottom: 3rem;">
            Temukan buku favorit Anda dari ribuan koleksi kami
        </p>

        <!-- Filter & Search -->
        <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
            <form method="GET" action="">
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Cari Buku</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Judul atau penulis..." value="<?= $search ?>">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Kategori</label>
                        <select name="category" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?= $cat['category'] ?>" <?= $category == $cat['category'] ? 'selected' : '' ?>>
                                    <?= $cat['category'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Filter</label>
                        <select name="filter" class="form-control">
                            <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Semua</option>
                            <option value="free" <?= $filter == 'free' ? 'selected' : '' ?>>Gratis</option>
                            <option value="premium" <?= $filter == 'premium' ? 'selected' : '' ?>>Premium</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">🔍 Cari</button>
                </div>
            </form>
        </div>

        <!-- Books Grid -->
        <?php if ($books->num_rows > 0): ?>
            <div class="cards-grid">
                <?php while ($book = $books->fetch_assoc()): ?>
                    <div class="card book-card" onclick="window.location.href='book_detail.php?id=<?= $book['id'] ?>'">
                        <img src="../<?= $book['cover_image'] ?>" alt="<?= $book['title'] ?>" class="book-cover">
                        <h3 class="book-title"><?= $book['title'] ?></h3>
                        <p class="book-author">oleh <?= $book['author'] ?></p>
                        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.5rem;">
                            📂 <?= $book['category'] ?>
                        </p>
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
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 4rem;">
                <p style="font-size: 3rem; margin-bottom: 1rem;">📚</p>
                <h3>Tidak Ada Buku Ditemukan</h3>
                <p style="color: var(--text-secondary); margin-top: 1rem;">
                    Coba ubah filter atau kata kunci pencarian Anda
                </p>
                <a href="catalog.php" class="btn btn-primary" style="margin-top: 1rem;">Reset Filter</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>