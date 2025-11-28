<?php
require_once '../includes/db_connect.php';

// Keamanan: Cek sesi login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'desa') {
    header("Location: ../index.php");
    exit();
}

// Validasi input id_data dari URL
if (!isset($_GET['id_data']) || !is_numeric($_GET['id_data'])) {
    header("Location: dashboard.php");
    exit();
}

$id_data = (int)$_GET['id_data'];
$id_desa = $_SESSION['id_desa'];

// Proses form jika ada data yang dikirim (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil semua data dari form
    $penduduk_bulan_lalu = (int)$_POST['penduduk_bulan_lalu'];
    $lahir = (int)$_POST['lahir'];
    $mati = (int)$_POST['mati'];
    $pindah = (int)$_POST['pindah'];
    $datang = (int)$_POST['datang'];
    $penduduk_bulan_ini = (int)$_POST['penduduk_bulan_ini'];
    $kartu_keluarga = (int)$_POST['kartu_keluarga'];
    $ktp = (int)$_POST['ktp'];

    // Query UPDATE
    $sql = "
        UPDATE data_penduduk SET
            penduduk_bulan_lalu = ?, lahir = ?, mati = ?, pindah = ?, datang = ?,
            penduduk_bulan_ini = ?, kartu_keluarga = ?, ktp = ?
        WHERE id_data = ? AND id_desa = ?
    ";
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $penduduk_bulan_lalu, $lahir, $mati, $pindah, $datang,
            $penduduk_bulan_ini, $kartu_keluarga, $ktp,
            $id_data, $id_desa
        ]);

        $_SESSION['notification'] = "Data kependudukan berhasil diperbarui!";
        $_SESSION['notification_type'] = "success";
        header("Location: dashboard.php"); // Kembali ke dashboard setelah sukses
        exit();

    } catch (PDOException $e) {
        // Jika gagal, siapkan pesan error untuk ditampilkan
        $error_message = "Gagal memperbarui data: " . $e->getMessage();
    }
}

// Ambil data yang akan diedit dari database untuk ditampilkan di form
$stmt_get = $db->prepare("SELECT * FROM data_penduduk WHERE id_data = ? AND id_desa = ?");
$stmt_get->execute([$id_data, $id_desa]);
$data_penduduk = $stmt_get->fetch();

if (!$data_penduduk) {
    $_SESSION['notification'] = "Data tidak ditemukan atau Anda tidak memiliki hak akses.";
    $_SESSION['notification_type'] = "error";
    header("Location: dashboard.php");
    exit();
}
$periode = strftime('%B %Y', mktime(0, 0, 0, $data_penduduk['bulan'], 1, $data_penduduk['tahun']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Kependudukan - SISTEM LAMPID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="main-wrapper">
    <?php include '../includes/header.php'; ?>
    <div class="content">
        <div class="content-header">
            <h1>Edit Data Kependudukan</h1>
            <p>Anda sedang mengedit data untuk periode <strong><?php echo htmlspecialchars($periode); ?></strong>.</p>
        </div>

        <?php if(isset($error_message)): ?>
            <div class="notification error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Form Edit Data</h2>
            <form method="POST" class="dashboard-form">
                <div class="form-group"><label>Penduduk Bulan Lalu</label><input type="number" name="penduduk_bulan_lalu" value="<?php echo $data_penduduk['penduduk_bulan_lalu']; ?>" required></div>
                <div class="form-group"><label>Lahir</label><input type="number" name="lahir" value="<?php echo $data_penduduk['lahir']; ?>" required></div>
                <div class="form-group"><label>Mati</label><input type="number" name="mati" value="<?php echo $data_penduduk['mati']; ?>" required></div>
                <div class="form-group"><label>Pindah</label><input type="number" name="pindah" value="<?php echo $data_penduduk['pindah']; ?>" required></div>
                <div class="form-group"><label>Datang</label><input type="number" name="datang" value="<?php echo $data_penduduk['datang']; ?>" required></div>
                <div class="form-group"><label>Penduduk Bulan Ini</label><input type="number" name="penduduk_bulan_ini" value="<?php echo $data_penduduk['penduduk_bulan_ini']; ?>" required></div>
                <div class="form-group"><label>Kartu Keluarga</label><input type="number" name="kartu_keluarga" value="<?php echo $data_penduduk['kartu_keluarga']; ?>" required></div>
                <div class="form-group"><label>KTP</label><input type="number" name="ktp" value="<?php echo $data_penduduk['ktp']; ?>" required></div>
                <button type="submit" class="btn" style="width: auto;">Simpan Perubahan</button>
                <a href="dashboard.php" style="margin-left: 15px;">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>