<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "perpustakaan_online");

// Cek koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fungsi redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Fungsi cek admin
function isAdmin() {
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    return $_SESSION['user']['role'] === 'admin';
}