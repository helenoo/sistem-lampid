<?php
require_once '../includes/db_connect.php';

// Keamanan: Pastikan hanya user desa yang bisa mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'desa') {
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}

// Daftar file template yang tersedia untuk diunduh
// --- PERUBAHAN DIMULAI DI SINI ---
$templates = [
    [
        'display_name' => 'Data Perkembangan Penduduk (Excel)',
        'file_name' => 'DATA PERKEMBANGAN PENDUDUK.xlsx',
        'description' => 'Template Excel untuk melaporkan data lahir, mati, pindah, dan datang setiap bulannya.'
    ],
    [
        'display_name' => 'Laporan Penduduk Menurut Kelompok Umur (Excel)',
        'file_name' => 'LAPORAN BULANAN PENDUDUK MENURUT KELOMPOK UMUR.xlsx',
        'description' => 'Template Excel untuk rekapitulasi jumlah penduduk berdasarkan kelompok usia.'
    ],
    [
        'display_name' => 'Laporan Penduduk Menurut Kelompok Umur (Word)',
        'file_name' => 'LAPORAN BULANAN PENDUDUK MENURUT KELOMPOK UMUR.docx',
        'description' => 'Template Word (opsional) untuk laporan rekapitulasi penduduk berdasarkan kelompok usia.'
    ]
];
// --- PERUBAHAN SELESAI DI SINI ---

// Path ke direktori template
$template_path = '../assets/templates/';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Download Template - SISTEM LAMPID</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .btn-download {
            display: inline-block;
            background-color: var(--accent-green);
            color: var(--dark-bg);
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-download:hover {
            background-color: #7cdcb0;
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include '../includes/header.php'; // Memuat sidebar ?>

    <div class="content">
        <div class="content-header">
            <h1>Download Template Dokumen</h1>
            <p>Silakan unduh template di bawah ini untuk mempersiapkan laporan bulanan Anda.</p>
        </div>

        <div class="card">
            <h2>Daftar Template</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama Dokumen</th>
                        <th>Deskripsi</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $template): ?>
                        <?php $file_full_path = $template_path . $template['file_name']; ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($template['display_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($template['description']); ?></td>
                            <td style="text-align: center;">
                                <?php if (file_exists($file_full_path)): ?>
                                    <a href="<?php echo $file_full_path; ?>" class="btn-download" download>
                                        Unduh
                                    </a>
                                <?php else: ?>
                                    <span style="color: #e74c3c;">File tidak ditemukan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>