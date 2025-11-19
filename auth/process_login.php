// difkalestari6/ukk/ukk-a74c6983610426cfeaf45330d1b2cb6328d7fd90/auth/process_login.php
<?php
session_start();
require_once '../config/database.php'; // Pastikan ini ada

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$username = isset($_POST['username']) ? clean_input($_POST['username']) : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";

if (empty($username) || empty($password)) {
    header("Location: login.php?error=empty");
    exit;
}

$sql = "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: login.php?error=invalid");
    exit;
}

$user = $res->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    header("Location: login.php?error=invalid");
    exit;
}

// Set session
$_SESSION['user'] = [
    'id'        => $user['id'],
    'username'  => $user['username'],
    'email'     => $user['email'],
    'full_name' => $user['full_name'],
    'role'      => $user['role']
];

// Redirect sesuai role
if ($user['role'] === "admin") {
    // Admin yang login dari link user, tetap diarahkan ke dashboard admin
    header("Location: ../admin/index.php");
} else {
    // User biasa diarahkan ke dashboard user
    header("Location: ../user/dashboard.php");
}
exit;