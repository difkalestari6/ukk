<?php
/**
 * KONEKSI.PHP - Alternative Database Connection File
 * Bisa digunakan sebagai backup dari config.php
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'perpustakaan_online';

// Create Connection
$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check Connection
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($koneksi, "utf8");

// Optional: Set timezone
date_default_timezone_set('Asia/Jakarta');

/**
 * Helper function untuk query
 */
function query($query) {
    global $koneksi;
    $result = mysqli_query($koneksi, $query);
    
    if (!$result) {
        die("Query Error: " . mysqli_error($koneksi));
    }
    
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    
    return $rows;
}

/**
 * Helper function untuk escape string
 */
function escape($string) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, $string);
}

/**
 * Check if user is logged in
 */
function isLogin() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdminRole() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirect helper
 */
function redirectTo($url) {
    header("Location: $url");
    exit();
}

/**
 * Get current user data
 */
function getCurrentUser() {
    global $koneksi;
    
    if (!isLogin()) {
        return null;
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($koneksi, $query);
    
    return mysqli_fetch_assoc($result);
}

/**
 * Flash message helper
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>