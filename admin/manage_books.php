<?php
session_start();
require_once '../config/database.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../auth/login.php');
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $book_id = (int)$_GET['delete'];
    $query = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    
    if ($stmt->execute()) {
        $success = "Buku berhasil dihapus!";
    } else {
        $error = "Gagal menghapus buku!";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = clean_input($_POST['title']);
    $author = clean_input($_POST['author']);
    $description = clean_input($_POST['description']);
    $category = clean_input($_POST['category']);
    $is_free = isset($_POST['is_free']) ? 1 : 0;
    $price = $is_free ? 0 : floatval($_POST['price']);
    
    // Handle file upload
    $cover_image = 'assets/images/default-cover.jpg';
    $pdf_file = '';
    
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $target_dir = "../assets/images/";
        $cover_image = "assets/images/" . time() . "_" . basename($_FILES['cover_image']['name']);
        move_uploaded_file($_FILES['cover_image']['tmp_name'], "../" . $cover_image);
    }
    
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $target_dir = "../assets/books/";
        $pdf_file = "assets/books/" . time() . "_" . basename($_FILES['pdf_file']['name']);
        move_uploaded_file($_FILES['pdf_file']['tmp_name'], "../" . $pdf_file);
    }
    
    if (isset($_POST['book_id']) && !empty($_POST['book_id'])) {
        // Update
        $book_id = (int)$_POST['book_id'];
        
        if (!empty($cover_image) && !empty($pdf_file)) {
            $query = "UPDATE books SET title=?, author=?, description=?, category=?, 
                      is_free=?, price=?, cover_image=?, pdf_file=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssisssi", $title, $author, $description, $category, 
                              $is_free, $price, $cover_image, $pdf_file, $book_id);
        } else {
            $query = "UPDATE books SET title=?, author=?, description=?, category=?, 
                      is_free=?, price=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssisdi", $title, $author, $description, $category, 
                              $is_free, $price, $book_id);
        }
        
        if ($stmt->execute()) {
            $success = "Buku berhasil diupdate!";
        }
    } else {
        // Insert
        $query = "INSERT INTO books (title, author, description, category, is_free, price, cover_image, pdf_file) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssidss", $title, $author, $description, $category, 
                          $is_free, $price, $cover_image, $pdf_file);
        
        if ($stmt->execute()) {
            $success = "Buku berhasil ditambahkan!";
        }
    }
}

// Get book for edit
$edit_book = null;
if (isset($_GET['edit'])) {
    $book_id = (int)$_GET['edit'];
    $query = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $edit_book = $stmt->get_result()->fetch_assoc();
}

// Get all books
$query = "SELECT * FROM books ORDER BY created_at DESC";
$books = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Admin</title>
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
                <li><a href="index.php">📊 Dashboard</a></li>
                <li><a href="manage_books.php" class="active">📚 Kelola Buku</a></li>
                <li><a href="manage_users.php">👥 Kelola User</a></li>
                <li><a href="statistics.php">📈 Statistik</a></li>
                <li><a href="../index.php">🏠 Ke Beranda</a></li>
                <li><a href="../auth/logout.php" style="color: var(--danger-color);">🚪 Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h1>Kelola Buku 📚</h1>
            
            <?php if (isset($success)): ?>
                <div class="alert" style="background: rgba(0, 245, 160, 0.1); color: var(--success-color); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <?= $success ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert" style="background: rgba(255, 71, 87, 0.1); color: var(--danger-color); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Form Add/Edit Book -->
            <div class="card" style="margin-bottom: 2rem; padding: 2rem;">
                <h2><?= $edit_book ? 'Edit Buku' : 'Tambah Buku Baru' ?></h2>
                <form method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
                    <?php if ($edit_book): ?>
                        <input type="hidden" name="book_id" value="<?= $edit_book['id'] ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Judul Buku *</label>
                            <input type="text" name="title" class="form-control" 
                                   value="<?= $edit_book['title'] ?? '' ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Penulis *</label>
                            <input type="text" name="author" class="form-control" 
                                   value="<?= $edit_book['author'] ?? '' ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi *</label>
                        <textarea name="description" class="form-control" rows="4" required><?= $edit_book['description'] ?? '' ?></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Kategori *</label>
                            <input type="text" name="category" class="form-control" 
                                   value="<?= $edit_book['category'] ?? '' ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" 
                                   value="<?= $edit_book['price'] ?? 0 ?>" id="price">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="is_free" id="is_free" 
                                   <?= ($edit_book && $edit_book['is_free']) ? 'checked' : '' ?>>
                            Buku Gratis
                        </label>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Cover Image</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                        </div>
                        
                        <div class="form-group">
                            <label>File PDF</label>
                            <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <?= $edit_book ? 'Update Buku' : 'Tambah Buku' ?>
                        </button>
                        <?php if ($edit_book): ?>
                            <a href="manage_books.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Books List -->
            <h2>Daftar Buku</h2>
            <div class="table-container" style="margin-top: 1.5rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($book = $books->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="../<?= $book['cover_image'] ?>" alt="<?= $book['title'] ?>" 
                                         style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td><?= $book['title'] ?></td>
                                <td><?= $book['author'] ?></td>
                                <td><?= $book['category'] ?></td>
                                <td>
                                    <?php if ($book['is_free']): ?>
                                        <span class="badge badge-free">GRATIS</span>
                                    <?php else: ?>
                                        <span class="badge badge-premium">PREMIUM</span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: var(--accent-color); font-weight: 600;">
                                    Rp <?= number_format($book['price'], 0, ',', '.') ?>
                                </td>
                                <td>
                                    <a href="?edit=<?= $book['id'] ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; margin-right: 0.5rem;">
                                        Edit
                                    </a>
                                    <a href="?delete=<?= $book['id'] ?>" 
                                       onclick="return confirm('Yakin ingin menghapus buku ini?')"
                                       class="btn btn-danger" style="padding: 0.5rem 1rem;">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Toggle price field based on is_free checkbox
        document.getElementById('is_free').addEventListener('change', function() {
            const priceField = document.getElementById('price');
            if (this.checked) {
                priceField.value = 0;
                priceField.disabled = true;
            } else {
                priceField.disabled = false;
            }
        });
    </script>
</body>
</html>