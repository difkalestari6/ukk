<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = clean_input($_POST['full_name']);
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input kosong
    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        header('Location: register.php?error=empty');
        exit();
    }
    
    // Cek password match
    if ($password !== $confirm_password) {
        header('Location: register.php?error=password_mismatch');
        exit();
    }
    
    // Cek username sudah ada
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        header('Location: register.php?error=username_exists');
        exit();
    }
    
    // Cek email sudah ada
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        header('Location: register.php?error=email_exists');
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru
    $query = "INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'user')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $full_name);
    
    if ($stmt->execute()) {
        header('Location: login.php?success=registered');
        exit();
    } else {
        header('Location: register.php?error=database');
        exit();
    }
} else {
    header('Location: register.php');
    exit();
}
?>