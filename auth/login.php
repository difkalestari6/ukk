<?php
session_start();
require_once '../config/database.php';

// Redirect jika sudah login
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
    <title>Login - Galactic Library</title>
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
        <h2 class="text-center">Selamat Datang Kembali</h2>
        <p class="text-center" style="margin-bottom: 2rem; color: var(--text-secondary);">
            Login untuk melanjutkan petualangan membaca Anda
        </p>

        <?php if (isset($_GET['error'])): ?>
            <div style="background: rgba(255, 71, 87, 0.1); color: var(--danger-color); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <?php
                if ($_GET['error'] == 'invalid') {
                    echo "❌ Username atau password salah!";
                } elseif ($_GET['error'] == 'empty') {
                    echo "❌ Semua field harus diisi!";
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div style="background: rgba(0, 245, 160, 0.1); color: var(--success-color); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                ✅ Registrasi berhasil! Silakan login.
            </div>
        <?php endif; ?>

        <form action="process_login.php" method="POST">
            <div class="form-group">
                <label>Username atau Email</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                🔐 Login
            </button>
        </form>

        <div class="text-center mt-2">
            <p style="color: var(--text-secondary);">
                Belum punya akun? 
                <a href="register.php" style="color: var(--primary-color); font-weight: 600;">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>