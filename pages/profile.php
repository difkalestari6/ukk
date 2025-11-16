<?php
require_once '../config.php';

if (!isLoggedIn()) {
    redirect('/pages/login.php');
}

$user_id = $_SESSION['user_id'];

// Get User Info
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get Subscription Info
$subscription = getUserSubscription($conn, $user_id);

// Get Favorites
$favorites_query = "SELECT b.*, f.added_at FROM favorites f 
                    JOIN books b ON f.book_id = b.id 
                    WHERE f.user_id = $user_id 
                    ORDER BY f.added_at DESC";
$favorites_result = mysqli_query($conn, $favorites_query);

// Handle Remove Favorite
if (isset($_POST['remove_favorite'])) {
    $book_id = (int)$_POST['book_id'];
    mysqli_query($conn, "DELETE FROM favorites WHERE user_id = $user_id AND book_id = $book_id");
    redirect('/pages/profile.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Perpustakaan Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: 100px;
        }
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }
        .avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            margin: 0 auto 20px;
        }
        .book-card {
            transition: transform 0.3s;
        }
        .book-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container" style="margin-bottom: 50px;">
        <div class="row">
            <div class="col-md-4">
                <div class="profile-card text-center">
                    <div class="avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h3><?= htmlspecialchars($user['username']) ?></h3>
                    <p class="text-muted mb-3"><?= htmlspecialchars($user['email']) ?></p>
                    <span class="badge bg-primary"><?= ucfirst($user['role']) ?></span>
                    
                    <hr class="my-4">
                    
                    <div class="text-start">
                        <h5><i class="bi bi-calendar"></i> Bergabung Sejak</h5>
                        <p class="text-muted"><?= date('d F Y', strtotime($user['created_at'])) ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Subscription Status -->
                <div class="profile-card">
                    <h4><i class="bi bi-gem"></i> Status Langganan</h4>
                    <hr>
                    
                    <?php if ($subscription): ?>
                        <div class="alert alert-success">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1"><i class="bi bi-star-fill"></i> Paket <?= $subscription['plan_type'] ?> Aktif</h5>
                                    <p class="mb-0"><strong>Berlaku hingga:</strong> <?= date('d F Y H:i', strtotime($subscription['end_date'])) ?></p>
                                </div>
                                <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #10b981;"></i>
                            </div>
                            
                            <?php
                            $remaining_hours = (strtotime($subscription['end_date']) - time()) / 3600;
                            if ($remaining_hours < 24 && $remaining_hours > 0):
                            ?>
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    <strong>Perhatian!</strong> Langganan Anda akan berakhir dalam <?= round($remaining_hours) ?> jam.
                                    <a href="subscription.php" class="alert-link">Perpanjang sekarang</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Anda belum memiliki langganan aktif.
                            <a href="subscription.php" class="alert-link">Berlangganan sekarang</a> untuk membaca semua buku premium!
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Favorite Books -->
                <div class="profile-card">
                    <h4><i class="bi bi-heart-fill"></i> Buku Favorit</h4>
                    <hr>
                    
                    <?php if (mysqli_num_rows($favorites_result) > 0): ?>
                        <div class="row g-3">
                            <?php while($book = mysqli_fetch_assoc($favorites_result)): ?>
                                <div class="col-md-6">
                                    <div class="card book-card">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= htmlspecialchars($book['title']) ?></h6>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-person"></i> <?= htmlspecialchars($book['author']) ?>
                                            </p>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-calendar"></i> Ditambahkan: <?= date('d M Y', strtotime($book['added_at'])) ?>
                                            </p>
                                            
                                            <div class="d-flex gap-2">
                                                <a href="read_book.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary flex-grow-1">
                                                    <i class="bi bi-book"></i> Baca
                                                </a>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                                    <button type="submit" name="remove_favorite" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Hapus dari favorit?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-heart" style="font-size: 4rem;"></i>
                            <p class="mt-3">Belum ada buku favorit</p>
                            <a href="../index.php" class="btn btn-primary">
                                <i class="bi bi-search"></i> Jelajahi Buku
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>