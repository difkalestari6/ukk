<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_login.php");
    exit;
}

$username = isset($_POST['username']) ? clean_input($_POST['username']) : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";

if (empty($username) || empty($password)) {
    header("Location: admin_login.php?error=empty");
    exit;
}

// Kueri yang secara eksplisit mencari user DENGAN role 'admin'
$sql = "SELECT id, username, email, password, full_name, role FROM users 
        WHERE (username = ? OR email = ?) AND role = 'admin' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$res = $stmt->get_result();

// Cek apakah user admin ditemukan
if ($res->num_rows === 0) {
    // Pesan ini muncul jika user tidak ditemukan ATAU jika user ditemukan tapi role-nya BUKAN admin
    header("Location: admin_login.php?error=invalid");
    exit;
}

$user = $res->fetch_assoc();

// Verifikasi password (menggunakan hash di database)
if (!password_verify($password, $user['password'])) {
    // Jika hash tidak cocok dengan password yang diinput
    header("Location: admin_login.php?error=invalid");
    exit;
}

// LOGIN BERHASIL: Set session
$_SESSION['user'] = [
    'id'        => $user['id'],
    'username'  => $user['username'],
    'email'     => $user['email'],
    'full_name' => $user['full_name'],
    'role'      => $user['role']
];
// Untuk kompatibilitas dengan file lama
$_SESSION['user_id'] = $user['id']; 

// Redirect ke dashboard admin
header("Location: ../admin/index.php");
exit;