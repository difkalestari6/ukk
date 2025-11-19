<?php
require_once '../config/database.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
        }
        .box h2 {
            text-align: center;
            color: #0a1d37;
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
            background: #0a1d37;
            color: white;
            padding: 10px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #10294f;
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

    <h2>Login</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">
            <?= $_GET['error'] === "empty" ? "Semua field wajib diisi!" : "Username atau password salah!" ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="success">
            Berhasil daftar! Silakan login.
        </div>
    <?php endif; ?>

    <form method="POST" action="process_login.php">

        <div class="input-group">
            <label>Username / Email</label>
            <input type="text" name="username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button class="btn" type="submit">Login</button>

    </form>

</div>

</body>
</html>
