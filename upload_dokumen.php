<?php
require_once '../includes/db_connect.php';

// Keamanan: Pastikan user login dan role-nya 'desa'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'desa') {
    header("Location: ../index.php");
    exit();
}

// Pastikan request adalah metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form
    $id_desa = $_SESSION['id_desa'];
    $bulan = (int)$_POST['bulan'];
    $tahun = (int)$_POST['tahun'];
    $tipe_dokumen = $_POST['tipe_dokumen'];
    $file = $_FILES['dokumen_laporan'];

    // Validasi dasar
    if (empty($bulan) || empty($tahun) || empty($tipe_dokumen) || $file['error'] == UPLOAD_ERR_NO_FILE) {
        $_SESSION['notification'] = "Semua field wajib diisi dan file harus dipilih.";
        $_SESSION['notification_type'] = "error";
        header("Location: dashboard.php");
        exit();
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['notification'] = "Terjadi error saat mengupload file.";
        $_SESSION['notification_type'] = "error";
        header("Location: dashboard.php");
        exit();
    }

    // ======================================================================
    // LOGIKA BARU: Validasi tipe file berdasarkan jenis dokumen
    // ======================================================================
    $nama_file_asli = basename($file['name']);
    $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
    $valid = false;

    if ($tipe_dokumen == 'perkembangan_penduduk') {
        if ($ekstensi_file != 'xlsx') {
            $_SESSION['notification'] = "Gagal! Untuk jenis 'Data Perkembangan Penduduk', file harus berformat .xlsx";
        } else {
            $valid = true;
        }
    } elseif ($tipe_dokumen == 'kelompok_umur') {
        if ($ekstensi_file != 'docx') {
            $_SESSION['notification'] = "Gagal! Untuk jenis 'Laporan Kelompok Umur', file harus berformat .docx";
        } else {
            $valid = true;
        }
    }

    // Jika validasi gagal, hentikan proses
    if (!$valid) {
        $_SESSION['notification_type'] = "error";
        header("Location: dashboard.php");
        exit();
    }
    // ======================================================================
    // Akhir dari logika validasi tipe file
    // ======================================================================

    // Cek duplikasi data (logika ini tetap ada)
    $stmt_check = $db->prepare("SELECT id_upload FROM dokumen_uploads WHERE id_desa = ? AND bulan = ? AND tahun = ? AND tipe_dokumen = ?");
    $stmt_check->execute([$id_desa, $bulan, $tahun, $tipe_dokumen]);
    if ($stmt_check->rowCount() > 0) {
        $nama_bulan = strftime('%B', mktime(0, 0, 0, $bulan, 10));
        $_SESSION['notification'] = "Gagal! Dokumen untuk periode {$nama_bulan} {$tahun} dengan jenis yang sama sudah pernah diupload.";
        $_SESSION['notification_type'] = "error";
        header("Location: dashboard.php");
        exit();
    }
    
    // Proses upload jika semua validasi lolos
    $upload_dir = '../uploads/';
    $nama_file_unik = $tahun . "-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "_" . $id_desa . "_" . $tipe_dokumen . "_" . time() . "." . $ekstensi_file;
    $path_tujuan = $upload_dir . $nama_file_unik;

    if (move_uploaded_file($file['tmp_name'], $path_tujuan)) {
        // Simpan informasi file ke database
        $sql = "INSERT INTO dokumen_uploads (id_desa, bulan, tahun, tipe_dokumen, nama_file, path_file) VALUES (?, ?, ?, ?, ?, ?)";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_desa, $bulan, $tahun, $tipe_dokumen, $nama_file_asli, $path_tujuan]);
            $_SESSION['notification'] = "Dokumen berhasil diupload!";
            $_SESSION['notification_type'] = "success";
        } catch (PDOException $e) {
            unlink($path_tujuan);
            $_SESSION['notification'] = "Gagal menyimpan informasi file: " . $e->getMessage();
            $_SESSION['notification_type'] = "error";
        }
    } else {
        $_SESSION['notification'] = "Gagal memindahkan file yang diupload.";
        $_SESSION['notification_type'] = "error";
    }

    header("Location: dashboard.php");
    exit();
}
?>