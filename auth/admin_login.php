// difkalestari6/ukk/ukk-a74c6983610426cfeaf45330d1b2cb6328d7fd90/auth/admin_login.php
<?php
require_once '../config/database.php';

// Cek jika sudah login, redirect sesuai role
if (is_logged_in()) {
    if (is_admin()) {
        header('Location: ../admin/index.php');
    } else {
        // Jika user biasa mencoba akses admin login, kembalikan ke user dashboard
        header('Location: ../user/dashboard.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial;
            background: #0a1d37;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .box {
            width: 350px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(255,255,255,0.1);
            border-top: 5px solid #FF4757; /* Pembeda visual Admin */
        }
        .box h2 {
            text-align: center;
            color: #FF4757; 
            margin-bottom: 15px;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            font-size: 14px;
            color: #0a1d37;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-top: 5px;
        }
        .btn {
            width: 100%;
            background: #FF4757; /* Warna Admin */
            color: white;
            padding: 10px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #e63745;
        }
        .error {
            background: #ff6161;
            padding: 10px;
            border-radius: 8px;
            color: white;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .success {
            background: #19d18c;
            padding: 10px;
            border-radius: 8px;
            color: white;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="box">

    <h2>Admin Login</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">
            <?= $_GET['error'] === "empty" ? "Semua field wajib diisi!" : "Username atau password salah!" ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="process_admin_login.php">

        <div class="input-group">
            <label>Username / Email</label>
            <input type="text" name="username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button class="btn" type="submit">Admin Login</button>

    </form>
    
    <p style="text-align: center; margin-top: 15px; font-size: 12px;">
        Bukan Admin? <a href="login.php" style="color: #FF4757;">Login User</a>
    </p>

</div>

</body>
</html>