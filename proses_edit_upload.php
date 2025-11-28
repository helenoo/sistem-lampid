<?php
require_once '../includes/db_connect.php';

// Keamanan: Cek sesi login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'desa') {
    header("Location: ../index.php");
    exit();
}

// Pastikan request adalah metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validasi input
    if (!isset($_POST['id_upload']) || !is_numeric($_POST['id_upload']) || !isset($_FILES['dokumen_baru']) || $_FILES['dokumen_baru']['error'] == UPLOAD_ERR_NO_FILE) {
        $_SESSION['notification'] = "Permintaan tidak valid atau file baru belum dipilih.";
        $_SESSION['notification_type'] = "error";
        header("Location: dashboard.php");
        exit();
    }

    $id_upload = (int)$_POST['id_upload'];
    $id_desa = $_SESSION['id_desa'];
    $file_baru = $_FILES['dokumen_baru'];
    
    // Ambil path file LAMA untuk dihapus nanti
    $stmt_old = $db->prepare("SELECT path_file, bulan, tahun, tipe_dokumen FROM dokumen_uploads WHERE id_upload = ? AND id_desa = ?");
    $stmt_old->execute([$id_upload, $id_desa]);
    $old_data = $stmt_old->fetch();

    if (!$old_data) {
        $_SESSION['notification'] = "Dokumen lama tidak ditemukan atau Anda tidak berhak mengubahnya.";
        $_SESSION['notification_type'] = "error";
        header("Location: dashboard.php");
        exit();
    }

    // Proses upload file BARU
    $upload_dir = '../uploads/';
    $nama_file_asli_baru = basename($file_baru['name']);
    $ekstensi_file_baru = pathinfo($nama_file_asli_baru, PATHINFO_EXTENSION);
    $nama_file_unik_baru = $old_data['tahun'] . "-" . str_pad($old_data['bulan'], 2, '0', STR_PAD_LEFT) . "_" . $id_desa . "_" . $old_data['tipe_dokumen'] . "_" . time() . "." . $ekstensi_file_baru;
    $path_tujuan_baru = $upload_dir . $nama_file_unik_baru;

    if (move_uploaded_file($file_baru['tmp_name'], $path_tujuan_baru)) {
        // Jika file baru berhasil diupload, update database
        $sql_update = "UPDATE dokumen_uploads SET nama_file = ?, path_file = ?, tgl_upload = NOW() WHERE id_upload = ? AND id_desa = ?";
        
        try {
            $stmt_update = $db->prepare($sql_update);
            $stmt_update->execute([$nama_file_asli_baru, $path_tujuan_baru, $id_upload, $id_desa]);

            // Jika database berhasil diupdate, hapus file LAMA dari server
            if (file_exists($old_data['path_file'])) {
                unlink($old_data['path_file']);
            }
            
            $_SESSION['notification'] = "Dokumen berhasil diganti!";
            $_SESSION['notification_type'] = "success";

        } catch (PDOException $e) {
            // Jika gagal update DB, hapus file BARU yang sudah terlanjur diupload
            unlink($path_tujuan_baru);
            $_SESSION['notification'] = "Gagal memperbarui database: " . $e->getMessage();
            $_SESSION['notification_type'] = "error";
        }

    } else {
        $_SESSION['notification'] = "Gagal mengupload file baru.";
        $_SESSION['notification_type'] = "error";
    }

    header("Location: dashboard.php");
    exit();
}
?>