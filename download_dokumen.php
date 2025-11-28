<?php
require_once '../includes/db_connect.php';

// Keamanan: Pastikan user adalah admin dan sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak. Anda harus login sebagai admin.");
}

// Validasi input: Pastikan id_upload ada dan merupakan angka
if (!isset($_GET['id_upload']) || !is_numeric($_GET['id_upload'])) {
    die("Permintaan tidak valid. ID file tidak ditemukan.");
}

$id_upload = (int)$_GET['id_upload'];

try {
    // Ambil informasi file dari database berdasarkan ID
    $stmt = $db->prepare("SELECT path_file, nama_file FROM dokumen_uploads WHERE id_upload = ?");
    $stmt->execute([$id_upload]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek apakah data file ditemukan di database
    if ($file) {
        $path_file_di_server = $file['path_file'];
        $nama_file_asli = $file['nama_file'];

        // Cek apakah file benar-benar ada di server
        if (file_exists($path_file_di_server)) {
            
            // Atur header untuk memaksa browser mengunduh file
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream'); // Tipe file umum untuk download
            header('Content-Disposition: attachment; filename="' . basename($nama_file_asli) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path_file_di_server));
            
            // Baca dan kirim isi file ke browser
            readfile($path_file_di_server);
            exit; // Hentikan eksekusi script setelah file dikirim

        } else {
            die("Error: File tidak ditemukan di server. Mungkin sudah terhapus.");
        }
    } else {
        die("Error: Data file tidak ditemukan di database.");
    }

} catch (PDOException $e) {
    die("Error database: " . $e->getMessage());
}

?>