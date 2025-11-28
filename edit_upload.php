<?php
require_once '../includes/db_connect.php';

// Keamanan: Cek sesi login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'desa') {
    header("Location: ../index.php");
    exit();
}

// Validasi input id_upload dari URL
if (!isset($_GET['id_upload']) || !is_numeric($_GET['id_upload'])) {
    header("Location: dashboard.php");
    exit();
}

$id_upload = (int)$_GET['id_upload'];
$id_desa = $_SESSION['id_desa'];

// Ambil data upload yang akan diedit dari database
$stmt = $db->prepare("SELECT * FROM dokumen_uploads WHERE id_upload = ? AND id_desa = ?");
$stmt->execute([$id_upload, $id_desa]);
$upload_data = $stmt->fetch();

// Jika data tidak ditemukan atau bukan milik desa ini, kembalikan ke dashboard
if (!$upload_data) {
    $_SESSION['notification'] = "Dokumen tidak ditemukan atau Anda tidak memiliki hak akses.";
    $_SESSION['notification_type'] = "error";
    header("Location: dashboard.php");
    exit();
}

// Menerjemahkan tipe dokumen
$jenis_dokumen_text = ($upload_data['tipe_dokumen'] == 'perkembangan_penduduk') ? 'Data Perkembangan Penduduk' : 'Laporan Kelompok Umur';
$periode = strftime('%B %Y', mktime(0, 0, 0, $upload_data['bulan'], 1, $upload_data['tahun']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Dokumen - SISTEM LAMPID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="main-wrapper">
    <?php include '../includes/header.php'; ?>
    <div class="content">
        <div class="content-header">
            <h1>Ganti Dokumen Laporan</h1>
            <p>Anda akan mengganti file laporan yang sudah diupload sebelumnya.</p>
        </div>

        <div class="card">
            <h2>Detail Dokumen Saat Ini</h2>
            <p><strong>Periode Laporan:</strong> <?php echo htmlspecialchars($periode); ?></p>
            <p><strong>Jenis Dokumen:</strong> <?php echo htmlspecialchars($jenis_dokumen_text); ?></p>
            <p><strong>Nama File Lama:</strong> <?php echo htmlspecialchars($upload_data['nama_file']); ?></p>
        </div>

        <div class="card">
            <h2>Upload File Pengganti</h2>
            <form action="proses_edit_upload.php" method="POST" enctype="multipart/form-data" class="dashboard-form">
                <input type="hidden" name="id_upload" value="<?php echo $id_upload; ?>">
                
                <div class="form-group">
                    <label for="dokumen_baru">Pilih File Baru</label>
                    <input type="file" id="dokumen_baru" name="dokumen_baru" required>
                </div>
                <button type="submit" class="btn" style="width: auto; padding: 12px 30px;">Simpan Perubahan</button>
                <a href="dashboard.php" style="margin-left: 15px; color: var(--text-secondary);">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>