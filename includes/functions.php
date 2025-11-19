<?php
/**
 * Helper Functions untuk Galactic Library
 */

/**
 * Format harga ke Rupiah
 */
function format_rupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format tanggal ke format Indonesia
 */
function format_tanggal($date) {
    $bulan = [
        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
    ];
    
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $bulan[(int)date('m', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "$day $month $year";
}

/**
 * Waktu yang lalu (time ago)
 */
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'Baru saja';
    if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
    if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
    if ($diff < 604800) return floor($diff / 86400) . ' hari lalu';
    
    return format_tanggal($datetime);
}

/**
 * Truncate text
 */
function truncate($text, $length = 100) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

/**
 * Upload file gambar
 */
function upload_image($file, $target_dir = '../assets/images/') {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed)) {
        return ['success' => false, 'message' => 'Format file tidak didukung'];
    }
    
    $new_filename = time() . '_' . uniqid() . '.' . $file_ext;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => 'assets/images/' . $new_filename];
    }
    
    return ['success' => false, 'message' => 'Gagal upload file'];
}

/**
 * Upload file PDF
 */
function upload_pdf($file, $target_dir = '../assets/books/') {
    $filename = $file['name'];
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if ($file_ext !== 'pdf') {
        return ['success' => false, 'message' => 'Hanya file PDF yang diizinkan'];
    }
    
    $new_filename = time() . '_' . uniqid() . '.pdf';
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => 'assets/books/' . $new_filename];
    }
    
    return ['success' => false, 'message' => 'Gagal upload file'];
}

/**
 * Generate random string
 */
function random_string($length = 10) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $result;
}

/**
 * Redirect dengan pesan
 */
function redirect($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

/**
 * Tampilkan flash message
 */
function flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        $color = $type == 'success' ? 'var(--success-color)' : 'var(--danger-color)';
        
        echo "<div class='alert' style='background: rgba(" . ($type == 'success' ? '0, 245, 160' : '255, 71, 87') . ", 0.1); color: $color; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;'>$message</div>";
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

/**
 * Hitung sisa hari
 */
function days_remaining($end_date) {
    $now = time();
    $end = strtotime($end_date);
    $diff = $end - $now;
    return max(0, floor($diff / 86400));
}

/**
 * Cek langganan aktif
 */
function has_active_subscription($user_id) {
    global $conn;
    $query = "SELECT * FROM subscriptions WHERE user_id = ? AND status = 'active' AND end_date > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

/**
 * Get user IP
 */
function get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'];
}

/**
 * Validasi email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}
?>