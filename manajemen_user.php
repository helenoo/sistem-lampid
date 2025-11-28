<?php
require_once '../includes/db_connect.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Logika untuk menangani form (tambah atau hapus user)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cek aksi apa yang dilakukan (misal: 'tambah' atau 'hapus')
    if (isset($_POST['action'])) {

        // Aksi: Menambah user baru
        if ($_POST['action'] == 'tambah') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $id_desa = $_POST['id_desa'];
            $role = 'desa'; // Role sudah pasti 'desa'

            // Validasi dasar
            if (empty($username) || empty($password) || empty($id_desa)) {
                $_SESSION['notification'] = "Semua field wajib diisi!";
                $_SESSION['notification_type'] = "error";
            } else {
                // Cek apakah username sudah ada
                $stmt_check = $db->prepare("SELECT id_user FROM users WHERE username = ?");
                $stmt_check->execute([$username]);

                if ($stmt_check->rowCount() > 0) {
                    $_SESSION['notification'] = "Username '{$username}' sudah digunakan. Silakan pilih username lain.";
                    $_SESSION['notification_type'] = "error";
                } else {
                    // Hash password sebelum disimpan
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Masukkan ke database
                    $stmt_insert = $db->prepare("INSERT INTO users (id_desa, username, password, role) VALUES (?, ?, ?, ?)");
                    if ($stmt_insert->execute([$id_desa, $username, $hashedPassword, $role])) {
                        $_SESSION['notification'] = "Update user berhasil.";
                        $_SESSION['notification_type'] = "success";
                    } else {
                        $_SESSION['notification'] = "Gagal menambahkan user baru.";
                        $_SESSION['notification_type'] = "error";
                    }
                }
            }
        }

        // Aksi: Menghapus user
        if ($_POST['action'] == 'hapus') {
            $id_user_to_delete = $_POST['id_user'];
            $stmt_delete = $db->prepare("DELETE FROM users WHERE id_user = ? AND role = 'desa'");
            if ($stmt_delete->execute([$id_user_to_delete])) {
                $_SESSION['notification'] = "User berhasil dihapus.";
                $_SESSION['notification_type'] = "success";
            } else {
                $_SESSION['notification'] = "Gagal menghapus user.";
                $_SESSION['notification_type'] = "error";
            }
        }
    }
    // Redirect untuk menghindari re-submit form
    header("Location: manajemen_user.php");
    exit();
}

// Ambil data untuk ditampilkan
// 1. Daftar semua desa untuk dropdown form
$desa_list = $db->query("SELECT id_desa, nama_desa FROM desa ORDER BY nama_desa ASC")->fetchAll(PDO::FETCH_ASSOC);
// 2. Daftar semua user dengan role 'desa'
$user_list = $db->query("
    SELECT u.id_user, u.username, d.nama_desa 
    FROM users u
    JOIN desa d ON u.id_desa = d.id_desa
    WHERE u.role = 'desa'
    ORDER BY d.nama_desa ASC
")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User - SISTEM LAMPID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .btn-hapus {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .btn-hapus:hover { background-color: #c0392b; }
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include '../includes/header.php'; // Memuat sidebar ?>

    <div class="content">
        <div class="content-header">
            <h1>Manajemen User Desa</h1>
            <p>Tambah, lihat, atau hapus akun user untuk setiap desa.</p>
        </div>

        <?php
        // Tampilkan notifikasi jika ada
        if (isset($_SESSION['notification'])) {
        // Cek dulu apakah 'notification_type' ada, jika tidak, beri nilai default 'success'
            $notification_type = isset($_SESSION['notification_type']) ? $_SESSION['notification_type'] : 'success';
            echo '<div class="notification ' . htmlspecialchars($notification_type) . '">' . htmlspecialchars($_SESSION['notification']) . '</div>';
            unset($_SESSION['notification']);
            unset($_SESSION['notification_type']);
            }
        ?>

        <div class="card">
            <h2>Update User Desa</h2>
            <form action="manajemen_user.php" method="POST" class="dashboard-form">
                <input type="hidden" name="action" value="tambah">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="cth: user_sukamaju" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password default untuk user" required>
                </div>
                <div class="form-group">
                    <label for="id_desa">Desa</label>
                    <select id="id_desa" name="id_desa" required>
                        <option value="">-- Pilih Desa --</option>
                        <?php foreach ($desa_list as $desa): ?>
                            <option value="<?php echo $desa['id_desa']; ?>"><?php echo htmlspecialchars($desa['nama_desa']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Update User</button>
            </form>
        </div>

        <div class="card">
            <h2>Daftar User Desa</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama Desa</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($user_list) > 0): ?>
                        <?php $no = 1; foreach ($user_list as $user): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['nama_desa']); ?></td>
                            <td style="text-align: center;">
                                <form action="manajemen_user.php" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.');">
                                    <input type="hidden" name="action" value="hapus">
                                    <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">
                                    <button type="submit" class="btn-hapus">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Belum ada user desa yang terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>