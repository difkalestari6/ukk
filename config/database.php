<?php
// =========================
// CONFIG / KONEKSI DATABASE
// =========================
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "digital_library";

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// =========================
// SESSION
// =========================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================
// SANITASI / ESCAPE
// =========================
/**
 * Escape untuk query (simple)
 */
function escape($value) {
    global $conn;
    return mysqli_real_escape_string($conn, $value);
}

/**
 * Sanitasi output / input sederhana untuk form
 */
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// =========================
// AUTHENTICATION HELPERS
// =========================
/**
 * Cek apakah user sudah login
 */
function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Return current user array (atau null)
 */
function current_user() {
    return is_logged_in() ? $_SESSION['user'] : null;
}

/**
 * Cek apakah user login dan admin
 */
function is_admin() {
    $u = current_user();
    return $u && isset($u['role']) && $u['role'] === 'admin';
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Proses login: menerima username/email dan password (plain)
 * Mengembalikan true jika sukses, false jika gagal
 */
function login_user($identifier, $password) {
    global $conn;

    // identifier bisa username atau email
    $id_esc = escape($identifier);

    $sql = "SELECT id, username, email, password, full_name, role FROM users
            WHERE username = '$id_esc' OR email = '$id_esc' LIMIT 1";
    $res = mysqli_query($conn, $sql);

    if (!$res || mysqli_num_rows($res) === 0) {
        return false;
    }

    $row = mysqli_fetch_assoc($res);

    // pastikan password disimpan hashed (password_hash)
    if (password_verify($password, $row['password'])) {
        // set session user (jangan simpan password)
        $_SESSION['user'] = [
            'id' => (int)$row['id'],
            'username' => $row['username'],
            'email' => $row['email'],
            'full_name' => $row['full_name'],
            'role' => $row['role']
        ];
        return true;
    }

    return false;
}

/**
 * Logout
 */
function logout_user() {
    // hapus session user saja
    if (isset($_SESSION['user'])) {
        unset($_SESSION['user']);
    }
    // optional: destroy full session
    // session_destroy();
}

/**
 * Helper untuk memaksa login pada halaman
 */
function require_login($redirect = '/auth/login.php') {
    if (!is_logged_in()) {
        redirect($redirect);
    }
}

/**
 * Helper untuk memaksa role admin
 */
function require_admin($redirect = '/index.php') {
    if (!is_logged_in() || !is_admin()) {
        redirect($redirect);
    }
}

// =========================
// BOOK & ACCESS HELPERS
// =========================
/**
 * Ambil data buku by id, mengembalikan assoc array atau null
 */
function get_book_by_id($book_id) {
    global $conn;
    $id = (int)$book_id;
    $sql = "SELECT * FROM books WHERE id = $id LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        return mysqli_fetch_assoc($res);
    }
    return null;
}

/**
 * Cek apakah user boleh mengakses buku:
 * - jika buku free
 * - atau user punya langganan aktif
 * - atau user sudah membeli buku tersebut
 *
 * Parameter $book bisa array hasil get_book_by_id atau id (fungsi akan handle)
 */
function can_access_book($user_id, $book) {
    // terima $book berupa id atau array
    if (!is_array($book)) {
        $book = get_book_by_id($book);
    }
    if (!$book) return false;

    // buku gratis
    if ((int)$book['is_free'] === 1) {
        return true;
    }

    // langganan aktif
    if (has_active_subscription($user_id)) {
        return true;
    }

    // sudah beli
    return has_purchased_book($user_id, $book['id']);
}

/**
 * Cek langganan aktif user
 */
function has_active_subscription($user_id) {
    global $conn;
    $uid = (int)$user_id;
    $now = date("Y-m-d H:i:s");

    $sql = "SELECT id FROM subscriptions
            WHERE user_id = $uid
            AND status = 'active'
            AND end_date >= '$now'
            LIMIT 1";
    $res = mysqli_query($conn, $sql);
    return ($res && mysqli_num_rows($res) > 0);
}

/**
 * Cek apakah user sudah membeli buku
 */
function has_purchased_book($user_id, $book_id) {
    global $conn;
    $uid = (int)$user_id;
    $bid = (int)$book_id;

    $sql = "SELECT id FROM purchases
            WHERE user_id = $uid AND book_id = $bid
            LIMIT 1";
    $res = mysqli_query($conn, $sql);
    return ($res && mysqli_num_rows($res) > 0);
}
