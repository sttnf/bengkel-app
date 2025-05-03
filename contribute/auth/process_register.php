<?php
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Setelah koneksi database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = 'user';

    // Validasi password (minimal 8 karakter)
    if (strlen($password) < 8) {
        header("Location: register.php?error=Password minimal 8 karakter");
        exit();
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah email sudah terdaftar
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        header("Location: register.php?error=Email sudah terdaftar");
        exit();
    }

    // Insert user baru ke database
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password_hash, $role);

    if ($stmt->execute()) {
        header("Location: login.php?success=Registrasi berhasil! Silakan login.");
        exit();
    } else {
        header("Location: register.php?error=Registrasi gagal. Silakan coba lagi.");
        exit();
    }
}
