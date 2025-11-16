<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('/index.php');
}

// Get Statistics
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'"))['count'];
$total_books = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM books"))['count'];
$total_subscriptions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM subscriptions WHERE status = 'active'"))['count'];
$total_favorites = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM favorites"))['count'];

// Get Recent Books
$recent_books = mysqli_query($conn, "SELECT * FROM books ORDER BY created_at DESC LIMIT 5");

// Get Recent Users
$recent_users = mysqli_query($conn, "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Perpustakaan Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: #f3f4f6;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            position: fixed;
            width: 250px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 10px;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.2);
        }
        .main-content {
            margin-left: 270px;
            padding: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="text-white text-center mb-4">
            <i class="bi bi-shield-check" style="font-size: 3rem;"></i>
            <h4 class="mt-2">Admin Panel</h4>
        </div>
        <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="manage_books.php"><i class="bi bi-book"></i> Kelola Buku</a>
        <a href="manage_users.php"><i class="bi bi-people"></i> Kelola User</a>
        <a href="manage_subscriptions.php"><i class="bi bi-gem"></i> Kelola Langganan</a>
        <hr style="border-color: rgba(255,255,255,0.3);">
        <a href="../index.php"><i class="bi bi-house"></i> Ke Halaman Utama</a>
        <a href="../pages/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <div class="main-content">
        <h2 class="mb-4">Dashboard</h2>

        <!-- Statistics Cards -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total User</p>
                            <h3 class="mb-0"><?= $total_users ?></h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Buku</p>
                            <h3 class="mb-0"><?= $total_books ?></h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-book-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Langganan Aktif</p>
                            <h3 class="mb-0"><?= $total_subscriptions ?></h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-gem"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Favorit</p>
                            <h3 class="mb-0"><?= $total_favorites ?></h3>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Books and Users -->
        <div class="row g-4 mt-3">
            <div class="col-md-6">
                <div class="stat-card">
                    <h5 class="mb-3"><i class="bi bi-book"></i> Buku Terbaru</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($book = mysqli_fetch_assoc($recent_books)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td>
                                        <?php if($book['is_premium']): ?>
                                            <span class="badge bg-warning">Premium</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Gratis</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="stat-card">
                    <h5 class="mb-3"><i class="bi bi-people"></i> User Terbaru</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Bergabung</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($user = mysqli_fetch_assoc($recent_users)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>