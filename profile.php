<?php
// Path yang benar: langsung ke folder includes
require_once 'includes/db_connect.php';

// Keamanan: Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect ke index utama
    exit();
}

$user_id = $_SESSION['user_id'];

// Proses form jika ada data yang dikirim (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... (Logika proses form tidak berubah, biarkan seperti yang sudah ada)
    $new_username = trim($_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $is_success = false;

    if (empty($new_username)) {
        $_SESSION['notification'] = "Username tidak boleh kosong.";
        $_SESSION['notification_type'] = "error";
    } else {
        $stmt_check = $db->prepare("SELECT id_user FROM users WHERE username = ? AND id_user != ?");
        $stmt_check->execute([$new_username, $user_id]);
        if ($stmt_check->rowCount() > 0) {
            $_SESSION['notification'] = "Username '{$new_username}' sudah digunakan.";
            $_SESSION['notification_type'] = "error";
        } else {
            if (!empty($new_password)) {
                if ($new_password === $confirm_password) {
                    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET username = ?, password = ? WHERE id_user = ?";
                    $stmt_update = $db->prepare($sql);
                    $is_success = $stmt_update->execute([$new_username, $hashedPassword, $user_id]);
                } else {
                    $_SESSION['notification'] = "Konfirmasi password tidak cocok!";
                    $_SESSION['notification_type'] = "error";
                    header("Location: profile.php");
                    exit();
                }
            } else {
                $sql = "UPDATE users SET username = ? WHERE id_user = ?";
                $stmt_update = $db->prepare($sql);
                $is_success = $stmt_update->execute([$new_username, $user_id]);
            }
            if ($is_success) {
                $_SESSION['notification'] = "Profil berhasil diperbarui.";
                $_SESSION['notification_type'] = "success";
                $_SESSION['username'] = $new_username;
            } else {
                $_SESSION['notification'] = "Gagal memperbarui profil.";
                $_SESSION['notification_type'] = "error";
            }
        }
    }
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Profile - SISTEM LAMPID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="main-wrapper">
    <?php 
    // Path yang benar: langsung ke folder includes
    include 'includes/header.php'; 
    ?>

    <div class="content">
        <div class="content-header">
            <h1>Update Profile</h1>
            <p>Ubah username atau password Anda.</p>
        </div>

        <?php
        // Blok notifikasi yang sudah aman
        if (isset($_SESSION['notification'])) {
            $notification_type = isset($_SESSION['notification_type']) ? $_SESSION['notification_type'] : 'success';
            echo '<div class="notification ' . htmlspecialchars($notification_type) . '">' . htmlspecialchars($_SESSION['notification']) . '</div>';
            unset($_SESSION['notification']);
            unset($_SESSION['notification_type']);
        }
        ?>

        <div class="card">
            <h2>Formulir Update Profile</h2>
            <form action="profile.php" method="POST" class="dashboard-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                </div>
                <hr style="border-color: var(--medium-green); margin: 20px 0;">
                <p style="color: #ccc;">Kosongkan password jika Anda tidak ingin mengubahnya.</p>
                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Masukkan password baru">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Ketik ulang password baru">
                </div>
                <button type="submit" class="btn">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>