<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Query database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Debugging
        // var_dump($user); exit();

        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect berdasarkan role
            if ($_SESSION['user_role'] === 'admin') {
                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: indexx.php");
                exit();
            }
        }
    }

    $_SESSION['error'] = 'Email atau password salah';
    header("Location: login.php");
    exit();
}

header("Location: login.php");
exit();
