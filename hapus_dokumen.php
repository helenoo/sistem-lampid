<?php
require_once '../includes/db_connect.php';

// Keamanan: Pastikan user login dan role-nya 'desa'
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

try {
    // Ambil path file untuk dihapus dari server
    $stmt_get = $db->prepare("SELECT path_file FROM dokumen_uploads WHERE id_upload = ? AND id_desa = ?");
    $stmt_get->execute([$id_upload, $id_desa]);
    $file_data = $stmt_get->fetch();

    if ($file_data) {
        // Hapus entri dari database terlebih dahulu
        $stmt_delete = $db->prepare("DELETE FROM dokumen_uploads WHERE id_upload = ? AND id_desa = ?");
        $stmt_delete->execute([$id_upload, $id_desa]);

        // Jika berhasil hapus dari DB, hapus file fisik dari server
        $path_file_fisik = $file_data['path_file'];
        if (file_exists($path_file_fisik)) {
            unlink($path_file_fisik);
        }
        
        $_SESSION['notification'] = "Dokumen berhasil dihapus.";
        $_SESSION['notification_type'] = "success";
    } else {
        $_SESSION['notification'] = "Dokumen tidak ditemukan atau Anda tidak berhak menghapusnya.";
        $_SESSION['notification_type'] = "error";
    }

} catch (PDOException $e) {
    $_SESSION['notification'] = "Gagal menghapus dokumen: " . $e->getMessage();
    $_SESSION['notification_type'] = "error";
}

header("Location: dashboard.php");
exit();
?>