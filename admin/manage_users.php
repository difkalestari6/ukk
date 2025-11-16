<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('/index.php');
}

$success = '';
$error = '';

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM users WHERE id=$id AND role='user'")) {
        $success = "User berhasil dihapus!";
    } else {
        $error = "Gagal menghapus user!";
    }
}

// Get All Users
$users = mysqli_query($conn, "SELECT u.*, 
    (SELECT COUNT(*) FROM subscriptions WHERE user_id = u.id AND status = 'active') as active_subs,
    (SELECT COUNT(*) FROM favorites WHERE user_id = u.id) as total_favorites
    FROM users u WHERE role = 'user' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f3f4f6; }
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
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="text-white text-center mb-4">
            <i class="bi bi-shield-check" style="font-size: 3rem;"></i>
            <h4 class="mt-2">Admin Panel</h4>
        </div>
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="manage_books.php"><i class="bi bi-book"></i> Kelola Buku</a>
        <a href="manage_users.php" class="active"><i class="bi bi-people"></i> Kelola User</a>
        <a href="manage_subscriptions.php"><i class="bi bi-gem"></i> Kelola Langganan</a>
        <hr style="border-color: rgba(255,255,255,0.3);">
        <a href="../index.php"><i class="bi bi-house"></i> Ke Halaman Utama</a>
        <a href="../pages/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <div class="main-content">
        <h2 class="mb-4"><i class="bi bi-people"></i> Kelola User</h2>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Langganan Aktif</th>
                                <th>Total Favorit</th>
                                <th>Bergabung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <i class="bi bi-person-circle"></i> 
                                    <?= htmlspecialchars($user['username']) ?>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php if($user['active_subs'] > 0): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Ya
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-x-circle"></i> Tidak
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= $user['total_favorites'] ?> buku
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewDetail(<?= htmlspecialchars(json_encode($user)) ?>)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Hapus user ini? Data favorit dan langganan juga akan terhapus!')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- User Detail Modal -->
    <div class="modal fade" id="userDetailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDetailContent">
                    <!-- Content will be inserted by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewDetail(user) {
            const content = `
                <div class="text-center mb-3">
                    <i class="bi bi-person-circle" style="font-size: 4rem; color: #6366f1;"></i>
                </div>
                <table class="table">
                    <tr>
                        <th>Username:</th>
                        <td>${user.username}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>${user.email}</td>
                    </tr>
                    <tr>
                        <th>User ID:</th>
                        <td>#${user.id}</td>
                    </tr>
                    <tr>
                        <th>Bergabung Sejak:</th>
                        <td>${new Date(user.created_at).toLocaleDateString('id-ID', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}</td>
                    </tr>
                    <tr>
                        <th>Langganan Aktif:</th>
                        <td>${user.active_subs > 0 ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>'}</td>
                    </tr>
                    <tr>
                        <th>Total Buku Favorit:</th>
                        <td><span class="badge bg-primary">${user.total_favorites} buku</span></td>
                    </tr>
                </table>
            `;
            document.getElementById('userDetailContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('userDetailModal')).show();
        }
    </script>
</body>
</html>