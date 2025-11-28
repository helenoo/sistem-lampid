<?php
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set session
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] == 'desa') {
            $_SESSION['id_desa'] = $user['id_desa'];
        }

        // Notifikasi sederhana via session
        $_SESSION['notification'] = "Login berhasil! Selamat datang, " . htmlspecialchars($user['username']);
        $_SESSION['notification_type'] = "success";

        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: desa/dashboard.php");
        }
        exit();
    } else {
        // PERBAIKAN DISINI:
        // Redirect kembali ke login.php (bukan index.php) agar notifikasi error muncul di form
        header("Location: login.php?error=Username atau password salah!");
        exit();
    }
}
?>