<?php
require_once '../includes/db_connect.php';

// Keamanan: Pastikan user sudah login dan perannya adalah 'desa'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'desa') {
    // Jika tidak, tendang ke halaman login
    header("Location: ../index.php");
    exit();
}

// Pastikan request adalah metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Ambil semua data dari form
    $id_desa = $_SESSION['id_desa'];
    $bulan = (int)$_POST['bulan'];
    $tahun = (int)$_POST['tahun'];
    $penduduk_bulan_lalu = (int)$_POST['penduduk_bulan_lalu'];
    $lahir = (int)$_POST['lahir'];
    $mati = (int)$_POST['mati'];
    $pindah = (int)$_POST['pindah'];
    $datang = (int)$_POST['datang'];
    $penduduk_bulan_ini = (int)$_POST['penduduk_bulan_ini'];
    $kartu_keluarga = (int)$_POST['kartu_keluarga'];
    $ktp = (int)$_POST['ktp'];

    // 2. Siapkan query SQL
    // Menggunakan "INSERT ... ON DUPLICATE KEY UPDATE"
    // Jika data untuk desa, bulan, dan tahun tersebut belum ada, maka akan INSERT (membuat data baru).
    // Jika sudah ada, maka akan UPDATE (memperbarui data yang ada).
    $sql = "
        INSERT INTO data_penduduk 
            (id_desa, bulan, tahun, penduduk_bulan_lalu, lahir, mati, pindah, datang, penduduk_bulan_ini, kartu_keluarga, ktp)
        VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            penduduk_bulan_lalu = VALUES(penduduk_bulan_lalu),
            lahir = VALUES(lahir),
            mati = VALUES(mati),
            pindah = VALUES(pindah),
            datang = VALUES(datang),
            penduduk_bulan_ini = VALUES(penduduk_bulan_ini),
            kartu_keluarga = VALUES(kartu_keluarga),
            ktp = VALUES(ktp)
    ";

    try {
        // 3. Eksekusi query
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $id_desa, $bulan, $tahun, $penduduk_bulan_lalu, $lahir, $mati, $pindah, $datang, $penduduk_bulan_ini, $kartu_keluarga, $ktp
        ]);

        // 4. Beri notifikasi sukses
        $_SESSION['notification'] = "Data kependudukan untuk bulan " . strftime('%B', mktime(0,0,0,$bulan,10)) . " {$tahun} berhasil disimpan!";
        $_SESSION['notification_type'] = "success";

    } catch (PDOException $e) {
        // 5. Beri notifikasi gagal jika ada error
        $_SESSION['notification'] = "Terjadi kesalahan saat menyimpan data: " . $e->getMessage();
        $_SESSION['notification_type'] = "error";
    }

    // 6. Kembalikan user ke halaman dashboard
    header("Location: dashboard.php");
    exit();

} else {
    // Jika halaman diakses langsung tanpa POST, kembalikan ke dashboard
    header("Location: dashboard.php");
    exit();
}
?>