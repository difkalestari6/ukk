// difkalestari6/ukk/ukk-a74c6983610426cfeaf45330d1b2cb6328d7fd90/auth/register.php
<?php
require_once '../config/database.php'; // Include database.php untuk mendapatkan fungsi is_logged_in() dan is_admin()

// Perbaikan Error: Menggunakan fungsi helper yang benar untuk cek sesi
if (is_logged_in()) {
    if (is_admin()) {
        header('Location: ../admin/index.php');
    } else {
        header('Location: ../user/dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Galactic Library</title>
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
        </div>
    </nav>

    <div class="form-container">
        <h2 class="text-center">Bergabunglah dengan Kami</h2>
        <p class="text-center" style="margin-bottom: 2rem; color: var(--text-secondary);">
            Buat akun untuk memulai petualangan membaca Anda
        </p>

        <?php if (isset($_GET['error'])): ?>
            <div style="background: rgba(255, 71, 87, 0.1); color: var(--danger-color); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <?php
                if ($_GET['error'] == 'username_exists') {
                    echo "❌ Username sudah digunakan!";
                } elseif ($_GET['error'] == 'email_exists') {
                    echo "❌ Email sudah terdaftar!";
                } elseif ($_GET['error'] == 'password_mismatch') {
                    echo "❌ Password tidak cocok!";
                } elseif ($_GET['error'] == 'empty') {
                    echo "❌ Semua field harus diisi!";
                }
                ?>
            </div>
        <?php endif; ?>

        <form action="process_register.php" method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required minlength="3">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                ✨ Daftar Sekarang
            </button>
        </form>

        <div class="text-center mt-2">
            <p style="color: var(--text-secondary);">
                Sudah punya akun? 
                <a href="login.php" style="color: var(--primary-color); font-weight: 600;">
                    Login
                </a>
            </p>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>