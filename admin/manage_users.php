<?php
session_start();
require_once '../config/database.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../auth/login.php');
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    if ($user_id != $_SESSION['user_id']) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
}

// Get all users
$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM purchases WHERE user_id = u.id) as total_purchases,
          (SELECT COUNT(*) FROM subscriptions WHERE user_id = u.id AND status = 'active') as active_subs
          FROM users u ORDER BY created_at DESC";
$users = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo" style="margin-bottom: 2rem;">
                <span>📚</span> Admin Panel
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php">📊 Dashboard</a></li>
                <li><a href="manage_books.php">📚 Kelola Buku</a></li>
                <li><a href="manage_users.php" class="active">👥 Kelola User</a></li>
                <li><a href="statistics.php">📈 Statistik</a></li>
                <li><a href="../index.php">🏠 Ke Beranda</a></li>
                <li><a href="../auth/logout.php" style="color: var(--danger-color);">🚪 Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1>Kelola User 👥</h1>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Pembelian</th>
                            <th>Langganan</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= $user['full_name'] ?></td>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td>
                                    <?php if ($user['role'] == 'admin'): ?>
                                        <span class="badge" style="background: var(--danger-color);">ADMIN</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: var(--text-secondary);">USER</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $user['total_purchases'] ?></td>
                                <td><?= $user['active_subs'] ?></td>
                                <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?= $user['id'] ?>" 
                                           onclick="return confirm('Yakin hapus user ini?')"
                                           class="btn btn-danger" style="padding: 0.5rem 1rem;">
                                            Hapus
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary);">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>