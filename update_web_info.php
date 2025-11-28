<?php
require_once '../includes/db_connect.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Proses Form saat admin menekan tombol Simpan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $jumlah_rw = (int)$_POST['jumlah_rw'];
    $jumlah_rt = (int)$_POST['jumlah_rt'];
    // Ambil data baru: luas wilayah dan jumlah penduduk
    $luas_wilayah = $_POST['luas_wilayah'];
    $jumlah_penduduk = (int)$_POST['jumlah_penduduk'];

    if (!empty($judul) && !empty($deskripsi)) {
        // Perbarui query UPDATE untuk menyertakan semua kolom
        $stmt = $db->prepare("
            UPDATE web_info 
            SET judul = ?, deskripsi = ?, jumlah_rw = ?, jumlah_rt = ?, luas_wilayah = ?, jumlah_penduduk = ? 
            WHERE id_info = 1
        ");
        $stmt->execute([$judul, $deskripsi, $jumlah_rw, $jumlah_rt, $luas_wilayah, $jumlah_penduduk]);

        $_SESSION['notification'] = "Informasi website berhasil diperbarui!";
        $_SESSION['notification_type'] = "success";
    } else {
        $_SESSION['notification'] = "Judul dan Deskripsi tidak boleh kosong!";
        $_SESSION['notification_type'] = "error";
    }
    header("Location: update_web_info.php");
    exit();
}

// Ambil data terkini dari database untuk ditampilkan di form
$stmt = $db->query("SELECT * FROM web_info WHERE id_info = 1");
$info = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Informasi Website - SISTEM LAMPID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="main-wrapper">
    <?php include '../includes/header.php'; ?>

    <div class="content">
        <div class="content-header">
            <h1>Update Informasi Website</h1>
            <p>Ubah data yang tampil di halaman utama.</p>
        </div>

        <?php
        if (isset($_SESSION['notification'])) {
            $notification_type = isset($_SESSION['notification_type']) ? $_SESSION['notification_type'] : 'success';
            echo '<div class="notification ' . htmlspecialchars($notification_type) . '">' . htmlspecialchars($_SESSION['notification']) . '</div>';
            unset($_SESSION['notification']);
            unset($_SESSION['notification_type']);
        }
        ?>

        <div class="card">
            <h2>Form Informasi Website</h2>
            <form action="update_web_info.php" method="POST" class="dashboard-form">
                <div class="form-group">
                    <label for="judul">Judul Website</label>
                    <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($info['judul']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi Singkat</label>
                    <textarea id="deskripsi" name="deskripsi" rows="5" required><?php echo htmlspecialchars($info['deskripsi']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="jumlah_rw">Jumlah RW</label>
                    <input type="number" id="jumlah_rw" name="jumlah_rw" value="<?php echo htmlspecialchars($info['jumlah_rw']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="jumlah_rt">Jumlah RT</label>
                    <input type="number" id="jumlah_rt" name="jumlah_rt" value="<?php echo htmlspecialchars($info['jumlah_rt']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="luas_wilayah">Luas Wilayah (contoh: 45,31 km²)</label>
                    <input type="text" id="luas_wilayah" name="luas_wilayah" value="<?php echo htmlspecialchars($info['luas_wilayah']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="jumlah_penduduk">Jumlah Penduduk</label>
                    <input type="number" id="jumlah_penduduk" name="jumlah_penduduk" value="<?php echo htmlspecialchars($info['jumlah_penduduk']); ?>" required>
                </div>
                <button type="submit" class="btn" style="width: auto; padding: 12px 30px;">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>