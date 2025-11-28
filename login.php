<?php
require_once 'includes/db_connect.php';

// Jika user SUDAH login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] == 'admin' ? 'admin/dashboard.php' : 'desa/dashboard.php'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SISTEM LAMPID CIBUNGBULANG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body-wrapper">

    <div class="login-container standalone">
        <a href="index.php" class="back-to-home"><i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama</a>
        <div class="login-header">
            <img src="assets/img/logo_cibungbulang.png" alt="Logo Kecamatan Cibungbulang" style="width:70px; margin-bottom: 15px;">
            <h2>Login Akun</h2>
            <p>Silakan masuk untuk melanjutkan.</p>
        </div>
        <form action="login_process.php" method="POST" class="login-form">
            <?php if(isset($_GET['error'])): ?>
                <div class="notification error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <div class="form-group input-with-icon">
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" required placeholder="Username">
            </div>
            <div class="form-group input-with-icon">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" required placeholder="Password">
            </div>
            <button type="submit" class="btn">Masuk</button>
            
            </form>
    </div>

</body>
</html>